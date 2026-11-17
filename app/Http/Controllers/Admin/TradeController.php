<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\TradePair;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
    public function index()
    {
        $trades = Trade::all();
        $pairs = TradePair::all();
        $users = User::where('role', 'user')->get();
        return view('admin.trade.trade', compact('trades', 'pairs', 'users'));
    }

    public function create()
    {
        $pairs = TradePair::all();
        $users = User::where('role', 'user')->orderBy('name')->get();
        return view('admin.trade.create', compact('pairs', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'trade_pair_id' => 'required|exists:trade_pairs,id',
            'amount' => 'required|numeric|min:0.01',
            'leverage' => 'required|integer|min:1|max:500',
            'duration' => 'required|integer|min:1',
            'action_type' => 'required|in:buy,sell',
            'status' => 'nullable|in:open,closed',
            'profit_loss' => 'nullable|numeric',
            'stop_loss' => 'nullable|numeric',
            'take_profit' => 'nullable|numeric',
        ]);

        $user = User::findOrFail($validated['user_id']);

        $created = DB::transaction(function () use ($validated, $user) {
            $locked = User::whereKey($user->getKey())->lockForUpdate()->first();
            if ($validated['amount'] > (float) $locked->balance) {
                return false;
            }

            $trade = Trade::create([
                'user_id' => $validated['user_id'],
                'trade_pair_id' => $validated['trade_pair_id'],
                'amount' => $validated['amount'],
                'status' => $validated['status'] ?? 'open',
                'leverage' => $validated['leverage'],
                'duration' => $validated['duration'],
                'action_type' => $validated['action_type'],
                'stop_loss' => $validated['stop_loss'] ?? null,
                'take_profit' => $validated['take_profit'] ?? null,
                'profit_loss' => $validated['profit_loss'] ?? 0,
            ]);

            // Stake leaves the balance; a trade created as already closed pays stake + P&L straight back
            // (never below zero), mirroring closeTrade().
            $net = $trade->status === 'closed'
                ? max(0, (float) $trade->amount + (float) $trade->profit_loss) - (float) $trade->amount
                : -(float) $trade->amount;
            $locked->increment('balance', $net);

            return true;
        });

        if (! $created) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient user balance. Available: ' . money($user->balance, 2, $user));
        }

        return redirect()->route('admin.trade.history')->with('success', 'Trade created successfully for ' . $user->fullname() . '!');
    }

    public function tradeHistory(Request $request){
        $filterUser = $request->filled('user') ? User::find($request->query('user')) : null;
        $base = fn () => Trade::with(['user', 'trade_pair'])->when($filterUser, fn ($q) => $q->where('user_id', $filterUser->id));

        $openTrades = $base()->where('status', 'open')->latest()->get();
        $closedTrades = $base()->where('status', 'closed')->orderBy('updated_at', 'desc')->get();

        return view('admin.trade.history', compact('openTrades', 'closedTrades', 'filterUser'));
    }

    /**
     * Edit a trade's P&L. An open trade's P&L is paid when it is closed; for a trade that is
     * already closed the stake + old P&L was already paid, so the balance moves by the difference.
     */
    public function editPnl(Request $request, $id)
    {
        $request->validate([
            'profit_loss' => 'required|numeric'
        ]);

        DB::transaction(function () use ($request, $id) {
            $trade = Trade::whereKey($id)->lockForUpdate()->firstOrFail();
            $old = (float) $trade->profit_loss;
            $new = (float) $request->profit_loss;

            $trade->profit_loss = $new;
            $trade->save();

            if ($trade->status === 'closed' && $new != $old) {
                User::whereKey($trade->user_id)->lockForUpdate()->first()?->increment('balance', $new - $old);
            }
        });

        return response()->json(['success' => true, 'message' => 'PnL updated successfully']);
    }

    /**
     * Close an open trade and pay stake + P&L to the user's main balance.
     * The "Loss" button sends a positive number: it is always booked as a loss.
     * Closing twice never pays twice.
     */
    public function closeTrade(Request $request, $id)
    {
        $request->validate([
            'profit_loss' => 'required|numeric',
            'action' => 'nullable|in:profit,loss',
        ]);

        $pnl = (float) $request->profit_loss;
        if ($request->action === 'loss') {
            $pnl = -abs($pnl);
        } elseif ($request->action === 'profit') {
            $pnl = abs($pnl);
        }

        $closed = DB::transaction(function () use ($id, $pnl) {
            $trade = Trade::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($trade->status !== 'open') {
                return false;
            }

            $trade->profit_loss = $pnl;
            $trade->status = 'closed';
            $trade->save();

            $user = User::whereKey($trade->user_id)->lockForUpdate()->first();
            $user?->increment('balance', max(0, (float) $trade->amount + $pnl));

            return true;
        });

        if (! $closed) {
            return redirect()->route('admin.trade.history')->with('error', 'This trade is already closed.');
        }

        return redirect()->route('admin.trade.history')->with('success', 'Trade closed successfully!');
    }

    /** Delete a trade. An open trade's stake is returned to the user first so no money disappears. */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $trade = Trade::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($trade->status === 'open') {
                User::whereKey($trade->user_id)->lockForUpdate()->first()?->increment('balance', (float) $trade->amount);
            }
            $trade->delete();
        });

        return back()->with('success', 'Trade deleted successfully!');
    }

}
