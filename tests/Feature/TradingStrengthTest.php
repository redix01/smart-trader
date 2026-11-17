<?php

namespace Tests\Feature;

use App\Models\Trade;
use App\Models\TradePair;
use App\Models\User;
use App\Services\TradingStrengthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradingStrengthTest extends TestCase
{
    use RefreshDatabase;

    private function trade(User $user, string $status, float $pl, ?\DateTimeInterface $at = null): void
    {
        $pair = TradePair::first() ?? TradePair::create(['pair' => 'BTC/USDT', 'type' => 'crypto']);
        $t = Trade::create(['user_id' => $user->id, 'trade_pair_id' => $pair->id, 'market' => 'crypto', 'amount' => 100,
            'status' => $status, 'leverage' => 1, 'duration' => 60, 'action_type' => 'buy', 'profit_loss' => $pl]);
        if ($at) {
            $t->forceFill(['created_at' => $at])->save();
        }
    }

    public function test_new_user_starts_in_the_learning_phase(): void
    {
        $user = User::factory()->create(['trading_balance' => 0]);

        $s = app(TradingStrengthService::class)->forUser($user);

        $this->assertSame(0.0, $s['score']);
        $this->assertSame('Learning Phase', $s['label']);
        $this->assertCount(4, $s['components']);
    }

    public function test_capital_component_scales_with_trading_balance_and_is_capped(): void
    {
        $svc = app(TradingStrengthService::class);

        $low = $svc->forUser(User::factory()->create(['trading_balance' => 12500]))['components'][0]['points'];
        $full = $svc->forUser(User::factory()->create(['trading_balance' => 5000000]))['components'][0]['points'];

        $this->assertEquals(17.5, $low);   // 35 * sqrt(12500/50000)
        $this->assertEquals(35.0, $full);  // capped
    }

    public function test_performance_needs_closed_trades_then_follows_win_rate(): void
    {
        $user = User::factory()->create(['trading_balance' => 0]);
        $svc = app(TradingStrengthService::class);

        $this->trade($user, 'closed', 10);
        $this->trade($user, 'closed', 5);
        $this->assertSame(0.0, $svc->forUser($user)['components'][1]['points']); // only 2 closed

        $this->trade($user, 'closed', -4);
        $this->trade($user, 'closed', 8);
        $s = $svc->forUser($user->fresh());
        $this->assertEquals(75.0, $s['win_rate']);
        $this->assertEquals(22.5, $s['components'][1]['points']); // 30 * 0.75
    }

    public function test_activity_counts_only_the_last_30_days(): void
    {
        $user = User::factory()->create(['trading_balance' => 0]);
        $this->trade($user, 'open', 0);
        $this->trade($user, 'open', 0, now()->subDays(45));

        $this->assertEquals(1.0, app(TradingStrengthService::class)->forUser($user)['components'][2]['points']); // 20 * 1/20
    }

    public function test_score_is_clamped_and_matches_the_label(): void
    {
        $user = User::factory()->create(['trading_balance' => 9000000]);

        $s = app(TradingStrengthService::class)->forUser($user);

        $this->assertLessThanOrEqual(100, $s['score']);
        $this->assertSame(TradingStrengthService::label($s['score']), $s['label']);
        $this->assertSame($s['score'], $user->trading_strength);
    }

    public function test_endpoint_returns_json_for_the_signed_in_user_only(): void
    {
        $this->getJson('/user/trading-strength')->assertUnauthorized();

        $user = User::factory()->create(['trading_balance' => 12500]);
        $this->actingAs($user)->getJson('/user/trading-strength')
            ->assertOk()
            ->assertJsonStructure(['score', 'label', 'components' => [['key', 'label', 'points', 'max']], 'win_rate', 'updated_at'])
            ->assertJsonPath('components.0.points', 17.5);
    }

    public function test_dashboard_card_is_live(): void
    {
        $user = User::factory()->create(['trading_balance' => 12500]);

        $this->actingAs($user)->get('/user/dashboard')->assertOk()
            ->assertSee('id="strengthCard"', false)
            ->assertSee('/user/trading-strength', false)
            ->assertSee('setInterval(refresh, 30000)', false)
            ->assertSee('How it');
    }
}
