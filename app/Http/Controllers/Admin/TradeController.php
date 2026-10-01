<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\TradePair;
use App\Models\User;
use Illuminate\Http\Request;

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

        if ($validated['amount'] > $user->balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient user balance. Available: $' . number_format($user->balance, 2));
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

        // Deduct stake from user balance
        $user->balance -= $trade->amount;

        // If admin created the trade as already closed, settle PnL immediately
        // (mirrors closeTrade(): balance += amount + profit_loss)
        if ($trade->status === 'closed') {
            $user->balance += $trade->amount + (float) $trade->profit_loss;
        }

        $user->save();

        return redirect()->route('admin.trade.history')->with('success', 'Trade created successfully for ' . $user->fullname() . '!');
    }

    public function openTrades(){
        $trades = Trade::latest()->get();
        return view('admin.trade.open-trades', compact('trades'));
    }
    public function closedTrades(){
        $trades = Trade::orderBy('updated_at', 'desc')->get();
        return view('admin.trade.closed-trades', compact('trades'));
    }

    public function tradeHistory(){
        $openTrades = Trade::where('status', 'open')->latest()->get();
        $closedTrades = Trade::where('status', 'closed')->orderBy('updated_at', 'desc')->get();
        
        \Log::info('Trade History Data:', [
            'open_trades_count' => $openTrades->count(),
            'closed_trades_count' => $closedTrades->count(),
            'open_trades' => $openTrades->pluck('id', 'status'),
            'closed_trades' => $closedTrades->pluck('id', 'status')
        ]);
        
        return view('admin.trade.history', compact('openTrades', 'closedTrades'));
    }



    public function editPnl(Request $request, $id)
    {
        $request->validate([
            'profit_loss' => 'required|numeric'
        ]);

        $trade = Trade::findOrFail($id);
        $trade->profit_loss = $request->profit_loss;
        $trade->save();

        return response()->json(['success' => true, 'message' => 'PnL updated successfully']);
    }

    public function closeTrade(Request $request, $id)
    {
        $trade = Trade::findOrFail($id);
        
        if ($request->action == 'profit') {
            $trade->profit_loss = $request->get('profit_loss');
            $trade->status = 'closed';
            $trade->save();
            
            $user = User::find($trade->user_id);
            $user->balance += $trade->amount + $trade->profit_loss;
            $user->save();
        } else {
            $trade->profit_loss = $request->get('profit_loss');
            $trade->status = 'closed';
            $trade->save();
            
            $user = User::find($trade->user_id);
            $user->balance += $trade->amount + $trade->profit_loss;
            $user->save();
        }
        
        return redirect()->route('admin.closedTrades')->with('success', 'Trade closed successfully!');
    }

    public function destroy($id)
    {
        $trade = Trade::findOrFail($id);
        $trade->delete();
        return back()->with('success', 'Trade deleted successfully!');
    }

}
