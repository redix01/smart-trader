<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveTrade;
use App\Services\TradeEngine;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Admin view of the Trade Room orders users place from their dashboard (live_trades).
 * Every money movement goes through TradeEngine so margin is refunded / paid out exactly
 * like the user's own Cancel and Close buttons, under row locks, never twice.
 */
class LiveTradeController extends Controller
{
    private const STATUSES = ['pending', 'open', 'closed', 'liquidated', 'cancelled'];

    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        $filterUser = $request->filled('user') ? \App\Models\User::find($request->query('user')) : null;

        $query = LiveTrade::with('user')->latest();
        if ($filterUser) {
            $query->where('user_id', $filterUser->id);
        }
        if (in_array($status, self::STATUSES, true)) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('symbol', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'));
            });
        }

        $trades = $query->paginate(20)->withQueryString();
        $counts = LiveTrade::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');

        return view('admin.live-trades.index', compact('trades', 'counts', 'status', 'search', 'filterUser'));
    }

    /** Close an open position. With profit_loss the admin sets the result; without it, it closes at the live market price. */
    public function close(Request $request, LiveTrade $liveTrade, TradeEngine $engine)
    {
        $data = $request->validate(['profit_loss' => 'nullable|numeric|between:-1000000000,1000000000']);

        try {
            $closed = ($data['profit_loss'] ?? null) !== null && $data['profit_loss'] !== ''
                ? $engine->settle($liveTrade, (float) $data['profit_loss'])
                : $engine->close($liveTrade, null, 'manual');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        if (! $closed) {
            return back()->with('error', 'This position is not open any more.');
        }

        $pnl = (float) $closed->realized_pnl;
        $closed->user?->createNotification(
            'trade_closed',
            'Position closed',
            'Your '.$closed->symbol.' position was closed with '.($pnl >= 0 ? 'a profit of ' : 'a loss of ').money(abs($pnl), 2, $closed->user).'.',
            ['live_trade_id' => $closed->id, 'realized_pnl' => $pnl]
        );

        return back()->with('success', 'Position closed. '.($pnl >= 0 ? 'Profit ' : 'Loss ').money(abs($pnl), 2, $closed->user).' settled to the user\'s trading balance.');
    }

    /** Cancel a pending limit order and refund its margin. */
    public function cancel(LiveTrade $liveTrade, TradeEngine $engine)
    {
        if (! $engine->cancel($liveTrade)) {
            return back()->with('error', 'Only orders that are still waiting can be cancelled.');
        }

        return back()->with('success', 'Order cancelled and '.money($liveTrade->amount, 2, $liveTrade->user).' returned to the user\'s trading balance.');
    }

    /** Remove a finished trade from history. Live orders must be closed or cancelled first so no margin is lost. */
    public function destroy(LiveTrade $liveTrade)
    {
        if (in_array($liveTrade->status, [LiveTrade::STATUS_PENDING, LiveTrade::STATUS_OPEN], true)) {
            return back()->with('error', 'Close or cancel this trade first so the user\'s margin is settled, then delete it.');
        }
        $liveTrade->delete();

        return back()->with('success', 'Trade removed from history.');
    }
}
