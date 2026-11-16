<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrimecapPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_shows_prime_digital_capital_brand(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Prime Digital Capital')
            ->assertSee('Every market. One trading account.')
            ->assertSee('Trade Room')->assertSee('Copy trading')->assertSee('AI traders')
            ->assertSee('Trading bots')->assertSee('Staking')
            ->assertDontSee('crypto card')
            ->assertSee('support@primedigitalcap.com')
            ->assertDontSee('Nano Market Pro');
    }

    public function test_trade_and_company_pages_use_the_prime_layout(): void
    {
        foreach (['crypto', 'stocks', 'forex', 'copy', 'about', 'faqs', 'privacy-policy', 'rules', 'risk-disclosure'] as $page) {
            $this->get('/'.$page)
                ->assertOk()
                ->assertSee('prime/styles.css', false)
                ->assertSee('Prime Digital Capital')
                ->assertDontSee('Laravel LTD');
        }
    }

    public function test_landing_lists_copy_traders_and_plans_from_database(): void
    {
        \App\Models\CopyTrader::create(['name' => 'Ada Quant', 'avatar' => null, 'amount' => 250, 'win_rate' => 81, 'profit_share' => 20, 'win' => 90, 'loss' => 21, 'status' => 'active']);
        \App\Models\Plan::create(['name' => 'Test Staking', 'type' => 'staking', 'price' => 50, 'min_funding' => 75, 'is_active' => true]);

        $this->get('/')->assertOk()
            ->assertSee('Ada Quant')->assertSee('81%')->assertSee('$250')
            ->assertSee('From <b>$75</b>', false);
    }

    public function test_landing_renders_with_empty_tables(): void
    {
        $this->get('/')->assertOk()->assertSee('Compare traders');
    }

    public function test_hero_uses_background_video(): void
    {
        $this->get('/')->assertOk()->assertSee('prime/hero-bg.mp4', false);
        $this->assertFileExists(public_path('prime/hero-bg.mp4'));
    }

    public function test_dashboard_shows_live_markets_that_open_the_trade_room(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('id="dashMarkets"', false)
            ->assertSee(e(route('user.liveTrading.index', ['asset_type' => 'stock', 'symbol' => 'AAPL'])), false)
            ->assertSee(e(route('user.liveTrading.index', ['asset_type' => 'crypto', 'symbol' => 'BTC'])), false);
    }

    public function test_read_only_price_pages_are_removed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/user/markets/stocks')->assertNotFound();
        $this->actingAs($user)->get('/user/markets/crypto')->assertNotFound();
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('user.markets.stocks'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('user.markets.crypto'));
    }
}
