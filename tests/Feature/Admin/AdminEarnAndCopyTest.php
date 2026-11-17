<?php

namespace Tests\Feature\Admin;

use App\Models\CopiedTrade;
use App\Models\CopyTrader;
use App\Models\Plan;
use App\Models\UserMining;
use App\Models\UserStaking;

/** Mining and copy trading: admin actions must move money exactly like the user's own buttons, once. */
class AdminEarnAndCopyTest extends AdminTestCase
{
    private function miningPlan(): Plan
    {
        return Plan::create(['name' => 'Mine', 'type' => 'mining', 'price' => 0, 'currency' => 'USD', 'is_active' => true, 'min_funding' => 50, 'max_funding' => 5000, 'mining_duration' => 30]);
    }

    private function mining($user, array $attrs = []): UserMining
    {
        return UserMining::create($attrs + [
            'user_id' => $user->id, 'plan_id' => $this->miningPlan()->id, 'amount_invested' => 500, 'currency' => 'USD',
            'start_date' => now(), 'end_date' => now()->addDays(30), 'status' => 'active', 'total_mined' => 0, 'total_withdrawn' => 0,
        ]);
    }

    public function test_editing_mined_totals_credits_nothing_until_the_user_withdraws(): void
    {
        $user = $this->member(['balance' => 0]);
        $m = $this->mining($user);

        $this->asAdmin()->put(route('admin.mining.update', $m->id), ['total_mined' => 80, 'current_value' => 580, 'status' => 'active'])->assertSessionHas('success');
        $this->assertEqualsWithDelta(0, (float) $user->fresh()->balance, 0.001);

        // The user withdraws what the admin booked: paid once, to the main balance.
        $this->actingAs($user)->post(route('user.mining.withdraw', $m))->assertSessionHas('success');
        $this->assertEqualsWithDelta(80, (float) $user->fresh()->balance, 0.001);
    }

    public function test_total_mined_cannot_drop_below_what_was_already_withdrawn(): void
    {
        $user = $this->member();
        $m = $this->mining($user, ['total_mined' => 80, 'total_withdrawn' => 60]);

        $this->asAdmin()->put(route('admin.mining.update', $m->id), ['total_mined' => 10, 'current_value' => 0, 'status' => 'active'])->assertSessionHas('error');
        $this->assertEqualsWithDelta(80, (float) $m->fresh()->total_mined, 0.001);
    }

    public function test_cancelling_mining_returns_principal_and_unpaid_earnings_once(): void
    {
        $user = $this->member(['balance' => 0]);
        $m = $this->mining($user, ['total_mined' => 100, 'total_withdrawn' => 30]);

        $this->asAdmin()->post(route('admin.mining.cancel', $m->id))->assertSessionHas('success');
        $this->assertEqualsWithDelta(570, (float) $user->fresh()->balance, 0.001); // 500 principal + 70 unpaid
        $this->assertSame('cancelled', $m->fresh()->status);

        $this->asAdmin()->post(route('admin.mining.cancel', $m->id))->assertSessionHas('error');
        $this->assertEqualsWithDelta(570, (float) $user->fresh()->balance, 0.001);
        $this->assertDatabaseHas('user_notifications', ['user_id' => $user->id, 'type' => 'mining_cancelled']);
    }

    public function test_deleting_live_mining_refunds_but_deleting_a_cancelled_one_does_not_refund_again(): void
    {
        $user = $this->member(['balance' => 0]);
        $live = $this->mining($user);
        $done = $this->mining($user, ['status' => 'cancelled']);

        $this->asAdmin()->delete(route('admin.mining.destroy', $live->id));
        $this->assertEqualsWithDelta(500, (float) $user->fresh()->balance, 0.001);

        $this->asAdmin()->delete(route('admin.mining.destroy', $done->id));
        $this->assertEqualsWithDelta(500, (float) $user->fresh()->balance, 0.001);
        $this->assertSame(0, UserMining::count());
    }

    private function copy($user, int $status = 1, float $amount = 200): CopiedTrade
    {
        $trader = CopyTrader::firstOrCreate(['name' => 'Ace'], ['amount' => 100, 'win_rate' => 80, 'profit_share' => 10, 'win' => 5, 'loss' => 1, 'status' => 'active']);

        return CopiedTrade::create(['user_id' => $user->id, 'copy_trader_id' => $trader->id, 'amount' => $amount, 'status' => $status, 'trade_count' => 0, 'win' => 0, 'loss' => 0, 'pnl' => 0]);
    }

    public function test_stopping_a_copy_returns_the_stake_like_the_users_own_stop_button(): void
    {
        $user = $this->member(['balance' => 0]);
        $c = $this->copy($user);

        $this->asAdmin()->post(route('admin.copied-trades.deactivate', $c->id))->assertSessionHas('success');
        $this->assertEqualsWithDelta(200, (float) $user->fresh()->balance, 0.001);
        $this->assertNotNull($c->fresh()->stopped_at);

        $this->asAdmin()->post(route('admin.copied-trades.deactivate', $c->id))->assertSessionHas('error');
        $this->assertEqualsWithDelta(200, (float) $user->fresh()->balance, 0.001);
    }

    public function test_restarting_a_copy_takes_the_stake_back_out_so_stop_start_cannot_mint_money(): void
    {
        $user = $this->member(['balance' => 200]);
        $c = $this->copy($user, 0);

        $this->asAdmin()->post(route('admin.copied-trades.activate', $c->id))->assertSessionHas('success');
        $this->assertEqualsWithDelta(0, (float) $user->fresh()->balance, 0.001);
        $this->assertSame(1, (int) $c->fresh()->status);

        $this->asAdmin()->post(route('admin.copied-trades.deactivate', $c->id));
        $this->asAdmin()->post(route('admin.copied-trades.activate', $c->id));
        $this->asAdmin()->post(route('admin.copied-trades.deactivate', $c->id));
        $this->assertEqualsWithDelta(200, (float) $user->fresh()->balance, 0.001); // still 200, never more
    }

    public function test_restart_is_refused_when_the_user_cannot_fund_it(): void
    {
        $user = $this->member(['balance' => 50]);
        $c = $this->copy($user, 0);

        $this->asAdmin()->post(route('admin.copied-trades.activate', $c->id))->assertSessionHas('error');
        $this->assertSame(0, (int) $c->fresh()->status);
        $this->assertEqualsWithDelta(50, (float) $user->fresh()->balance, 0.001);
    }

    public function test_editing_copy_pnl_credits_only_the_difference_and_delete_refunds_active_stake(): void
    {
        $user = $this->member(['balance' => 0]);
        $c = $this->copy($user);

        $payload = ['trade_count' => 4, 'win' => 3, 'loss' => 1];
        $this->asAdmin()->post(route('admin.copied-trades.edit-pnl', $c->id), $payload + ['pnl' => 25])->assertSessionHas('success');
        $this->asAdmin()->post(route('admin.copied-trades.edit-pnl', $c->id), $payload + ['pnl' => 40]);
        $this->assertEqualsWithDelta(40, (float) $user->fresh()->balance, 0.001);

        $this->asAdmin()->delete(route('admin.copied-trades.destroy', $c->id));
        $this->assertEqualsWithDelta(240, (float) $user->fresh()->balance, 0.001);
        $this->assertSame(0, CopiedTrade::count());
    }

    public function test_plans_with_user_money_in_them_cannot_be_deleted_only_deactivated(): void
    {
        $user = $this->member();
        $plan = $this->miningPlan();
        UserMining::create(['user_id' => $user->id, 'plan_id' => $plan->id, 'amount_invested' => 100, 'currency' => 'USD', 'start_date' => now(), 'status' => 'active']);
        $unused = Plan::create(['name' => 'Unused', 'type' => 'staking', 'price' => 0, 'currency' => 'USD', 'is_active' => true]);

        $this->asAdmin()->delete(route('admin.plans.destroy', $plan))->assertSessionHas('error');
        $this->assertSame(1, UserMining::count());

        $this->asAdmin()->post(route('admin.plans.toggle-status', $plan))->assertSessionHas('success');
        $this->assertFalse((bool) $plan->fresh()->is_active);

        $this->asAdmin()->delete(route('admin.plans.destroy', $unused))->assertSessionHas('success');
        $this->assertNull(Plan::find($unused->id));
    }
}
