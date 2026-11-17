<?php

namespace Tests\Feature\Admin;

use App\Models\User;

class AdminUsersTest extends AdminTestCase
{
    public function test_user_list_and_detail_pages_render_with_the_users_currency(): void
    {
        $user = $this->member(['currency' => 'GBP', 'balance' => 1234.5]);

        $this->asAdmin()->get(route('admin.user.index'))->assertOk()->assertSee($user->email)->assertSee('£1,234.50', false);
        $this->asAdmin()->get(route('admin.user.show', $user->id))->assertOk()->assertSee('£1,234.50', false);
    }

    public function test_balance_adjustments_hit_the_chosen_wallet_and_never_go_negative(): void
    {
        $user = $this->member(['trading_balance' => 100]);

        $this->asAdmin()->post(route('admin.updateBalance', $user->id), ['wallet_type' => 'trading_balance', 'amount' => 50, 'action_type' => 'add'])->assertSessionHas('success');
        $this->assertEqualsWithDelta(150, (float) $user->fresh()->trading_balance, 0.001);

        $this->asAdmin()->post(route('admin.updateBalance', $user->id), ['wallet_type' => 'trading_balance', 'amount' => 500, 'action_type' => 'remove'])->assertSessionHas('error');
        $this->assertEqualsWithDelta(150, (float) $user->fresh()->trading_balance, 0.001);

        $this->asAdmin()->post(route('admin.updateBalance', $user->id), ['wallet_type' => 'trading_balance', 'amount' => 20, 'action_type' => 'remove']);
        $this->asAdmin()->post(route('admin.updateBalance', $user->id), ['wallet_type' => 'staking_balance', 'amount' => 9, 'action_type' => 'reset']);
        $this->assertEqualsWithDelta(130, (float) $user->fresh()->trading_balance, 0.001);
        $this->assertEqualsWithDelta(9, (float) $user->fresh()->staking_balance, 0.001);
        $this->assertEqualsWithDelta(0, (float) $user->fresh()->balance, 0.001);
    }

    public function test_balance_adjustment_rejects_unknown_wallets(): void
    {
        $user = $this->member();

        $this->asAdmin()->post(route('admin.updateBalance', $user->id), ['wallet_type' => 'password', 'amount' => 1, 'action_type' => 'add'])->assertSessionHasErrors('wallet_type');
    }

    public function test_verify_and_unverify_email(): void
    {
        $user = $this->member(['email_verified_at' => null]);

        $this->asAdmin()->post(route('admin.user.verifyEmail', $user->id))->assertSessionHas('success');
        $this->assertNotNull($user->fresh()->email_verified_at);

        $this->asAdmin()->post(route('admin.user.unverifyEmail', $user->id));
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_deactivated_users_are_logged_out_and_cannot_come_back(): void
    {
        $user = $this->member();

        $this->asAdmin()->post(route('admin.updateStatus', $user->id), ['status' => 'inactive'])->assertSessionHas('success');
        $this->assertSame('inactive', $user->fresh()->status);

        $this->actingAs($user->fresh())->get('/user/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();

        $this->asAdmin()->post(route('admin.updateStatus', $user->id), ['status' => 'active']);
        $this->actingAs($user->fresh())->get('/user/dashboard')->assertOk();
    }

    public function test_admin_cannot_deactivate_or_delete_admin_accounts(): void
    {
        $other = User::factory()->create(['role' => 'admin']);

        $this->asAdmin()->post(route('admin.updateStatus', $this->admin->id), ['status' => 'inactive'])->assertSessionHas('error');
        $this->asAdmin()->delete(route('admin.deleteUser', $other->id))->assertSessionHas('error');
        $this->asAdmin()->delete(route('admin.deleteUser', $this->admin->id))->assertSessionHas('error');

        $this->assertNotNull(User::find($other->id));
        $this->assertSame('active', $this->admin->fresh()->status);
    }

    public function test_deleting_a_regular_user_works_and_returns_to_the_list(): void
    {
        $user = $this->member();

        $this->asAdmin()->delete(route('admin.deleteUser', $user->id))->assertRedirect(route('admin.user.index'));
        $this->assertNull(User::find($user->id));
    }

    public function test_user_pages_are_admin_only(): void
    {
        $user = $this->member();

        $this->actingAs($user)->get(route('admin.user.index'))->assertRedirect('/');
        $this->actingAs($user)->post(route('admin.updateBalance', $user->id), ['wallet_type' => 'balance', 'amount' => 999, 'action_type' => 'add'])->assertRedirect('/');
        $this->assertEqualsWithDelta(0, (float) $user->fresh()->balance, 0.001);
    }
}
