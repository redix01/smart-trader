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
            ->assertSee('Upgrade the way')
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

    public function test_hero_uses_background_video(): void
    {
        $this->get('/')->assertOk()->assertSee('prime/hero-bg.mp4', false);
        $this->assertFileExists(public_path('prime/hero-bg.mp4'));
    }

    public function test_dashboard_market_pages_and_trade_room_render(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('user.markets.stocks'))
            ->assertOk()->assertSee('Stock Prices')->assertSee('embed-widget-market-quotes.js', false);

        $this->actingAs($user)->get(route('user.markets.crypto'))
            ->assertOk()->assertSee('Crypto Prices')->assertSee('crypto-rows', false);

        $this->actingAs($user)->get(route('user.dashboard'))
            ->assertOk()->assertSee('Trade Room')->assertSee(route('user.markets.crypto'), false);
    }

    public function test_market_pages_require_login(): void
    {
        $this->get(route('user.markets.stocks'))->assertRedirect(route('login'));
    }
}
