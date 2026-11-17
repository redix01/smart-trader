<?php

namespace Tests\Feature\Admin;

use App\Mail\DeclineDepositMail;
use App\Mail\DepositApprovalMail;
use App\Mail\RejectWithdrawalMail;
use App\Models\Deposit;
use App\Models\PaymentMethod;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Mail;

/** Money in and out: the wallet an admin credits/refunds must be the one the user picked, exactly once. */
class AdminDepositWithdrawalTest extends AdminTestCase
{
    private function deposit($user, string $wallet, float $amount = 100): Deposit
    {
        $pm = PaymentMethod::create(['crypto_type' => 'bitcoin', 'address' => 'bc1q', 'is_active' => true]);

        return Deposit::create(['user_id' => $user->id, 'amount' => $amount, 'payment_method_id' => $pm->id, 'wallet_type' => $wallet, 'status' => 0, 'proof' => 'deposits/p.png']);
    }

    public function test_each_deposit_wallet_credits_its_own_balance(): void
    {
        Mail::fake();
        $user = $this->member();

        foreach (['balance' => 'balance', 'trading' => 'trading_balance', 'holding' => 'holding_balance', 'staking' => 'staking_balance'] as $wallet => $column) {
            $d = $this->deposit($user, $wallet, 10);
            $this->asAdmin()->post(route('admin.deposit.approve', $d->id))->assertSessionHas('success');
            $this->assertEqualsWithDelta(10, (float) $user->fresh()->$column, 0.001, "$wallet deposit must credit $column");
        }

        // The Trading wallet deposit used to land in the main balance: it must have stayed at 10, not 20.
        $this->assertEqualsWithDelta(10, (float) $user->fresh()->balance, 0.001);
        Mail::assertSent(DepositApprovalMail::class, 4);
    }

    public function test_approving_twice_credits_once_and_notifies_the_user(): void
    {
        Mail::fake();
        $user = $this->member();
        $d = $this->deposit($user, 'balance', 250);

        $this->asAdmin()->post(route('admin.deposit.approve', $d->id));
        $this->asAdmin()->post(route('admin.deposit.approve', $d->id))->assertSessionHas('error');

        $this->assertEqualsWithDelta(250, (float) $user->fresh()->balance, 0.001);
        $this->assertSame(1, $d->fresh()->status);
        $this->assertDatabaseHas('user_notifications', ['user_id' => $user->id, 'type' => 'deposit_approved']);
    }

    public function test_decline_does_not_credit_notifies_user_and_cannot_be_approved_after(): void
    {
        Mail::fake();
        $user = $this->member();
        $d = $this->deposit($user, 'trading', 80);

        $this->asAdmin()->post(route('admin.deposit.decline', $d->id))->assertSessionHas('success');
        $this->asAdmin()->post(route('admin.deposit.approve', $d->id))->assertSessionHas('error');

        $this->assertSame(2, $d->fresh()->status);
        $this->assertEqualsWithDelta(0, (float) $user->fresh()->trading_balance, 0.001);
        $this->assertDatabaseHas('user_notifications', ['user_id' => $user->id, 'type' => 'deposit_declined']);
        Mail::assertSent(DeclineDepositMail::class);
        $this->assertStringContainsString('Declined', $d->fresh()->adminStatus());
    }

    public function test_deposit_pages_show_the_users_own_currency_and_filter_by_user(): void
    {
        $eur = $this->member(['currency' => 'EUR']);
        $other = $this->member();
        $this->deposit($eur, 'balance', 55);
        $this->deposit($other, 'balance', 77);

        $this->asAdmin()->get(route('admin.transactions.deposits'))->assertOk()->assertSee('€55.00', false)->assertSee('$77.00', false);
        $this->asAdmin()->get(route('admin.transactions.deposits', ['user' => $eur->id]))->assertOk()
            ->assertSee('€55.00', false)->assertDontSee('$77.00', false)->assertSee($eur->email);
    }

    private function withdrawal($user, string $from = 'balance', float $amount = 40): Withdrawal
    {
        return Withdrawal::create(['user_id' => $user->id, 'amount' => $amount, 'payment_method' => 'crypto', 'from_account' => $from, 'wallet' => 'BTC', 'address' => 'bc1q', 'status' => 0]);
    }

    public function test_approving_a_withdrawal_only_marks_it_paid(): void
    {
        Mail::fake();
        $user = $this->member(['balance' => 60]); // 100 requested 40 already deducted
        $w = $this->withdrawal($user);

        $this->asAdmin()->post(route('admin.transactions.withdrawals.approve', $w->id))->assertSessionHas('success');
        $this->asAdmin()->post(route('admin.transactions.withdrawals.approve', $w->id))->assertSessionHas('error');

        $this->assertSame(1, $w->fresh()->status);
        $this->assertEqualsWithDelta(60, (float) $user->fresh()->balance, 0.001);
        $this->assertDatabaseHas('user_notifications', ['user_id' => $user->id, 'type' => 'withdrawal_approved']);
    }

    public function test_rejecting_refunds_the_wallet_it_came_from_once(): void
    {
        Mail::fake();
        foreach (['balance', 'trading_balance', 'mining_balance'] as $from) {
            $user = $this->member();
            $w = $this->withdrawal($user, $from, 40);

            $this->asAdmin()->post(route('admin.transactions.withdrawals.reject', $w->id))->assertSessionHas('success');
            $this->asAdmin()->post(route('admin.transactions.withdrawals.reject', $w->id))->assertSessionHas('error');

            $this->assertEqualsWithDelta(40, (float) $user->fresh()->$from, 0.001, "refund must go to $from, once");
            $this->assertSame(2, $w->fresh()->status);
            $this->assertDatabaseHas('user_notifications', ['user_id' => $user->id, 'type' => 'withdrawal_rejected']);
        }
        Mail::assertSent(RejectWithdrawalMail::class, 3);
    }

    public function test_a_withdrawal_with_an_unexpected_source_account_refunds_the_main_balance_only(): void
    {
        Mail::fake();
        $user = $this->member(['staking_balance' => 5]);
        $w = $this->withdrawal($user, 'profit', 30);

        $this->asAdmin()->post(route('admin.transactions.withdrawals.reject', $w->id));

        $this->assertEqualsWithDelta(30, (float) $user->fresh()->balance, 0.001);
        $this->assertEqualsWithDelta(5, (float) $user->fresh()->staking_balance, 0.001);
    }

    public function test_withdrawal_page_filters_by_user_and_shows_source_wallet(): void
    {
        $a = $this->member();
        $b = $this->member();
        $this->withdrawal($a, 'trading_balance', 12);
        $this->withdrawal($b, 'balance', 34);

        $this->asAdmin()->get(route('admin.transactions.withdrawals', ['user' => $a->id]))->assertOk()
            ->assertSee('$12.00', false)->assertDontSee('$34.00', false)->assertSee('Trading Balance');
    }

    public function test_non_admins_cannot_touch_deposits_or_withdrawals(): void
    {
        $user = $this->member();
        $d = $this->deposit($user, 'balance');

        $this->actingAs($user)->post(route('admin.deposit.approve', $d->id))->assertRedirect('/');
        $this->assertSame(0, $d->fresh()->status);
    }
}
