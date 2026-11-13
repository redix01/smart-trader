@extends('frontpage.nano.layout')
@section('title', 'FAQ — Nano Market Pro')

@php
  $faqs = [
    ['How do I open an account?', 'Register with your email and a secure password, verify your email with the code we send you, complete identity verification (KYC), then fund your account and start trading.'],
    ['Is there a fee to deposit or withdraw?', 'We do not charge commissions or fees on deposits and withdrawals. Your payment provider or network may apply its own charges.'],
    ['Can I lose more than I deposit?', 'Forex, crypto and CFD trading carry substantial risk and you can lose your invested capital. Never trade money you cannot afford to lose, and read the risk disclosure before you start.'],
    ['What is copy trading?', 'Copy trading lets you mirror the positions of a selected expert in your own account, scaled to the amount you allocate. You can change or stop an allocation at any time.'],
    ['What are AI traders?', 'AI traders execute predefined strategies consistently, with risk controls built into the process. They cannot remove the possibility of loss.'],
    ['What is cryptocurrency?', 'Cryptocurrency is digital currency that uses cryptography to secure transactions. It has no central issuing or regulating authority; transactions are recorded on a decentralised ledger called a blockchain.'],
    ['Who developed Bitcoin?', 'The original Bitcoin code was designed by Satoshi Nakamoto, who outlined the idea in a 2008 white paper. Nakamoto left the code to developers and the community in 2010, and hundreds of developers have contributed since.'],
    ['How do I contact support?', 'Email support@nanomarketpro.net. Our team is online 24/7.'],
  ];
@endphp

@section('content')
<header class="page-head">
  <div class="wrap">
    <p class="eyebrow">FAQ</p>
    <h1>Frequently asked <em>questions</em></h1>
    <p class="lead">Everything you need to know about the platform. Can't find your answer? <a href="mailto:support@nanomarketpro.net" class="text-link">Talk to our team</a>.</p>
  </div>
</header>

<section>
  <div class="wrap">
    <div class="faq">
      @foreach ($faqs as [$q, $a])
        <details @if($loop->first) open @endif><summary>{{ $q }}</summary><p>{{ $a }}</p></details>
      @endforeach
    </div>
  </div>
</section>

@include('frontpage.nano._cta')
@endsection
