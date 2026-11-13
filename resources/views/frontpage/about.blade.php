@extends('frontpage.nano.layout')
@section('title', 'About — Nano Market Pro')

@section('content')
<header class="page-head">
  <div class="wrap">
    <p class="eyebrow">About us</p>
    <h1>Honesty and transparency in <em>trading technology</em></h1>
    <p class="lead">Nano Market Pro is an online trading platform offering round-the-clock access to forex, crypto, stocks and automated trading tools.</p>
  </div>
</header>

<section>
  <div class="wrap split split-top">
    <div>
      <p class="eyebrow">Who we are</p>
      <p class="statement" style="margin-top:18px">Intelligent trading. <em>Innovative technology.</em></p>
    </div>
    <div class="prose">
      <p>We maintain high standards to keep trading environments secure, and we aim to offer clear, tailored trading conditions for every client. We help traders build the knowledge and skills they need to trade efficiently and responsibly.</p>
      <p>Our daily market analysis brings fresh market insight to clients every day, alongside copy trading, AI traders and signals.</p>
    </div>
  </div>
</section>

<section class="tight">
  <div class="wrap">
    <p class="eyebrow">Why Nano Market Pro</p>
    <h2>Built for clarity.</h2>
    <div class="grid">
      <div class="card"><span class="k">01</span><h3>One trading account</h3><p>A single account type keeps things simple, with every product available from one dashboard.</p></div>
      <div class="card"><span class="k">02</span><h3>No deposit fees</h3><p>We do not charge commissions or fees on your deposits and withdrawals.</p></div>
      <div class="card"><span class="k">03</span><h3>Industry standards</h3><p>We respect industry standards, including AML and KYC checks that protect every client's funds.</p></div>
      <div class="card"><span class="k">04</span><h3>Fast onboarding</h3><p>A simple registration form and quick verification get you started in minutes.</p></div>
    </div>
  </div>
</section>

<section class="tight">
  <div class="wrap">
    <div class="founder">
      <img src="https://thomaspeggs.com/assets/thomas-peggs.webp" alt="Thomas Peggs, founder of Nano Market Pro" width="660" height="880" loading="lazy">
      <div>
        <p class="eyebrow">Founder</p>
        <h3>Thomas Peggs</h3>
        <p class="role">Founder, Nano Market Pro</p>
        <p>Thomas Peggs brings hands-on experience scaling Forex and CFD trading infrastructure — from low-latency execution and MetaTrader ecosystems to global compliance frameworks.</p>
        <a href="https://thomaspeggs.com/" class="btn btn-navy" rel="noopener">Read Thomas Peggs' profile</a>
      </div>
    </div>
  </div>
</section>

@include('frontpage.nano._cta')
@endsection
