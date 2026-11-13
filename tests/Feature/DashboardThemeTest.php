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
}
