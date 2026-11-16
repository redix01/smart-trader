@extends('dashboard.layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Markets</p>
            <h1 class="text-2xl font-bold text-white mt-1">Stock Prices</h1>
            <p class="text-gray-400 mt-1">Live quotes for leading US equities and ETFs. Select a symbol to open its chart.</p>
        </div>
        <a href="{{ route('user.liveTrading.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Open Trade Room →</a>
    </div>

    {{-- Ticker tape --}}
    <div class="rounded-xl overflow-hidden border border-gray-700 bg-gray-800">
        <div class="tradingview-widget-container"><div class="tradingview-widget-container__widget"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
            {"symbols":[{"proName":"FOREXCOM:SPXUSD","title":"S&P 500"},{"proName":"FOREXCOM:NSXUSD","title":"Nasdaq 100"},{"proName":"NASDAQ:AAPL","title":"Apple"},{"proName":"NASDAQ:NVDA","title":"Nvidia"},{"proName":"NASDAQ:TSLA","title":"Tesla"},{"proName":"NASDAQ:MSFT","title":"Microsoft"},{"proName":"NASDAQ:AMZN","title":"Amazon"}],"showSymbolLogo":true,"isTransparent":true,"displayMode":"adaptive","colorTheme":"dark","locale":"en"}
            </script>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        {{-- Live quotes --}}
        <div class="xl:col-span-2 rounded-xl overflow-hidden border border-gray-700 bg-gray-800" style="min-height:620px">
            <div class="tradingview-widget-container" style="height:620px"><div class="tradingview-widget-container__widget" style="height:100%"></div>
                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-quotes.js" async>
                {"width":"100%","height":"100%","symbolsGroups":[
                  {"name":"Tech","symbols":[{"name":"NASDAQ:AAPL","displayName":"Apple"},{"name":"NASDAQ:MSFT","displayName":"Microsoft"},{"name":"NASDAQ:NVDA","displayName":"Nvidia"},{"name":"NASDAQ:GOOGL","displayName":"Alphabet"},{"name":"NASDAQ:AMZN","displayName":"Amazon"},{"name":"NASDAQ:META","displayName":"Meta"},{"name":"NASDAQ:TSLA","displayName":"Tesla"},{"name":"NASDAQ:NFLX","displayName":"Netflix"},{"name":"NASDAQ:ADBE","displayName":"Adobe"}]},
                  {"name":"Finance","symbols":[{"name":"NYSE:JPM","displayName":"JPMorgan"},{"name":"NYSE:V","displayName":"Visa"},{"name":"NYSE:MA","displayName":"Mastercard"},{"name":"NYSE:GS","displayName":"Goldman Sachs"},{"name":"NYSE:BAC","displayName":"Bank of America"}]},
                  {"name":"ETFs","symbols":[{"name":"AMEX:SPY","displayName":"SPDR S&P 500"},{"name":"NASDAQ:QQQ","displayName":"Invesco QQQ"},{"name":"AMEX:DIA","displayName":"SPDR Dow Jones"},{"name":"AMEX:IWM","displayName":"iShares Russell 2000"}]}
                ],"showSymbolLogo":true,"isTransparent":true,"colorTheme":"dark","locale":"en","backgroundColor":"rgba(0,0,0,0)"}
                </script>
            </div>
        </div>

        {{-- Chart --}}
        <div class="xl:col-span-3 rounded-xl overflow-hidden border border-gray-700 bg-gray-800" style="min-height:620px">
            <div class="tradingview-widget-container" style="height:620px"><div class="tradingview-widget-container__widget" style="height:100%"></div>
                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                {"autosize":true,"symbol":"NASDAQ:AAPL","interval":"60","timezone":"Etc/UTC","theme":"dark","style":"1","locale":"en","allow_symbol_change":true,"hide_side_toolbar":false,"backgroundColor":"rgba(10,12,20,1)","gridColor":"rgba(255,255,255,0.04)","support_host":"https://www.tradingview.com"}
                </script>
            </div>
        </div>
    </div>

    {{-- Screener --}}
    <div class="rounded-xl overflow-hidden border border-gray-700 bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-700"><h3 class="text-lg font-semibold text-white">US stock screener</h3><p class="text-sm text-gray-400">Sort by price, change, volume and market cap.</p></div>
        <div class="tradingview-widget-container" style="height:560px"><div class="tradingview-widget-container__widget" style="height:100%"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-screener.js" async>
            {"width":"100%","height":"100%","defaultColumn":"overview","defaultScreen":"most_capitalized","market":"america","showToolbar":true,"colorTheme":"dark","isTransparent":true,"locale":"en"}
            </script>
        </div>
    </div>
</div>
@endsection
