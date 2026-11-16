@extends('frontpage.prime.layout')
@section('title', 'Help Center — Prime Digital Capital')
@section('description', 'Answers to common questions about Prime Digital Capital accounts, deposits, trading and crypto.')

@section('content')
@include('frontpage.prime.page-hero', ['pill' => 'Help center', 'title' => 'Frequently asked questions', 'lead' => 'Everything you need to know about the platform. Can’t find your answer? Email support@primedigitalcap.com or start a live chat.', 'noCta' => true])

@php
  $groups = [
    'Account' => [
      ['How do I open an account?', 'Click Get started, fill in the registration form and verify your email. You can then complete identity verification from your dashboard.'],
      ['Why do I need to verify my identity?', 'Verification protects your account and is part of our anti-money-laundering checks. It is required before withdrawals.'],
      ['How do I contact support?', 'Use live chat in your dashboard or email support@primedigitalcap.com. The team is available 24/7.'],
    ],
    'Deposits & withdrawals' => [
      ['How do I fund my account?', 'Open Deposit in your dashboard, choose a supported payment method and follow the instructions shown.'],
      ['Are there deposit fees?', 'We don’t charge commission on deposits or withdrawals. Your payment provider or network may charge its own fees.'],
      ['How do I withdraw?', 'Request a withdrawal from the Withdraw page in your dashboard. Requests are processed after review.'],
    ],
    'Trading' => [
      ['What is the Trade Room?', 'Your personal trading desk: choose an asset, set amount and leverage, add take-profit and stop-loss, and manage your open positions.'],
      ['Are prices live?', 'Yes. Crypto prices stream tick by tick and stock quotes update in real time.'],
      ['Is trading risky?', 'Yes. Trading can result in the loss of some or all of your capital, and leverage magnifies losses. Read our risk disclosure before you start.'],
    ],
    'Crypto basics' => [
      ['What is cryptocurrency?', 'A digital currency that uses cryptography to secure transactions. There is no central issuer; a decentralised network records transactions and issues new units.'],
      ['Who developed Bitcoin?', 'Bitcoin was designed by Satoshi Nakamoto, who described it in a 2008 white paper. It was the first successful digital currency built on trust in cryptography rather than central authorities.'],
      ['Can Bitcoin be regulated?', 'Changing the Bitcoin protocol needs the cooperation of nearly all its users, which makes it very hard for any single party to control. Exchanges and platforms that offer it are subject to local rules.'],
    ],
  ];
@endphp

<section class="sec no-line" style="padding-top:80px">
  <div class="wrap">
    @foreach($groups as $group => $items)
      <div class="sec-head reveal" style="margin-bottom:24px;{{ $loop->first ? '' : 'margin-top:64px' }}"><h2 style="font-size:28px">{{ $group }}</h2></div>
      <div class="faq reveal">
        @foreach($items as [$q, $a])
          <details><summary>{{ $q }}</summary><p>{{ $a }}</p></details>
        @endforeach
      </div>
    @endforeach
  </div>
</section>

@include('frontpage.prime.cta', ['pill' => 'Still have questions?', 'title' => 'Talk to our team', 'lead' => 'Email support@primedigitalcap.com or open an account and use live chat.'])
@endsection
