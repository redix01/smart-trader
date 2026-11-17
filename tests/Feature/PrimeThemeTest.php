<?php

namespace Tests\Feature;

use App\Models\AiTrader;
use App\Models\AiTraderPlan;
use Database\Seeders\AiTraderPlanSeeder;
use Database\Seeders\AiTraderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrimeThemeTest extends TestCase
{
    use RefreshDatabase;

    public static function authRoutes(): array
    {
        return [['/login'], ['/register'], ['/forgot-password']];
    }

    public static function publicRoutes(): array
    {
        return [['/'], ['/forex'], ['/crypto'], ['/stocks'], ['/copy'], ['/about'], ['/faqs'],
            ['/rules'], ['/privacy-policy'], ['/risk-disclosure'], ['/ai-traders']];
    }

    /** @dataProvider authRoutes */
    public function test_auth_pages_use_the_prime_theme(string $uri): void
    {
        $this->get($uri)->assertOk()
            ->assertSee('auth-shell', false)
            ->assertSee('brand-lockup', false)
            ->assertDontSee('Nano Market Pro');
    }

    /** @dataProvider publicRoutes */
    public function test_public_pages_use_the_prime_layout(string $uri): void
    {
        $this->get($uri)->assertOk()
            ->assertSee('prime/styles.css', false)
            ->assertSee('Prime Digital Capital')
            ->assertDontSee('Nano Market Pro')
            ->assertDontSee('cdn.tailwindcss.com', false);
    }

    public function test_register_form_keeps_its_fields_and_csrf(): void
    {
        $html = $this->get('/register')->assertOk()->getContent();
        foreach (['name', 'username', 'email', 'phone', 'country', 'currency', 'password', 'password_confirmation', 'terms', 'registration_time', '_token'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $html, "register form lost field {$field}");
        }
    }

    public function test_login_form_keeps_honeypots_and_time_field(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        foreach (['email', 'password', 'remember', 'website', 'phone_alt', 'company', 'login_time', '_token'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $html, "login form lost field {$field}");
        }
    }

    public function test_landing_page_shows_ticker_and_stock_table(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('embed-widget-ticker-tape.js', false)
            ->assertSee('id="stock-table"', false);
    }

    public function test_ai_trader_pages_render_in_the_prime_layout(): void
    {
        $this->seed([AiTraderPlanSeeder::class, AiTraderSeeder::class]);
        $plan = AiTraderPlan::firstOrFail();
        $trader = AiTrader::firstOrFail();

        $this->get(route('ai-traders.plan', $plan))->assertOk()->assertSee('prime/styles.css', false)->assertSee($plan->name);
        $this->get(route('ai-traders.trader', $trader))->assertOk()->assertSee('prime/styles.css', false)->assertSee($trader->name);
    }
}
