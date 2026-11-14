<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserHolding;
use App\Models\AiTraderPlan;
use Database\Seeders\AiTraderPlanSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/** Wallet stays in the user's currency; USD market/platform prices are converted at the edge. */
class PlatformPriceCurrencyTest extends TestCase
{
    use RefreshDatabase;

    private const GBP_PER_USD = 0.75605; // seeded in the exchange_rates migration

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function user(string $currency, float $balance = 1000): User
    {
        return User::factory()->create(['currency' => $currency, 'balance' => $balance, 'trading_balance' => $balance]);
    }

    private function asset(float $usdPrice = 100.0): Asset
    {
        return Asset::create(['symbol' => 'TST', 'name' => 'Test Coin', 'type' => 'crypto', 'current_price' => $usdPrice,
            'price_change_24h' => 2, 'price_change_percentage_24h' => 2, 'is_active' => true]);
    }

    public function test_asset_api_returns_prices_in_the_users_currency(): void
    {
        $this->asset(100);

        $gbp = $this->actingAs($this->user('GBP'))->getJson('/user/holding/search?q=TS')->assertOk()->json();
        $this->assertEqualsWithDelta(100 * self::GBP_PER_USD, $gbp[0]['current_price'], 0.001);
        $this->assertEquals(100.0, $gbp[0]['price_usd']);

        $usd = $this->actingAs($this->user('USD'))->getJson('/user/holding/search?q=TS')->assertOk()->json();
        $this->assertEqualsWithDelta(100.0, $usd[0]['current_price'], 0.001);
    }

    public function test_buying_charges_the_converted_server_price_and_ignores_the_client_price(): void
    {
        $asset = $this->asset(100);
        $user = $this->user('GBP', 1000);

        $this->actingAs($user)->postJson('/user/holding/buy', [
            'asset_id' => $asset->id, 'quantity' => 2, 'price_per_unit' => 0.0001, // tampered
        ])->assertOk()->assertJson(['success' => true]);

        $expected = 2 * 100 * self::GBP_PER_USD;               // 151.21
        $this->assertEqualsWithDelta(1000 - $expected, (float) $user->fresh()->balance, 0.01);
        $holding = UserHolding::where('user_id', $user->id)->firstOrFail();
        $this->assertEqualsWithDelta($expected, (float) $holding->total_invested, 0.01);
    }

    public function test_usd_users_are_charged_exactly_the_usd_price(): void
    {
        $asset = $this->asset(100);
        $user = $this->user('USD', 1000);

        $this->actingAs($user)->postJson('/user/holding/buy', ['asset_id' => $asset->id, 'quantity' => 2])->assertOk();

        $this->assertEqualsWithDelta(800.0, (float) $user->fresh()->balance, 0.001);
    }

    public function test_holdings_are_valued_at_the_current_rate_and_selling_credits_the_wallet(): void
    {
        $asset = $this->asset(100);
        $user = $this->user('GBP', 1000);
        $this->actingAs($user)->postJson('/user/holding/buy', ['asset_id' => $asset->id, 'quantity' => 1])->assertOk();

        $asset->update(['current_price' => 200]);               // price doubles in USD
        $this->actingAs($user)->getJson('/user/holding/list')->assertOk();
        $holding = UserHolding::where('user_id', $user->id)->firstOrFail();
        $this->assertEqualsWithDelta(200 * self::GBP_PER_USD, (float) $holding->current_value, 0.01);
        $this->assertEqualsWithDelta(100 * self::GBP_PER_USD, (float) $holding->unrealized_pnl, 0.01);

        $before = (float) $user->fresh()->trading_balance;
        $this->actingAs($user)->postJson('/user/holding/sell', ['asset_id' => $asset->id, 'quantity' => 1])->assertOk();
        $this->assertEqualsWithDelta($before + 200 * self::GBP_PER_USD, (float) $user->fresh()->trading_balance, 0.01);
    }

    public function test_signal_plan_is_charged_in_the_users_currency_and_the_client_amount_is_ignored(): void
    {
        $this->seed(PlanSeeder::class);
        $plan = Plan::where('type', 'signal')->firstOrFail();
        $user = $this->user('GBP', 5000);

        $this->actingAs($user)->post('/user/signal-subscriptions', ['plan_id' => $plan->id, 'amount' => 0.01]);

        $expected = round($plan->price * convert_currency(1, $plan->currency, 'GBP'), 2);
        $this->assertEqualsWithDelta(5000 - $expected, (float) $user->fresh()->balance, 0.01);
    }

    public function test_ai_trader_plan_price_is_shown_converted(): void
    {
        $this->seed(AiTraderPlanSeeder::class);
        $plan = AiTraderPlan::firstOrFail();
        $this->actingAs($this->user('GBP'));

        $this->assertSame('£'.number_format($plan->price * self::GBP_PER_USD, 2), $plan->formatted_price);
    }
}
