<?php

namespace App\Http\Controllers;

use App\Models\LiveTrade;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveTradingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        app(\App\Services\TradeEngine::class)->match($user); // fill reached limits / TP / SL before showing state
        $liveTrades = $user->liveTrades()->latest()->get();
        $cryptoAssets = Asset::where('type', 'crypto')->get();
        $stockAssets = Asset::where('type', 'stock')->get();
        $forexAssets = collect([
            // Major Pairs
            ['symbol' => 'EUR/USD', 'name' => 'Euro / US Dollar'],
            ['symbol' => 'GBP/USD', 'name' => 'British Pound / US Dollar'],
            ['symbol' => 'USD/JPY', 'name' => 'US Dollar / Japanese Yen'],
            ['symbol' => 'USD/CHF', 'name' => 'US Dollar / Swiss Franc'],
            ['symbol' => 'AUD/USD', 'name' => 'Australian Dollar / US Dollar'],
            ['symbol' => 'USD/CAD', 'name' => 'US Dollar / Canadian Dollar'],
            ['symbol' => 'NZD/USD', 'name' => 'New Zealand Dollar / US Dollar'],
            
            // Cross Pairs
            ['symbol' => 'EUR/GBP', 'name' => 'Euro / British Pound'],
            ['symbol' => 'EUR/JPY', 'name' => 'Euro / Japanese Yen'],
            ['symbol' => 'GBP/JPY', 'name' => 'British Pound / Japanese Yen'],
            ['symbol' => 'EUR/CHF', 'name' => 'Euro / Swiss Franc'],
            ['symbol' => 'GBP/CHF', 'name' => 'British Pound / Swiss Franc'],
            ['symbol' => 'AUD/JPY', 'name' => 'Australian Dollar / Japanese Yen'],
            ['symbol' => 'CAD/JPY', 'name' => 'Canadian Dollar / Japanese Yen'],
            ['symbol' => 'NZD/JPY', 'name' => 'New Zealand Dollar / Japanese Yen'],
            
            // Commodity Pairs
            ['symbol' => 'AUD/CAD', 'name' => 'Australian Dollar / Canadian Dollar'],
            ['symbol' => 'AUD/CHF', 'name' => 'Australian Dollar / Swiss Franc'],
            ['symbol' => 'AUD/NZD', 'name' => 'Australian Dollar / New Zealand Dollar'],
            ['symbol' => 'CAD/CHF', 'name' => 'Canadian Dollar / Swiss Franc'],
            ['symbol' => 'EUR/AUD', 'name' => 'Euro / Australian Dollar'],
            ['symbol' => 'EUR/CAD', 'name' => 'Euro / Canadian Dollar'],
            ['symbol' => 'GBP/AUD', 'name' => 'British Pound / Australian Dollar'],
            ['symbol' => 'GBP/CAD', 'name' => 'British Pound / Canadian Dollar'],
            ['symbol' => 'GBP/NZD', 'name' => 'British Pound / New Zealand Dollar'],
            
            // Exotic Pairs
            ['symbol' => 'USD/SEK', 'name' => 'US Dollar / Swedish Krona'],
            ['symbol' => 'USD/NOK', 'name' => 'US Dollar / Norwegian Krone'],
            ['symbol' => 'USD/DKK', 'name' => 'US Dollar / Danish Krone'],
            ['symbol' => 'USD/PLN', 'name' => 'US Dollar / Polish Zloty'],
            ['symbol' => 'USD/CZK', 'name' => 'US Dollar / Czech Koruna'],
            ['symbol' => 'USD/HUF', 'name' => 'US Dollar / Hungarian Forint'],
            ['symbol' => 'USD/TRY', 'name' => 'US Dollar / Turkish Lira'],
            ['symbol' => 'USD/ZAR', 'name' => 'US Dollar / South African Rand'],
            ['symbol' => 'USD/MXN', 'name' => 'US Dollar / Mexican Peso'],
            ['symbol' => 'USD/BRL', 'name' => 'US Dollar / Brazilian Real'],
            ['symbol' => 'USD/INR', 'name' => 'US Dollar / Indian Rupee'],
            ['symbol' => 'USD/SGD', 'name' => 'US Dollar / Singapore Dollar'],
            ['symbol' => 'USD/HKD', 'name' => 'US Dollar / Hong Kong Dollar'],
            ['symbol' => 'USD/KRW', 'name' => 'US Dollar / South Korean Won'],
            ['symbol' => 'USD/CNY', 'name' => 'US Dollar / Chinese Yuan'],
            
            // Additional Cross Pairs
            ['symbol' => 'CHF/JPY', 'name' => 'Swiss Franc / Japanese Yen'],
            ['symbol' => 'NZD/CHF', 'name' => 'New Zealand Dollar / Swiss Franc'],
            ['symbol' => 'CAD/AUD', 'name' => 'Canadian Dollar / Australian Dollar'],
            ['symbol' => 'EUR/NZD', 'name' => 'Euro / New Zealand Dollar'],
            ['symbol' => 'GBP/CHF', 'name' => 'British Pound / Swiss Franc'],
        ]);
        
        // Market to open on: ?asset_type=&symbol= (also used by the old /trade deep link), else the first crypto.
        $requestedType = in_array(request('asset_type'), ['crypto', 'stock'], true) ? request('asset_type') : null;
        $selected = $requestedType
            ? Asset::where('type', $requestedType)->where('symbol', request('symbol'))->first()
            : null;
        $selected = $selected ?? $cryptoAssets->first() ?? $stockAssets->first();

        $positions = $liveTrades->where('status', LiveTrade::STATUS_OPEN)->values();
        $openOrders = $liveTrades->where('status', LiveTrade::STATUS_PENDING)->values();
        $orderHistory = $liveTrades->whereIn('status', [LiveTrade::STATUS_CLOSED, LiveTrade::STATUS_LIQUIDATED, LiveTrade::STATUS_CANCELLED])->take(20)->values();
        $inOpenOrders = $openOrders->sum('amount') + $positions->sum('amount');

        return view('dashboard.live-trading.index', compact(
            'liveTrades',
            'cryptoAssets',
            'stockAssets',
            'forexAssets',
            'selected',
            'positions',
            'openOrders',
            'orderHistory',
            'inOpenOrders'
        ));
    }

    /** Old per-market page: open the Trade Room terminal on that market instead. */
    public function trade(Request $request)
    {
        return redirect()->route('user.liveTrading.index', array_filter([
            'asset_type' => $request->get('asset_type'),
            'symbol' => $request->get('symbol'),
        ]));
    }

    public function store(Request $request, \App\Services\TradeEngine $engine)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'asset_type' => 'required|string|in:crypto,stock',
            'symbol' => 'required|string|max:20',
            'order_type' => 'required|string|in:limit,market',
            'side' => 'required|string|in:buy,sell',
            // Market orders: margin amount. Limit orders: limit price + position quantity.
            'amount' => 'required_if:order_type,market|nullable|numeric|min:1',
            'price' => 'required_if:order_type,limit|nullable|numeric|gt:0',
            'quantity' => 'required_if:order_type,limit|nullable|numeric|gt:0',
            'leverage' => 'nullable|numeric|min:1|max:100',
            'take_profit' => 'nullable|numeric|gt:0',
            'stop_loss' => 'nullable|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        if (! Asset::where('type', $request->asset_type)->where('symbol', $request->symbol)->exists()) {
            return response()->json(['success' => false, 'message' => 'This market is not available.'], 422);
        }

        try {
            $trade = $engine->place(Auth::user(), $validator->validated());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->first(), 'errors' => $e->errors()], 422);
        }

        $qty = rtrim(rtrim(number_format((float) $trade->quantity, 8), '0'), '.');
        $message = $trade->isOpen()
            ? ($trade->side === 'buy' ? 'Bought ' : 'Sold ').$qty.' '.$trade->symbol.' at '.money($trade->entry_price).'. Position is open.'
            : 'Limit order placed: '.($trade->side === 'buy' ? 'buy ' : 'sell ').$qty.' '.$trade->symbol.' at '.money($trade->price).'.';

        return response()->json(['success' => true, 'message' => $message, 'trade' => $trade]);
    }

    public function cancel(LiveTrade $liveTrade, \App\Services\TradeEngine $engine)
    {
        if ($liveTrade->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to this trade.'], 403);
        }

        if (! $engine->cancel($liveTrade)) {
            return response()->json(['success' => false, 'message' => 'Only orders that are still waiting can be cancelled.'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Order cancelled. '.money($liveTrade->amount).' returned to your trading balance.']);
    }

    public function close(LiveTrade $liveTrade, \App\Services\TradeEngine $engine)
    {
        if ($liveTrade->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to this trade.'], 403);
        }

        try {
            $closed = $engine->close($liveTrade);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->first()], 422);
        }

        if (! $closed) {
            return response()->json(['success' => false, 'message' => 'This position is already closed.'], 400);
        }

        $pnl = (float) $closed->realized_pnl;

        return response()->json([
            'success' => true,
            'message' => 'Position closed at '.money($closed->exit_price).'. '.($pnl >= 0 ? 'Profit ' : 'Loss ').money(abs($pnl)).'.',
            'trade' => $closed,
        ]);
    }

    public function getPrice(Request $request)
    {
        $assetType = $request->input('asset_type');
        $symbol = $request->input('symbol');
        
        if ($assetType === 'crypto' || $assetType === 'stock') {
            $asset = Asset::where('type', $assetType)
                ->where('symbol', $symbol)
                ->first();
                
            if ($asset) {
                return response()->json([
                    'success' => true,
                    'price' => auth()->user()->convertFrom($asset->current_price),
                    'change_24h' => $asset->price_change_24h
                ]);
            }
        } else {
            // For forex, return a mock price
            $mockPrice = rand(100, 200) / 100;
            return response()->json([
                'success' => true,
                'price' => $mockPrice,
                'change_24h' => rand(-50, 50) / 10
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Asset not found'
        ], 404);
    }
    
    /**
     * Refresh all asset prices with real-time data
     */
    public function refreshPrices(Request $request)
    {
        try {
            $priceService = new \App\Services\AssetPriceService();
            
            // Update crypto prices
            $priceService->updateCryptoPrices();
            
            // Update stock prices
            $priceService->updateStockPrices();
            
            return response()->json([
                'success' => true,
                'message' => 'Asset prices refreshed successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh prices: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show trading history with open and closed trades
     */
    public function history()
    {
        $user = Auth::user();
        
        $openTrades = LiveTrade::where('user_id', $user->id)
            ->whereIn('status', [LiveTrade::STATUS_PENDING, LiveTrade::STATUS_OPEN])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $closedTrades = LiveTrade::where('user_id', $user->id)
            ->whereIn('status', [LiveTrade::STATUS_CLOSED, LiveTrade::STATUS_LIQUIDATED, LiveTrade::STATUS_CANCELLED])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('dashboard.live-trading.history', compact('openTrades', 'closedTrades'));
    }
}
