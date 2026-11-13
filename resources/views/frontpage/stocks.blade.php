@extends('frontpage.nano.layout')
@section('title', 'Stock trading — Nano Market Pro')

@section('content')
<header class="page-head">
  <div class="wrap">
    <p class="eyebrow">Markets</p>
    <h1>Stock <em>trading</em></h1>
    <p class="lead">Trade the price movements of global shares as CFDs, whether prices are rising or falling.</p>
    @include('frontpage.nano._markets-nav')
  </div>
</header>

<section>
  <div class="wrap split split-top">
    <div class="stack">
      <div class="card"><span class="k">01</span><h3>Trade up or down</h3><p>A CFD lets you speculate on a share's price movement in either direction without owning the underlying asset.</p></div>
      <div class="card"><span class="k">02</span><h3>Global companies</h3><p>Access well-known listed companies alongside your currency and crypto positions.</p></div>
      <div class="card"><span class="k">03</span><h3>Limit your exposure</h3><p>Size each trade carefully. There is no failsafe strategy, so risk management matters more than prediction.</p></div>
    </div>
    <div class="doc">
      <h2>What is a stock CFD?</h2>
      <p>A CFD, or contract for difference, is a financial instrument that lets you trade on the price movements of stocks without actually owning them. You can speculate on prices going up or down.</p>
      <h2>What are stocks?</h2>
      <p>Stocks, also called equities or shares, are issued by public companies. Companies first sell shares in an initial public offering to raise capital; afterwards the shares trade between investors on the stock market. The value of a share reflects the market's view of the company, including its share of profits.</p>
      <h2>How does trading work?</h2>
      <p>A stock market is where buyers and sellers agree on a price. Shares are bought from an existing shareholder, not from the company, and sold to another investor. People buy and sell for different reasons: to take profit, to cut losses, or because they expect the price to change.</p>
      <p>Short-term price movements are very hard to predict. Many investors hold a diversified portfolio over the long term, but with CFDs you do not own the shares and receive no ownership rights.</p>
      <h2>Managing risk</h2>
      <p>All financial trading carries risk, and even experienced traders cannot predict every move. Limit the amount you put into any single trade and never trade money you cannot afford to lose.</p>
      <div class="callout"><p>CFDs are complex instruments and come with a high risk of losing money rapidly due to leverage. <a href="{{ route('risk') }}" class="text-link">Read the risk disclosure</a>.</p></div>
    </div>
  </div>
</section>

@include('frontpage.nano._cta')
@endsection
