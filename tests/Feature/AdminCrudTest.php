<?php

namespace Tests\Feature;

use App\Models\Expert;
use App\Models\MarketPair;
use App\Models\MiningPlan;
use App\Models\PlatformSetting;
use App\Models\PropertyProject;
use App\Models\StakingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'account_tier' => 'admin',
            'kyc_level' => 'verified',
        ]);
    }

    public function test_admin_dashboard_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_non_admin_cannot_access_admin(): void
    {
        $user = User::factory()->create(['account_tier' => 'user']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_user_index_loads(): void
    {
        User::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_user_show_loads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $user))
            ->assertOk();
    }

    public function test_admin_can_toggle_user_role(): void
    {
        $user = User::factory()->create(['account_tier' => 'user']);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.update', $user), ['account_tier' => 'admin'])
            ->assertSessionHas('success');

        $this->assertEquals('admin', $user->fresh()->account_tier);
    }

    public function test_admin_can_adjust_user_wallet_balance(): void
    {
        $user = User::factory()->create();
        $wallet = $user->wallets()->create([
            'currency' => 'USD',
            'label' => 'USD',
            'balance' => 100,
            'locked_balance' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.users.wallets.adjust', $user), [
                'currency' => 'USD',
                'operation' => 'add',
                'amount' => 25.5,
                'note' => 'Manual funding',
            ])
            ->assertSessionHas('success');

        $this->assertSame('125.50000000', $wallet->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'admin_credit',
            'currency' => 'USD',
            'status' => 'completed',
            'description' => 'Manual funding',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.users.wallets.adjust', $user), [
                'currency' => 'USD',
                'operation' => 'subtract',
                'amount' => 50,
            ])
            ->assertSessionHas('success');

        $this->assertSame('75.50000000', $wallet->fresh()->balance);
    }

    public function test_admin_cannot_subtract_more_than_wallet_balance(): void
    {
        $user = User::factory()->create();
        $user->wallets()->create([
            'currency' => 'USD',
            'label' => 'USD',
            'balance' => 10,
            'locked_balance' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.users.wallets.adjust', $user), [
                'currency' => 'USD',
                'operation' => 'subtract',
                'amount' => 25,
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_admin_can_place_trade_for_selected_user(): void
    {
        $user = User::factory()->create(['kyc_level' => 'verified']);
        $pair = MarketPair::create([
            'base_currency' => 'BTC',
            'quote_currency' => 'USDT',
            'current_price' => 50000,
            'price_change_24h' => 0,
            'volume_24h' => 0,
            'high_24h' => 50000,
            'low_24h' => 50000,
            'market_cap' => 0,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $user->wallets()->create([
            'currency' => 'USD',
            'label' => 'USD',
            'balance' => 1000,
            'locked_balance' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.trade-room.index', ['user_id' => $user->id]))
            ->assertOk();

        $this->actingAs($this->admin)
            ->post(route('admin.trade-room.store'), [
                'user_id' => $user->id,
                'market_type' => 'crypto',
                'pair_id' => $pair->id,
                'pair' => 'BTC/USDT',
                'side' => 'buy',
                'type' => 'Market',
                'amount' => 0.01,
            ])
            ->assertRedirect(route('admin.trade-room.index', ['user_id' => $user->id]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'pair' => 'BTC/USDT',
            'side' => 'buy',
            'status' => 'completed',
        ]);
        $this->assertEquals('499.50000000', $user->wallets()->where('currency', 'USD')->firstOrFail()->balance);
        $this->assertEquals('0.01000000', $user->wallets()->where('currency', 'BTC')->firstOrFail()->balance);
    }

    public function test_staking_plan_crud(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.staking-plans.store'), [
            'name' => 'Test Plan',
            'currency' => 'USDT',
            'apy' => 12.5,
            'duration_days' => 30,
            'min_amount' => 100,
        ])->assertSessionHas('success');

        $plan = StakingPlan::where('name', 'Test Plan')->first();
        $this->assertNotNull($plan);
        $this->assertEquals(12.5, $plan->apy);

        $this->patch(route('admin.staking-plans.update', $plan), [
            'apy' => 15.0,
        ])->assertSessionHas('success');

        $this->assertEquals(15.0, $plan->fresh()->apy);

        $this->delete(route('admin.staking-plans.destroy', $plan))
            ->assertSessionHas('success');

        $this->assertNull($plan->fresh());
    }

    public function test_mining_plan_crud(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.mining-plans.store'), [
            'name' => 'Gold Mining',
            'roi_percent' => 200,
            'duration_days' => 60,
            'min_amount' => 500,
        ])->assertSessionHas('success');

        $plan = MiningPlan::where('name', 'Gold Mining')->first();
        $this->assertNotNull($plan);

        $this->delete(route('admin.mining-plans.destroy', $plan))
            ->assertSessionHas('success');

        $this->assertNull($plan->fresh());
    }

    public function test_expert_crud(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.experts.store'), [
            'name' => 'Trader Joe',
            'win_rate' => 85,
            'total_volume' => 50000,
        ])->assertSessionHas('success');

        $expert = Expert::where('name', 'Trader Joe')->first();
        $this->assertNotNull($expert);

        $this->patch(route('admin.experts.update', $expert), [
            'status' => 'pro',
        ])->assertSessionHas('success');

        $this->assertEquals('pro', $expert->fresh()->status);
    }

    public function test_property_project_crud(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.property-projects.store'), [
            'title' => 'Luxury Tower',
            'min_investment' => 50000,
            'region' => 'New York',
        ])->assertSessionHas('success');

        $project = PropertyProject::where('title', 'Luxury Tower')->first();
        $this->assertNotNull($project);
        $this->assertTrue($project->is_active);

        $this->patch(route('admin.property-projects.update', $project), [
            'status' => 'active',
        ])->assertSessionHas('success');

        $this->assertEquals('active', $project->fresh()->status);
    }

    public function test_admin_index_routes_return_ok(): void
    {
        $this->actingAs($this->admin);

        $routes = [
            'admin.kyc.index',
            'admin.deposits.index',
            'admin.withdrawals.index',
            'admin.trade-room.index',
            'admin.staking-plans.index',
            'admin.mining-plans.index',
            'admin.experts.index',
            'admin.property-projects.index',
            'admin.settings.index',
        ];

        foreach ($routes as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_admin_can_update_platform_settings(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'settings' => [
                    ['key' => 'mail_admin_address', 'value' => 'ops@cognizantpromarket.com'],
                    ['key' => 'support_email', 'value' => 'help@cognizantpromarket.com'],
                    ['key' => 'site_name', 'value' => 'CognizantPro Market'],
                    ['key' => 'livechat_widget_code', 'value' => '<script>console.log("chat")</script>'],
                ],
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('platform_settings', [
            'key' => 'mail_admin_address',
            'value' => 'ops@cognizantpromarket.com',
            'group' => 'Mail',
            'type' => 'email',
        ]);

        $this->assertDatabaseHas('platform_settings', [
            'key' => 'support_email',
            'value' => 'help@cognizantpromarket.com',
            'group' => 'General',
            'type' => 'email',
        ]);

        $this->assertDatabaseHas('platform_settings', [
            'key' => 'livechat_widget_code',
            'value' => '<script>console.log("chat")</script>',
            'group' => 'Integrations',
            'type' => 'textarea',
        ]);
    }
}
