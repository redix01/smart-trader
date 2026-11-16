@extends('dashboard.layout.app')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0">
            <a href="{{ route('user.holding.index') }}" onclick="if (history.length > 1) { event.preventDefault(); history.back(); }" class="inline-flex items-center gap-1 min-h-[32px] text-sm font-semibold text-gray-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400 mt-1">Portfolio &middot; Chart</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1 truncate">{{ $asset->name }} <span class="text-gray-400 font-semibold">{{ $asset->symbol }}</span></h1>
            <p class="text-sm text-gray-400 mt-0.5">Live price chart with RSI, MACD and volume.</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-3 sm:p-4 min-w-0">
            <div class="text-xs text-gray-400">Price</div>
            <div class="mt-1 text-sm sm:text-xl font-semibold text-white font-mono break-all">{{ money_from($asset->current_price, 'USD', 8) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-3 sm:p-4 min-w-0">
            <div class="text-xs text-gray-400">24h change</div>
            <div class="mt-1 text-sm sm:text-xl font-semibold font-mono {{ $asset->price_change_24h >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                {{ $asset->price_change_24h >= 0 ? '+' : '' }}{{ number_format($asset->price_change_24h, 2) }}%
            </div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-3 sm:p-4 min-w-0">
            <div class="text-xs text-gray-400">Market cap</div>
            <div class="mt-1 text-sm sm:text-xl font-semibold text-white font-mono break-all">{{ money($asset->market_cap, 2) }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-700 bg-gray-800 p-2 sm:p-4">
        <div class="rounded-xl overflow-hidden bg-gray-900/50 h-[460px] sm:h-[600px]">
            <div id="tradingViewChart" class="w-full h-full"></div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const symbol = '{{ $asset->symbol }}';
    const assetName = '{{ $asset->name }}';
    const assetType = '{{ $asset->type }}';
    
    // Initialize TradingView chart
    initFullTradingViewChart(symbol, assetName, assetType);
});

function initFullTradingViewChart(symbol, assetName, assetType) {
    // Determine the correct symbol for TradingView
    let tvSymbol = getTradingViewSymbol(symbol, assetType);
    
    new TradingView.widget({
        "width": "100%",
        "height": "100%",
        "symbol": tvSymbol,
        "interval": "D",
        "timezone": "Etc/UTC",
        "theme": "dark",
        "style": "1",
        "locale": "en",
        "toolbar_bg": "#f1f3f6",
        "enable_publishing": false,
        "hide_side_toolbar": false,
        "allow_symbol_change": true,
        "container_id": "tradingViewChart",
        "studies": [
            "RSI@tv-basicstudies",
            "MACD@tv-basicstudies",
            "Volume@tv-basicstudies"
        ],
        "show_popup_button": true,
        "popup_width": "1000",
        "popup_height": "650"
    });
}

function getTradingViewSymbol(symbol, assetType) {
    // If it's a stock, use NASDAQ
    if (assetType === 'stock') {
        return `NASDAQ:${symbol}`;
    }
    
    // Crypto mappings
    const cryptoMappings = {
        'BTC': 'BINANCE:BTCUSD',
        'ETH': 'BINANCE:ETHUSD',
        'USDT': 'BINANCE:USDTUSD',
        'USDC': 'BINANCE:USDCUSD',
        'BNB': 'BINANCE:BNBUSD',
        'ADA': 'BINANCE:ADAUSD',
        'SOL': 'BINANCE:SOLUSD',
        'DOT': 'BINANCE:DOTUSD',
        'DOGE': 'BINANCE:DOGEUSD',
        'AVAX': 'BINANCE:AVAXUSD',
        'MATIC': 'BINANCE:MATICUSD',
        'LINK': 'BINANCE:LINKUSD',
        'UNI': 'BINANCE:UNIUSD',
        'LTC': 'BINANCE:LTCUSD',
        'BCH': 'BINANCE:BCHUSD',
        'XRP': 'BINANCE:XRPUSD',
        'ATOM': 'BINANCE:ATOMUSD',
        'FTM': 'BINANCE:FTMUSD',
        'NEAR': 'BINANCE:NEARUSD',
        'ALGO': 'BINANCE:ALGOUSD'
    };
    
    // Check if we have a specific crypto mapping
    if (cryptoMappings[symbol]) {
        return cryptoMappings[symbol];
    }
    
    // Default fallback for crypto
    return `BINANCE:${symbol}USD`;
}
</script>
@endsection
