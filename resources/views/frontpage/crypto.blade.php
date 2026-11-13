@extends('frontpage.nano.layout')
@section('title', 'Crypto trading — Nano Market Pro')

@section('content')
<header class="page-head">
  <div class="wrap">
    <p class="eyebrow">Markets</p>
    <h1>Crypto <em>trading</em></h1>
    <p class="lead">Trade Bitcoin, Ethereum and other leading cryptocurrencies alongside your forex and stock positions, in one account.</p>
    @include('frontpage.nano._markets-nav')
  </div>
</header>

<section>
  <div class="wrap split split-top">
    <div class="stack">
      <div class="card"><span class="k">01</span><h3>One account</h3><p>Hold crypto, currency and stock positions together and track them from a single dashboard.</p></div>
      <div class="card"><span class="k">02</span><h3>Trade both directions</h3><p>Go long or short with leverage where available, and manage risk with the tools in your dashboard.</p></div>
      <div class="card"><span class="k">03</span><h3>24/7 markets, 24/7 support</h3><p>Crypto never closes. Our support team is available around the clock.</p></div>
    </div>
    <div class="doc">
      <h2>What is a cryptocurrency?</h2>
      <p>A cryptocurrency such as Bitcoin is a digital currency traded peer-to-peer on a blockchain, independent of central authorities such as banks and governments. Cryptocurrencies are entirely virtual and are not backed by physical commodities.</p>
      <h2>How do cryptocurrencies work?</h2>
      <p>Cryptocurrencies rely on blockchain technology. A transfer request is validated by the network, added to a pool of other transactions to form a block, and entered into the existing chain. Once the block is added, the transaction is complete.</p>
      <h2>Is it an investment opportunity?</h2>
      <p>Cryptocurrencies have become established assets among many financial institutions. As with any investment there are risks: prices are highly volatile and move with market sentiment and economic events.</p>
      <div class="callout"><p>Crypto prices can fall as quickly as they rise, and you may lose all of the capital you invest. <a href="{{ route('risk') }}" class="text-link">Read the risk disclosure</a>.</p></div>
    </div>
  </div>
</section>

@include('frontpage.nano._cta')
@endsection
