<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardThemeTest extends TestCase
{
    use RefreshDatabase;

    public static function dashboardRoutes(): array
    {
        return collect(['dashboard', 'deposit', 'withdrawal', 'transactions', 'kyc', 'staking', 'mining',
            'profile', 'referrals', 'support', 'signals', 'copy-trading', 'portfolio'])
            ->map(fn ($p) => ['/user/'.$p])->all();
    }

    /** @dataProvider dashboardRoutes */
    public function test_user_pages_use_the_landing_theme(string $uri): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($uri);

        $response->assertOk()
            ->assertSee('nano/app.css', false)
            ->assertSee('nm-brand', false)
            ->assertSee('Nano Market Pro')
            ->assertDontSee('themeToggle', false)
            ->assertDontSee('light-theme', false);
    }

    public function test_profile_page_uses_the_dashboard_layout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/user/profile')->assertOk()->assertSee('id="sidebar"', false);
    }

    public function test_admin_layout_keeps_its_own_navy_theme(): void
    {
        $this->assertStringContainsString("partials.nano-theme", file_get_contents(resource_path('views/admin/layouts/app.blade.php')));
        $this->assertStringNotContainsString('nano-app-theme', file_get_contents(resource_path('views/admin/layouts/app.blade.php')));
    }

    public function test_install_prompt_stays_dismissed_for_the_session(): void
    {
        $html = $this->actingAs(User::factory()->create())->get('/user/dashboard')->assertOk()->getContent();

        $this->assertStringContainsString("sessionStorage.setItem('pwaPromptDismissed'", $html);
        $this->assertStringContainsString('if (!isDismissed()) setTimeout(showPrompt', $html);
        $this->assertStringNotContainsString("localStorage.getItem('pwaPromptDismissed')", $html);
    }

    public function test_livechat_widget_loads_on_the_support_page_only(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/user/dashboard')->assertOk()->assertDontSee('jivosite', false)->assertDontSee('embed-widget', false);
        $this->actingAs($user)->get('/user/support')->assertOk()->assertSee('code.jivosite.com/widget/opFqmnSygN', false);
    }

    public function test_user_ai_trader_pages_use_the_current_theme(): void
    {
        $this->seed([\Database\Seeders\AiTraderPlanSeeder::class, \Database\Seeders\AiTraderSeeder::class]);
        $user = User::factory()->create();
        $plan = \App\Models\AiTraderPlan::firstOrFail();
        $trader = \App\Models\AiTrader::firstOrFail();

        foreach (['/user/ai-traders', "/user/ai-traders/plan/{$plan->id}", "/user/ai-traders/trader/{$trader->id}"] as $uri) {
            $this->actingAs($user)->get($uri)->assertOk()
                ->assertSee('nano/app.css', false)
                ->assertDontSee('2FE6DE', false)
                ->assertDontSee('0A0714', false);
        }
    }

    public function test_install_app_button_is_compact_and_cannot_overflow_the_header(): void
    {
        $html = $this->actingAs(User::factory()->create())->get('/user/dashboard')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/id="installPwaBtn"[^>]*flex-shrink-0[^>]*whitespace-nowrap/', $html);
        $this->assertStringContainsString('<span class="sm:hidden">Install</span>', $html);
    }
}
