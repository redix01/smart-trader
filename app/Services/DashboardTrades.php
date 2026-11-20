<?php

namespace App\Services;

use App\Models\LiveTrade;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * One list of a user's trades for the dashboard: Trade Room positions (live_trades,
 * including ones an admin placed for them) plus legacy trades, as uniform rows.
 */
class DashboardTrades
{
    /** @return array{trades:Collection,openTrades:Collection,closedTrades:Collection,totalTrades:int,winningTrades:int,winRate:float,avgProfit:float} */
    public static function forUser(User $user): array
    {
        $live = LiveTrade::where('user_id', $user->id)
            ->where('status', '!=', LiveTrade::STATUS_CANCELLED)
            ->latest()->get()
            ->map(fn (LiveTrade $t) => (object) [
                'market' => $t->symbol,
                'side' => $t->side,
                'amount' => (float) $t->amount,
                'leverage' => (float) $t->leverage,
                'entry' => $t->entry_price !== null ? (float) $t->entry_price : (float) $t->price,
                'exit' => $t->exit_price !== null ? (float) $t->exit_price : null,
                'pnl' => $t->realized_pnl !== null ? (float) $t->realized_pnl : null,
                'open' => in_array($t->status, [LiveTrade::STATUS_OPEN, LiveTrade::STATUS_PENDING], true),
                'status' => $t->status,
                'by_manager' => $t->placed_by !== null,
                'date' => $t->closed_at ?? $t->created_at,
            ]);

        $legacy = Trade::whereUserId($user->id)->with('trade_pair')->latest()->get()
            ->map(fn (Trade $t) => (object) [
                'market' => $t->trade_pair->pair ?? 'N/A',
                'side' => $t->action_type,
                'amount' => (float) $t->amount,
                'leverage' => (float) $t->leverage,
                'entry' => (float) ($t->entry_price ?? 0),
                'exit' => $t->exit_price !== null ? (float) $t->exit_price : null,
                'pnl' => (float) ($t->profit_loss ?? 0),
                'open' => $t->status === 'open',
                'status' => $t->status,
                'by_manager' => false,
                'date' => $t->updated_at,
            ]);

        $trades = $live->concat($legacy)->sortByDesc('date')->values();
        $open = $trades->where('open', true)->values();
        $closed = $trades->where('open', false)->values();
        $winning = $closed->filter(fn ($t) => $t->pnl > 0)->count();

        return [
            'trades' => $trades,
            'openTrades' => $open,
            'closedTrades' => $closed,
            'totalTrades' => $trades->count(),
            'winningTrades' => $winning,
            'winRate' => $trades->count() > 0 ? $winning / $trades->count() * 100 : 0,
            'avgProfit' => $closed->count() > 0 ? (float) $closed->avg('pnl') : 0,
        ];
    }
}
