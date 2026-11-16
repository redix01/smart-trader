<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CurrencyDisplayTest extends TestCase
{
    use RefreshDatabase;

    private function gbpUser(): User
    {
        return User::factory()->create(['currency' => 'GBP', 'balance' => 1500, 'trading_balance' => 800]);
    }

    public function test_money_helper_uses_the_given_users_currency_not_the_signed_in_one(): void
    {
        $gbp = $this->gbpUser();
        $admin = User::factory()->create(['currency' => 'USD']);

        $this->actingAs($admin);

        $this->assertSame('£1,250.50', money(1250.5, 2, $gbp));   // emails / admin contexts
        $this->assertSame('$1,250.50', money(1250.5));            // falls back to the signed-in user
        $this->assertSame('£1,500.00', $gbp->formatted_balance);
        $this->assertSame('£800.00', $gbp->formatted_trading_balance);
    }

    public function test_money_defaults_to_dollars_for_guests(): void
    {
        $this->assertSame('$99.99', money(99.99));
    }

    public function test_gbp_user_sees_pound_signs_across_the_dashboard_and_operations(): void
    {
        $user = $this->gbpUser();

        foreach (['/user/dashboard', '/user/deposit', '/user/withdrawal', '/user/transactions', '/user/staking', '/user/mining', '/user/support'] as $uri) {
            $html = $this->actingAs($user)->get($uri)->assertOk()->getContent();

            $this->assertStringContainsString('window.CURRENCY_SYMBOL = "\\u00a3"', $html, "{$uri} should expose the £ symbol to scripts");
            $this->assertDoesNotMatchRegularExpression('/\$\s?\d/', preg_replace('#<script\b.*?</script>#s', '', $html), "{$uri} shows a hard-coded \$ amount");
        }

        $this->actingAs($user)->get('/user/dashboard')->assertSee('£1,500.00')->assertSee('£800.00');
    }

    public function test_user_facing_code_has_no_hard_coded_dollar_formatting(): void
    {
        $roots = [resource_path('views/dashboard'), resource_path('views/profile'), resource_path('views/emails'), app_path('Models'), app_path('Services')];
        $patterns = ['/\$\{\{/', "/'\\$'\s*\./", "/\\$'\s*\.\s*number_format/", '/\$\$\{/'];
        $skip = ['helpers.php', 'AssetPriceService.php', 'LivechatService.php'];

        $offenders = [];
        foreach ($roots as $root) {
            foreach (File::allFiles($root) as $file) {
                if (in_array($file->getFilename(), $skip, true)) {
                    continue;
                }
                $src = $file->getContents();
                foreach ($patterns as $p) {
                    if (preg_match($p, $src)) {
                        $offenders[] = $file->getRelativePathname().' ~ '.$p;
                    }
                }
            }
        }

        $this->assertSame([], $offenders, "Hard-coded \$ formatting found; use money() / formatAmount():\n".implode("\n", $offenders));
    }
}
