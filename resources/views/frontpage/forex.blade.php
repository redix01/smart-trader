@extends('frontpage.nano.layout')
@section('title', 'Forex trading — Nano Market Pro')

@section('content')
<header class="page-head">
  <div class="wrap">
    <p class="eyebrow">Markets</p>
    <h1>Forex <em>trading</em></h1>
    <p class="lead">Trade currency pairs on the world's largest and most liquid financial market, with tight spreads and fast, consistent execution.</p>
    @include('frontpage.nano._markets-nav')
  </div>
</header>

<section>
  <div class="wrap split split-top">
    <div class="stack">
      <div class="card"><span class="k">01</span><h3>Open 24 hours</h3><p>From Sunday evening to Friday evening (US Eastern), following the sun from Australasia to Europe to North America.</p></div>
      <div class="card"><span class="k">02</span><h3>Major, minor &amp; exotic pairs</h3><p>Speculate on price movements between currencies and profit from the difference between buying and selling prices.</p></div>
      <div class="card"><span class="k">03</span><h3>Leverage with limits</h3><p>Trade larger positions than your capital allows. Leverage magnifies losses as well as gains.</p></div>
    </div>
    <div class="doc">
      <h2>What is forex?</h2>
      <p>Forex is short for foreign exchange. It is the place where currencies are traded, and the basis of the market is the fluctuation of exchange rates. Forex traders speculate on the price movements of currency pairs.</p>
      <h2>What is margin?</h2>
      <p>Margin is the amount of your funds required to open a new position. It is based on the size of your trade, measured in lots: a standard lot is 100,000 units, with mini (10,000), micro (1,000) and nano (100) lots available. The larger the lot, the larger the margin.</p>
      <h2>What is leverage?</h2>
      <p>Leverage lets you hold a position larger than your own capital. At 1:100, a $1,000 deposit controls a position worth $100,000. It can increase your potential gains, but it magnifies your potential losses and puts your capital at risk.</p>
      <h2>When is the market open?</h2>
      <p>Because of time zones, the forex market runs 24 hours a day from 5 p.m. Eastern on Sunday to 4 p.m. Eastern on Friday, except holidays. When one region closes, another opens.</p>
      <div class="callout"><p>Forex and CFD trading carry substantial risk and may result in the loss of your invested capital. <a href="{{ route('risk') }}" class="text-link">Read the risk disclosure</a>.</p></div>
    </div>
  </div>
</section>

@include('frontpage.nano._cta')
@endsection
