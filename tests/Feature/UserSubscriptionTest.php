<?php

namespace Tests\Feature;

use App\Models\CopySubscription;
use App\Models\Expert;
use App\Models\MiningPlan;
use App\Models\MiningSubscription;
use App\Models\PropertyInvestment;
use App\Models\PropertyProject;
use App\Models\Stake;
use App\Models\StakingPlan;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_tier' => 'user',
            'kyc_level' => 'verified',
        ]);

        Wallet::factory()->create([
            'user_id' => $this->user->id,
            'currency' => 'USD',
            'balance' => 50000,
        ]);

        Wallet::factory()->create([
            'user_id' => $this->user->id,
            'currency' => 'USDT',
            'balance' => 50000,
        ]);
    }

    public function test_user_can_subscribe_to_staking_plan(): void
    {
        $plan = StakingPlan::factory()->create(['min_amount' => 100, 'is_active' => true]);

        $this->actingAs($this->user)
            ->post(route('stakes.store'), [
                'plan_id' => $plan->id,
                'amount' => 1000,
            ])->assertSessionHas('success');

        $this->assertDatabaseHas('stakes', [
            'user_id' => $this->user->id,
            'staking_plan_id' => $plan->id,
            'amount' => 1000,
        ]);
    }

    public function test_user_can_subscribe_to_mining_plan(): void
    {
        $plan = MiningPlan::factory()->create(['min_amount' => 500, 'is_active' => true]);

        $this->actingAs($this->user)
            ->post(route('mining.store'), [
                'plan_id' => $plan->id,
                'amount' => 1000,
            ])->assertSessionHas('success');

        $this->assertDatabaseHas('mining_subscriptions', [
            'user_id' => $this->user->id,
            'mining_plan_id' => $plan->id,
            'amount' => 1000,
        ]);
    }

    public function test_user_can_subscribe_to_expert(): void
    {
        $expert = Expert::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($this->user)
            ->post(route('experts.store'), [
                'expert_id' => $expert->id,
                'amount' => 500,
            ])->assertSessionHas('success');

        $this->assertDatabaseHas('copy_subscriptions', [
            'user_id' => $this->user->id,
            'expert_id' => $expert->id,
        ]);
    }

    public function test_user_can_invest_in_property(): void
    {
        $project = PropertyProject::factory()->create([
            'status' => 'active',
        ]);

        $this->actingAs($this->user)
            ->post(route('realestate.store'), [
                'project_id' => $project->id,
                'amount' => 2000,
            ])->assertSessionHas('success');

        $this->assertDatabaseHas('property_investments', [
            'user_id' => $this->user->id,
            'property_project_id' => $project->id,
        ]);
    }

    public function test_user_cannot_subscribe_without_kyc(): void
    {
        $unverified = User::factory()->create(['kyc_level' => 'unverified']);
        $plan = StakingPlan::factory()->create(['is_active' => true]);

        $this->actingAs($unverified)
            ->post(route('stakes.store'), [
                'staking_plan_id' => $plan->id,
                'amount' => 1000,
            ])->assertSessionHasErrors();
    }

    public function test_user_can_view_product_pages(): void
    {
        $this->actingAs($this->user);

        $pages = ['stakes', 'mining', 'experts', 'realestate'];
        foreach ($pages as $page) {
            $this->get(route($page))->assertOk();
        }
    }
}
