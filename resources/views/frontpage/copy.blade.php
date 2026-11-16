@extends('frontpage.prime.layout')
@section('title', 'Copy Trading — Prime Digital Capital')
@section('description', 'Automatically copy the trades of experienced traders on Prime Digital Capital, with your own allocation and limits.')

@section('content')
@include('frontpage.prime.page-hero', ['pill' => 'Copy trading', 'title' => 'Copy trades from experienced traders', 'lead' => 'Choose a trader, decide how much to allocate, and every trade they make is mirrored in your account automatically and in real time.', 'ctaRoute' => 'user.copyTrading.index', 'ctaAuth' => 'Browse traders'])

<section class="sec no-line" style="padding-top:80px">
  <div class="wrap">
    <div class="split">
      <div class="reveal">
        <span class="pill">How it works</span>
        <h2>One choice, then it runs itself</h2>
        <p>Once you pick a trader to copy, that is the only action needed. There is no code to run and no signals to enter by hand — the platform copies each trade on your behalf.</p>
        <ul class="checks">
          <li>You set the amount you allocate to each trader</li>
          <li>Trades are mirrored as they happen</li>
          <li>Stop copying at any time from your dashboard</li>
        </ul>
      </div>
      <div class="panel reveal" style="margin-top:0">
        <div class="trader-hd">
          <div class="av">PD</div>
          <div><b>Strategy provider</b><small>Example profile</small>
            <div class="trader-meta"><span>360 watchers</span><span>69.6K followers</span></div>
          </div>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm" style="margin-left:auto">Copy</a>
        </div>
        <div class="tstats">
          <div><small>Win rate</small><b class="up">54.81%</b></div>
          <div><small>Profit share</small><b>43.3%</b></div>
          <div><small>Trades</small><b>578</b></div>
          <div><small>Wins</small><b>28</b></div>
          <div><small>Losses</small><b>0</b></div>
          <div><small>Min. startup</small><b>$5,000</b></div>
        </div>
        <p class="note">Illustrative profile. Past performance is not indicative of future results.</p>
      </div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">Three steps</span><h2>Start copying in minutes</h2></div>
    <div class="steps">
      <div class="step reveal"><span class="n">01</span><h3>Pick a trader</h3><p>Compare win rate, trade history, profit share and minimum startup on each trader’s profile.</p></div>
      <div class="step reveal"><span class="n">02</span><h3>Set your allocation</h3><p>Decide how much to commit. Your copied positions are sized in proportion to it.</p></div>
      <div class="step reveal"><span class="n">03</span><h3>Track and adjust</h3><p>Follow results from your dashboard and stop copying whenever you choose.</p></div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">FAQ</span><h2>About our traders</h2></div>
    <div class="faq reveal">
      <details><summary>Who are the traders I can copy?</summary><p>We review applicants and look at their trading performance over time before listing them. You can read about each trader on their own performance page.</p></details>
      <details><summary>Do I need to do anything after I start copying?</summary><p>No. Once you have selected a trader and set your allocation, trades are copied automatically. You can stop at any time.</p></details>
      <details><summary>Can I lose money copy trading?</summary><p>Yes. Copying a trader means copying their losses as well as their gains. Only allocate what you can afford to lose and read our <a href="{{ route('risk') }}" class="text-link">risk disclosure</a>.</p></details>
    </div>
  </div>
</section>

@include('frontpage.prime.cta', ['pill' => 'Copy trading', 'title' => 'Find a trader to copy', 'lead' => 'Create an account to browse traders and start copying.'])
@endsection
