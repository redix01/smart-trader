<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserMining;
use App\Models\UserStaking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Staking and mining move real money: funded from their wallet, paid out to the main balance, never twice. */
class EarnPayoutsTest extends TestCase
{
    use RefreshDatabase;

    private function user(array $attrs = []): User
    {
        return User::factory()->create($attrs + ['currency' => 'USD', 'balance' => 0, 'staking_balance' => 1000, 'mining_balance' => 1000]);
    }

    private function stakingPlan(): Plan
    {
        return Plan::create([
            'name' => 'Stake Test', 'type' => 'staking', 'price' => 0, 'currency' => 'USD', 'is_active' => true,
            'apy_rate' => 36.5, 'minimum_amount' => 100, 'lock_period' => 0, 'staking_duration' => 30, 'staking_currency' => 'USD',
        ]);
    }

    private function miningPlan(): Plan
    {
        return Plan::create([
            'name' => 'Mine Test', 'type' => 'mining', 'price' => 0, 'currency' => 'USD', 'is_active' => true,
            'min_funding' => 50, 'max_funding' => 5000, 'mining_duration' => 30,
        ]);
    }

    public function test_stake_debits_staking_balance_and_is_refused_when_short(): void
    {
        $user = $this->user();
        $plan = $this->stakingPlan();

        $this->actingAs($user)->post(route('user.staking.store'), ['plan_id' => $plan->id, 'amount_staked' => 400])->assertRedirect();
        $this->assertEqualsWithDelta(600, (float) $user->fresh()->staking_balance, 0.001);
        $this->assertSame(1, UserStaking::count());

        $this->actingAs($user)->post(route('user.staking.store'), ['plan_id' => $plan->id, 'amount_staked' => 700])
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Insufficient staking balance'));
        $this->assertSame(1, UserStaking::count());
        $this->assertEqualsWithDelta(600, (float) $user->fresh()->staking_balance, 0.001);
    }

    public function test_rewards_withdraw_pays_unpaid_once_and_stops_at_end_date(): void
    {
        $user = $this->user();
        $this->actingAs($user)->post(route('user.staking.store'), ['plan_id' => $this->stakingPlan()->id, 'amount_staked' => 1000]);
        $staking = UserStaking::first();

        // 10 days at 36.5% APY on 1000 = 10 × 1.00 = 10.00
        $staking->update(['start_date' => now()->subDays(10)]);
        $this->actingAs($user)->post(route('user.staking.withdraw', $staking))->assertSessionHas('success');
        $this->assertEqualsWithDelta(10, (float) $user->fresh()->balance, 0.01);

        // Immediately again: nothing more owed.
        $this->actingAs($user)->post(route('user.staking.withdraw', $staking))->assertSessionHas('error');
        $this->assertEqualsWithDelta(10, (float) $user->fresh()->balance, 0.01);

        // Stake ended 5 days ago after 30 days: total accrual capped at 30 days = 30.00.
        $staking->update(['start_date' => now()->subDays(35), 'end_date' => now()->subDays(5)]);
        $this->actingAs($user)->post(route('user.staking.withdraw', $staking))->assertSessionHas('success');
        $this->assertEqualsWithDelta(30, (float) $user->fresh()->balance, 0.01);
        $this->assertEqualsWithDelta(30, (float) $staking->fresh()->total_rewards, 0.01);
    }

    public function test_cancel_returns_principal_and_unpaid_rewards_exactly_once(): void
    {
        $user = $this->user();
        $this->actingAs($user)->post(route('user.staking.store'), ['plan_id' => $this->stakingPlan()->id, 'amount_staked' => 1000]);
        $staking = UserStaking::first();
        $staking->update(['start_date' => now()->subDays(4)]);

        $this->actingAs($user)->post(route('user.staking.cancel', $staking))->assertSessionHas('success');
        $this->assertEqualsWithDelta(1004, (float) $user->fresh()->balance, 0.01);
        $this->assertSame(UserStaking::STATUS_CANCELLED, $staking->fresh()->status);

        $this->actingAs($user)->post(route('user.staking.cancel', $staking))->assertSessionHas('error');
        $this->actingAs($user)->post(route('user.staking.withdraw', $staking))->assertSessionHas('error');
        $this->assertEqualsWithDelta(1004, (float) $user->fresh()->balance, 0.01);
    }

    public function test_mining_start_debits_mining_balance_and_is_refused_when_short(): void
    {
        $user = $this->user();
        $plan = $this->miningPlan();

        $this->actingAs($user)->post(route('user.mining.store'), ['plan_id' => $plan->id, 'amount_invested' => 250])->assertRedirect();
        $this->assertEqualsWithDelta(750, (float) $user->fresh()->mining_balance, 0.001);

        $this->actingAs($user)->post(route('user.mining.store'), ['plan_id' => $plan->id, 'amount_invested' => 900])
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Insufficient mining balance'));
        $this->assertSame(1, UserMining::count());
    }

    public function test_mining_withdraw_pays_mined_minus_withdrawn_once(): void
    {
        $user = $this->user();
        $this->actingAs($user)->post(route('user.mining.store'), ['plan_id' => $this->miningPlan()->id, 'amount_invested' => 250]);
        $mining = UserMining::first();

        $this->actingAs($user)->post(route('user.mining.withdraw', $mining))->assertSessionHas('error');

        $mining->update(['total_mined' => 40]); // admin credits mined earnings
        $this->actingAs($user)->post(route('user.mining.withdraw', $mining))
            ->assertSessionHas('success', fn ($m) => ! str_contains($m, '24-48'));
        $this->assertEqualsWithDelta(40, (float) $user->fresh()->balance, 0.001);

        $this->actingAs($user)->post(route('user.mining.withdraw', $mining))->assertSessionHas('error');

        $mining->update(['total_mined' => 65]); // admin raises the cumulative total
        $this->actingAs($user)->post(route('user.mining.withdraw', $mining))->assertSessionHas('success');
        $this->assertEqualsWithDelta(65, (float) $user->fresh()->balance, 0.001);
        $this->assertEqualsWithDelta(65, (float) $mining->fresh()->total_withdrawn, 0.001);
    }

    public function test_cannot_withdraw_someone_elses_earnings(): void
    {
        $owner = $this->user();
        $this->actingAs($owner)->post(route('user.mining.store'), ['plan_id' => $this->miningPlan()->id, 'amount_invested' => 250]);
        $mining = UserMining::first();
        $mining->update(['total_mined' => 40]);

        $this->actingAs($this->user())->post(route('user.mining.withdraw', $mining))->assertForbidden();
        $this->assertSame(0.0, (float) $mining->fresh()->total_withdrawn);
    }
}
