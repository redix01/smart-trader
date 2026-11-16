@extends('frontpage.prime.layout')
@section('title', 'Trade Stocks & ETFs — Prime Digital Capital')
@section('description', 'Trade leading US stocks and ETFs with live quotes, charts and a personal Trade Room on Prime Digital Capital.')

@section('content')
@include('frontpage.prime.page-hero', ['pill' => 'Stocks & ETFs', 'title' => 'Trade the world’s leading companies', 'lead' => 'Follow live quotes for top US equities and ETFs, and trade their price movements — rising or falling — from your personal Trade Room.', 'ctaRoute' => 'user.markets.stocks', 'ctaAuth' => 'View live stock prices'])

<section class="sec no-line" style="padding-top:80px">
  <div class="wrap">
    <div class="split">
      <div class="reveal">
        <span class="pill">Live quotes</span>
        <h2>Watch the market in real time</h2>
        <p>Stock CFDs let you trade on price movements without owning the underlying shares — so you can take a position whether you expect prices to rise or fall.</p>
        <ul class="checks">
          <li>Big tech, finance and index ETFs in one place</li>
          <li>Go long or short on price movements</li>
          <li>Charts, screeners and quotes built into your dashboard</li>
        </ul>
      </div>
      <div class="widget reveal" style="height:460px">
        <div class="tradingview-widget-container" style="height:100%"><div class="tradingview-widget-container__widget" style="height:100%"></div>
          <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-quotes.js" async>
          {"width":"100%","height":"100%","symbolsGroups":[{"name":"Tech","symbols":[{"name":"NASDAQ:AAPL","displayName":"Apple"},{"name":"NASDAQ:MSFT","displayName":"Microsoft"},{"name":"NASDAQ:NVDA","displayName":"Nvidia"},{"name":"NASDAQ:AMZN","displayName":"Amazon"},{"name":"NASDAQ:TSLA","displayName":"Tesla"},{"name":"NASDAQ:META","displayName":"Meta"}]},{"name":"ETFs","symbols":[{"name":"AMEX:SPY","displayName":"S&P 500 ETF"},{"name":"NASDAQ:QQQ","displayName":"Nasdaq 100 ETF"},{"name":"AMEX:DIA","displayName":"Dow Jones ETF"}]}],"showSymbolLogo":true,"isTransparent":true,"colorTheme":"dark","locale":"en","backgroundColor":"rgba(0,0,0,0)"}
          </script>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">Market map</span><h2>See the whole market at a glance</h2><p class="lead">S&amp;P 500 companies sized by market cap and coloured by today’s move.</p></div>
    <div class="widget reveal" style="height:560px">
      <div class="tradingview-widget-container" style="height:100%"><div class="tradingview-widget-container__widget" style="height:100%"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-stock-heatmap.js" async>
        {"exchanges":[],"dataSource":"SPX500","grouping":"sector","blockSize":"market_cap_basic","blockColor":"change","locale":"en","colorTheme":"dark","hasTopBar":false,"isDataSetEnabled":false,"isZoomEnabled":true,"hasSymbolTooltip":true,"isMonoSize":false,"width":"100%","height":"100%"}
        </script>
      </div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">Learn</span><h2>Stocks, explained</h2></div>
    <div class="cols3 reveal">
      <div><h3>What are stocks?</h3><p>Stocks — also called shares or equities — are issued by public companies to raise capital. After the IPO, they trade on the stock market between investors, and they entitle holders to a portion of company profits.</p></div>
      <div><h3>How do I trade them?</h3><p>Prices move as buyers and sellers agree on value. Traders aim to profit from those changes; long-term investors often hold a diverse portfolio and collect dividends.</p></div>
      <div><h3>What are the risks?</h3><p>Short-term price moves are hard to predict and no strategy is failsafe. Limit how much you put into a single trade as part of your own risk management.</p></div>
    </div>
  </div>
</section>

@include('frontpage.prime.cta', ['pill' => 'Stocks', 'title' => 'Trade stocks with Prime Digital Capital', 'lead' => 'Live quotes, charts and a personal Trade Room — in one account.'])
@endsection
