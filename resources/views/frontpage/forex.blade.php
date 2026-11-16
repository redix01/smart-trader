@extends('frontpage.prime.layout')
@section('title', 'Trade Forex — Prime Digital Capital')
@section('description', 'Trade major and minor currency pairs on the world’s largest financial market with Prime Digital Capital.')

@section('content')
@include('frontpage.prime.page-hero', ['pill' => 'Forex trading', 'title' => 'Trade the world’s largest market', 'lead' => 'Speculate on major and minor currency pairs in a market that trades around $6.6 trillion a day and is open 24 hours a day, five days a week.'])

<section class="sec no-line" style="padding-top:80px">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">Live rates</span><h2>Cross rates, updated live</h2><p class="lead">Compare the major currencies against each other at a glance.</p></div>
    <div class="widget reveal" style="height:420px">
      <div class="tradingview-widget-container" style="height:100%"><div class="tradingview-widget-container__widget" style="height:100%"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-forex-cross-rates.js" async>
        {"width":"100%","height":"100%","currencies":["EUR","USD","JPY","GBP","CHF","AUD","CAD","NZD"],"isTransparent":true,"colorTheme":"dark","locale":"en","backgroundColor":"rgba(0,0,0,0)"}
        </script>
      </div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="kpis reveal">
      <div><b>$6.6T</b><span>Average daily turnover (BIS, 2019)</span></div>
      <div><b>24/5</b><span>Sunday 5pm to Friday 4pm EST</span></div>
      <div><b>3</b><span>Lot sizes: standard, mini, micro</span></div>
      <div><b>Long / short</b><span>Trade rising or falling rates</span></div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">Learn</span><h2>Forex, explained</h2></div>
    <div class="cols3 reveal">
      <div><h3>What is margin?</h3><p>Margin is the amount of your funds needed to open a position. It depends on trade size, measured in lots — a standard lot is 100,000 units, with mini (10,000) and micro (1,000) lots also available.</p></div>
      <div><h3>What is leverage?</h3><p>Leverage lets you open positions larger than your capital. At 1:100, a $1,000 deposit controls a $100,000 position. It magnifies losses just as much as gains.</p></div>
      <div><h3>When is the market open?</h3><p>Forex trades 24 hours a day from Sunday 5pm to Friday 4pm EST, moving from Australasia to Europe to North America as each session opens.</p></div>
    </div>
  </div>
</section>

@include('frontpage.prime.cta', ['pill' => 'Forex', 'title' => 'Start trading currencies', 'lead' => 'Open an account and trade major currency pairs from your dashboard.'])
@endsection
