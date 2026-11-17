<?php

namespace App\Services;

use App\Models\User;

/**
 * Trading Strength: a 0-100 score shown on the user dashboard, calculated from
 * real account activity instead of a stored number.
 *
 *   Capital          (35)  trading balance, scaled with a square root so early funding counts
 *   Performance      (30)  win rate of closed trades (needs 3+ closed trades)
 *   Activity         (20)  trades opened in the last 30 days (20 trades = full marks)
 *   Diversification  (15)  +5 each for an active plan, an active bot / AI trader, an active copy trade
 */
class TradingStrengthService
{
    public const CAPITAL_TARGET = 50000;   // trading balance that earns full capital points
    public const MIN_CLOSED_TRADES = 3;    // closed trades needed before win rate counts
    public const ACTIVITY_TARGET = 20;     // trades in 30 days for full activity points

    public function forUser(User $user): array
    {
        $capital = 35 * min(1, sqrt(max(0, (float) $user->trading_balance) / self::CAPITAL_TARGET));

        $closed = $user->trades()->where('status', 'closed');
        $closedCount = (clone $closed)->count();
        $wins = (clone $closed)->where('profit_loss', '>', 0)->count();
        $winRate = $closedCount > 0 ? ($wins / $closedCount) * 100 : 0.0;
        $performance = $closedCount >= self::MIN_CLOSED_TRADES ? 30 * ($winRate / 100) : 0.0;

        $recent = $user->trades()->where('created_at', '>=', now()->subDays(30))->count();
        $activity = 20 * min(1, $recent / self::ACTIVITY_TARGET);

        $hasPlan = $user->activeUserPlans()->exists();
        $hasAutomation = $user->botTradings()->where('status', 'active')->exists()
            || $user->activeAiTraderSubscriptions()->exists();
        $hasCopy = $user->copiedTrades()->where('status', 1)->exists();
        $diversification = 5 * ((int) $hasPlan + (int) $hasAutomation + (int) $hasCopy);

        $score = round(min(100, max(0, $capital + $performance + $activity + $diversification)), 1);

        return [
            'score' => $score,
            'label' => self::label($score),
            'components' => [
                ['key' => 'capital', 'label' => 'Capital', 'points' => round($capital, 1), 'max' => 35],
                ['key' => 'performance', 'label' => 'Performance', 'points' => round($performance, 1), 'max' => 30],
                ['key' => 'activity', 'label' => 'Activity', 'points' => round($activity, 1), 'max' => 20],
                ['key' => 'diversification', 'label' => 'Diversification', 'points' => (float) $diversification, 'max' => 15],
            ],
            'win_rate' => round($winRate, 1),
            'closed_trades' => $closedCount,
            'updated_at' => now()->toIso8601String(),
        ];
    }

    public static function label(float $score): string
    {
        return match (true) {
            $score >= 75 => 'Elite Performance',
            $score >= 50 => 'Strong Performance',
            $score >= 25 => 'Good Performance',
            default => 'Learning Phase',
        };
    }
}
