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
