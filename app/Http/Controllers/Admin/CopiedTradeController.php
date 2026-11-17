<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CopiedTrade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopiedTradeController extends Controller
{
    public function index()
    {
        $copiedTrades = CopiedTrade::with(['user', 'copy_trader'])->orderBy('created_at','desc')->paginate(20);
        return view('admin.copy-trade.index', compact('copiedTrades'));
    }

    public function editPnl(Request $request, $id)
    {
        $request->validate([
            'trade_count' => 'required|integer|min:0',
            'win' => 'required|integer|min:0',
            'loss' => 'required|integer|min:0',
            'pnl' => 'required|numeric',
        ]);

        // The P&L difference is credited to the user's main balance, the same wallet the stake is returned to when they stop.
        $pnlDifference = DB::transaction(function () use ($request, $id) {
            $copiedTrade = CopiedTrade::whereKey($id)->lockForUpdate()->firstOrFail();
            $difference = (float) $request->pnl - (float) ($copiedTrade->pnl ?? 0);

            $copiedTrade->update([
                'trade_count' => $request->trade_count,
                'win' => $request->win,
                'loss' => $request->loss,
                'pnl' => $request->pnl,
            ]);

            if ($difference != 0) {
                User::whereKey($copiedTrade->user_id)->lockForUpdate()->first()?->increment('balance', $difference);
            }

            return $difference;
        });

        $user = CopiedTrade::find($id)?->user;

        return redirect()->back()->with('success', 'Performance metrics updated successfully! ' .
            ($pnlDifference != 0 ? "User balance adjusted by " . money($pnlDifference, 2, $user) : ""));
    }

    /**
     * Restart a stopped copy trade. Stopping returned the stake to the user's balance, so
     * restarting takes it out again - otherwise stop/start would create money.
     */
    public function activate($id)
    {
        $result = DB::transaction(function () use ($id) {
            $copiedTrade = CopiedTrade::with('copy_trader')->whereKey($id)->lockForUpdate()->firstOrFail();
            if ((int) $copiedTrade->status === 1) {
                return 'This copied trade is already active.';
            }

            $user = User::whereKey($copiedTrade->user_id)->lockForUpdate()->firstOrFail();
            if ((float) $user->balance < (float) $copiedTrade->amount) {
                return 'The user\'s main balance ('.money($user->balance, 2, $user).') is lower than the copied amount ('.money($copiedTrade->amount, 2, $user).'), so it cannot be restarted.';
            }

            $user->decrement('balance', (float) $copiedTrade->amount);
            $copiedTrade->update(['status' => 1, 'stopped_at' => null]);

            return $copiedTrade;
        });

        if (is_string($result)) {
            return redirect()->back()->with('error', $result);
        }

        $result->user->createNotification(
            'copy_trade_started',
            'Copy Trade Started',
            "Your copy trade with {$result->copy_trader->name} has been started by admin.",
            [
                'copied_trade_id' => $result->id,
                'trader_name' => $result->copy_trader->name,
                'amount' => $result->amount
            ]
        );

        return redirect()->back()->with('success', 'Copied trade activated successfully!');
    }

    /** Stop a copy trade exactly like the user's own Stop button: the stake goes back to their main balance. */
    public function deactivate($id)
    {
        $copiedTrade = DB::transaction(function () use ($id) {
            $copiedTrade = CopiedTrade::with('copy_trader')->whereKey($id)->lockForUpdate()->firstOrFail();
            if ((int) $copiedTrade->status !== 1) {
                return null;
            }

            $copiedTrade->update(['status' => 0, 'stopped_at' => now()]);
            User::whereKey($copiedTrade->user_id)->lockForUpdate()->first()?->increment('balance', (float) $copiedTrade->amount);

            return $copiedTrade;
        });

        if (! $copiedTrade) {
            return redirect()->back()->with('error', 'This copied trade is already stopped.');
        }

        $copiedTrade->user->createNotification(
            'copy_trade_stopped',
            'Copy Trade Stopped',
            "Your copy trade with {$copiedTrade->copy_trader->name} has been stopped and " . money($copiedTrade->amount, 2, $copiedTrade->user) . " was returned to your balance.",
            [
                'copied_trade_id' => $copiedTrade->id,
                'trader_name' => $copiedTrade->copy_trader->name,
                'amount' => $copiedTrade->amount
            ]
        );

        return redirect()->back()->with('success', 'Copied trade stopped and the stake returned to the user\'s balance.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $copiedTrade = CopiedTrade::whereKey($id)->lockForUpdate()->firstOrFail();

            // An active copy still holds the user's stake: return it before the record disappears.
            if ((int) $copiedTrade->status === 1) {
                User::whereKey($copiedTrade->user_id)->lockForUpdate()->first()?->increment('balance', (float) $copiedTrade->amount);
            }

            $copiedTrade->delete();
        });

        return redirect()->back()->with('success', 'Copied trade deleted successfully!');
    }
}
