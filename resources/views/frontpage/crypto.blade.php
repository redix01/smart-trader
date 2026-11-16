@extends('frontpage.prime.layout')
@section('title', 'Trade Crypto — Prime Digital Capital')
@section('description', 'Trade Bitcoin, Ethereum and other leading cryptocurrencies with live tick-by-tick prices, adjustable leverage and 24/7 support.')

@section('content')
@include('frontpage.prime.page-hero', ['pill' => 'Crypto trading', 'title' => 'Trade cryptocurrencies around the clock', 'lead' => 'Buy and sell Bitcoin, Ethereum and other leading coins from your personal Trade Room — with live prices, adjustable leverage and fast deposits and withdrawals.', 'ctaRoute' => 'user.liveTrading.index', 'ctaAuth' => 'Open the Trade Room'])

<section class="sec no-line" style="padding-top:80px">
  <div class="wrap">
    <div class="split">
      <div class="reveal">
        <span class="pill">Live market</span>
        <h2>Prices that move with the market</h2>
        <p>Crypto prices on Prime Digital Capital stream tick by tick, so what you see is what the market is doing right now.</p>
        <ul class="checks">
          <li>Trade 24 hours a day, 7 days a week</li>
          <li>Go long or short with adjustable leverage</li>
          <li>Set take-profit and stop-loss on every position</li>
        </ul>
      </div>
      <div class="panel reveal" style="margin-top:0">
        <h4>Markets <span class="mono" style="float:right;font-size:12px;color:var(--up)">● LIVE</span></h4>
        <table class="mtable">
          <thead><tr><th>Asset</th><th>Price (USD)</th><th>24h</th><th>Volume</th></tr></thead>
          <tbody>
          @foreach([['BTC','Bitcoin','₿','#f7931a'],['ETH','Ethereum','Ξ','#627eea'],['SOL','Solana','S','#14b8a6'],['BNB','BNB','B','#d4a017'],['XRP','XRP','X','#334155'],['DOGE','Dogecoin','Ð','#c2a633'],['ADA','Cardano','A','#2563eb'],['LINK','Chainlink','L','#2a5ada']] as [$s,$n,$g,$c])
            <tr><td><span class="asset"><span class="coin" style="background:{{ $c }}">{{ $g }}</span>{{ $s }}</span></td><td data-live-price="{{ $s }}">—</td><td data-live-chg="{{ $s }}">—</td><td data-live-vol="{{ $s }}" style="color:var(--text-3)">—</td></tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">Chart</span><h2>Analyse before you trade</h2><p class="lead">Full charting with indicators and drawing tools, built into the platform.</p></div>
    <div class="widget reveal" style="height:560px">
      <div class="tradingview-widget-container" style="height:100%"><div class="tradingview-widget-container__widget" style="height:100%"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
        {"autosize":true,"symbol":"BINANCE:BTCUSDT","interval":"60","timezone":"Etc/UTC","theme":"dark","style":"1","locale":"en","allow_symbol_change":true,"backgroundColor":"rgba(11,19,48,1)","gridColor":"rgba(170,190,255,0.06)","support_host":"https://www.tradingview.com"}
        </script>
      </div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">Learn</span><h2>Crypto, explained</h2></div>
    <div class="cols3 reveal">
      <div><h3>What is a cryptocurrency?</h3><p>A cryptocurrency like Bitcoin is a digital currency traded peer-to-peer on a blockchain, independent of central authorities such as banks and governments. It is not backed by a physical commodity.</p></div>
      <div><h3>How do cryptocurrencies work?</h3><p>A transfer request is validated by the network, grouped with other transactions into a block, and added to the blockchain. Once the block is added, the transaction is confirmed.</p></div>
      <div><h3>Is crypto an investment opportunity?</h3><p>Crypto is now traded by major financial institutions. Like any market it carries risk — prices can be highly volatile and move sharply in either direction.</p></div>
    </div>
  </div>
</section>

@include('frontpage.prime.cta', ['pill' => 'Crypto', 'title' => 'Start trading crypto today', 'lead' => 'Open an account, fund your wallet and place your first trade in minutes.'])
@endsection

@include('frontpage.prime.live-crypto')
