<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Deposit;
use App\Models\LiveTrade;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\TradeEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Admin tools that act on a chosen user: in-app messages, trades placed on their
 * behalf, and direct credits to their Deposits balance. Every action leaves a
 * notification the user sees on their dashboard.
 */
class UserToolsController extends Controller
{
    public const MESSAGE_TYPE = 'admin_message';

    // ---------- Messages ----------

    public function messages(Request $request)
    {
        $users = User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'email']);
        $sent = UserNotification::with('user:id,name,email')
            ->where('type', self::MESSAGE_TYPE)
            ->latest()
            ->paginate(15);

        return view('admin.user-tools.messages', [
            'users' => $users,
            'sent' => $sent,
            'selectedUser' => $request->query('user'),
        ]);
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'audience' => 'required|in:selected,all',
            'user_ids' => 'required_if:audience,selected|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:120',
            'message' => 'required|string|max:2000',
        ]);

        $recipients = $data['audience'] === 'all'
            ? User::where('role', 'user')->pluck('id')
            : collect($data['user_ids'] ?? [])->unique();

        if ($recipients->isEmpty()) {
            return back()->withInput()->with('error', 'Choose at least one user.');
        }

        $now = now();
        DB::transaction(function () use ($recipients, $data, $now) {
            foreach ($recipients as $userId) {
                UserNotification::create([
                    'user_id' => $userId,
                    'type' => self::MESSAGE_TYPE,
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'data' => ['from' => 'admin', 'admin_id' => auth()->id()],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        return back()->with('success', 'Message sent to '.$recipients->count().' user(s). It shows on their dashboard.');
    }

    // ---------- Deposits balance ----------

    public function deposit(Request $request, string $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:100000000',
            'note' => 'nullable|string|max:255',
        ]);

        $user = DB::transaction(function () use ($id, $data) {
            $user = User::whereKey($id)->lockForUpdate()->firstOrFail();
            $user->increment('deposit_balance', round((float) $data['amount'], 2));

            Deposit::create([
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'status' => 1, // approved
                'wallet_type' => 'deposit',
            ]);

            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'title' => 'Deposit received',
                'message' => $user->formatAmount($data['amount']).' was added to your Deposits balance.'
                    .(! empty($data['note']) ? ' '.$data['note'] : ''),
                'data' => ['amount' => (float) $data['amount'], 'wallet' => 'deposit_balance', 'by_admin' => true],
            ]);

            return $user->fresh();
        });

        return back()->with('success', 'Deposited '.$user->formatAmount($data['amount']).' to '.$user->name.'. Deposits balance is now '.$user->formatAmount($user->deposit_balance).'.');
    }

    // ---------- Trades for a user ----------

    public function trades(Request $request)
    {
        $users = User::where('role', 'user')->orderBy('name')
            ->get(['id', 'name', 'email', 'trading_balance', 'deposit_balance', 'currency']);
        $assets = Asset::whereIn('type', ['crypto', 'stock'])
            ->orderBy('type')->orderBy('symbol')
            ->get(['symbol', 'name', 'type']);
        $trades = LiveTrade::with('user:id,name,email,currency')
            ->whereNotNull('placed_by')
            ->latest()
            ->paginate(20);

        return view('admin.user-tools.trades', [
            'users' => $users,
            'assets' => $assets,
            'trades' => $trades,
            'selectedUser' => $request->query('user'),
        ]);
    }

    public function placeTrade(Request $request, TradeEngine $engine)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'asset_type' => 'required|in:crypto,stock',
            'symbol' => 'required|string|max:20',
            'side' => 'required|in:buy,sell',
            'amount' => 'required|numeric|min:1',
            'leverage' => 'nullable|numeric|min:1|max:100',
            'take_profit' => 'nullable|numeric|gt:0',
            'stop_loss' => 'nullable|numeric|gt:0',
            'fund_from' => 'required|in:trading_balance,deposit_balance',
        ]);

        if (! Asset::where('type', $data['asset_type'])->where('symbol', $data['symbol'])->exists()) {
            return back()->withInput()->with('error', 'That market is not available.');
        }

        try {
            $trade = DB::transaction(function () use ($data, $engine) {
                $user = User::whereKey($data['user_id'])->lockForUpdate()->firstOrFail();
                $margin = round((float) $data['amount'], 2);

                // Funding from Deposits moves the margin into the trading wallet first.
                if ($data['fund_from'] === 'deposit_balance') {
                    if ($margin > (float) $user->deposit_balance) {
                        throw ValidationException::withMessages(['amount' => 'Insufficient Deposits balance. Available: '.$user->formatAmount($user->deposit_balance).'.']);
                    }
                    $user->decrement('deposit_balance', $margin);
                    $user->increment('trading_balance', $margin);
                }

                $trade = $engine->place($user, [
                    'asset_type' => $data['asset_type'],
                    'symbol' => $data['symbol'],
                    'order_type' => 'market',
                    'side' => $data['side'],
                    'amount' => $margin,
                    'leverage' => $data['leverage'] ?? 1,
                    'take_profit' => $data['take_profit'] ?? null,
                    'stop_loss' => $data['stop_loss'] ?? null,
                ]);
                $trade->update(['placed_by' => auth()->id()]);

                UserNotification::create([
                    'user_id' => $user->id,
                    'type' => 'trade',
                    'title' => 'New trade opened for you',
                    'message' => 'Your account manager opened a '.strtoupper($trade->side).' position on '.$trade->symbol
                        .' with '.$user->formatAmount($trade->amount).' margin at '.(float) $trade->leverage.'x. Track it in the Trade Room.',
                    'data' => ['live_trade_id' => $trade->id, 'by_admin' => true],
                ]);

                return $trade;
            });
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('admin.user-trades.index')
            ->with('success', strtoupper($trade->side).' '.$trade->symbol.' opened for '.$trade->user->name.' at '.money($trade->entry_price, 2, $trade->user).'.');
    }

    public function closeTrade(LiveTrade $liveTrade, TradeEngine $engine)
    {
        try {
            $closed = $engine->close($liveTrade, null, 'admin');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        if (! $closed) {
            return back()->with('error', 'This position is already closed.');
        }

        $user = $closed->user;
        $pnl = (float) $closed->realized_pnl;
        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'trade',
            'title' => 'Trade closed',
            'message' => $closed->symbol.' position closed. '.($pnl >= 0 ? 'Profit ' : 'Loss ').$user->formatAmount(abs($pnl)).'.',
            'data' => ['live_trade_id' => $closed->id, 'by_admin' => true],
        ]);

        return back()->with('success', $closed->symbol.' position closed for '.$user->name.'. P&L '.$user->formatAmount($pnl).'.');
    }
}
