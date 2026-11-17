@extends('frontpage.prime.layout')
@section('title', 'About Us — Prime Digital Capital')
@section('description', 'Prime Digital Capital is an online trading platform for crypto, stocks and currencies, built around clarity, security and support.')

@section('content')
@include('frontpage.prime.page-hero', ['pill' => 'About us', 'title' => 'Honest, transparent trading technology', 'lead' => 'Prime Digital Capital is an online trading platform that gives you access to crypto, stocks and currencies from one secure account — with clear information about risk at every step.'])

<section class="sec no-line" style="padding-top:80px">
  <div class="wrap">
    <div class="kpis reveal">
      <div><b>3</b><span>Asset classes: crypto, stocks, forex</span></div>
      <div><b>24/7</b><span>Live-chat support</span></div>
      <div><b>Live</b><span>Tick-by-tick crypto prices</span></div>
      <div><b>1</b><span>Account for everything</span></div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="split">
      <div class="reveal">
        <span class="pill">Our mission</span>
        <h2>Make trading clearer, not louder</h2>
        <p>We keep the trading environment secure and the conditions straightforward, so you can focus on your decisions. We help traders build the knowledge and skills to trade efficiently and responsibly.</p>
        <p>Every client gets the same tools: live market prices, a personal Trade Room, copy trading and a dashboard that shows exactly where your money is.</p>
      </div>
      <div class="cols3 reveal" style="grid-template-columns:1fr">
        <div><div class="ft"><h3>Security first</h3></div><p>Account verification, encrypted connections and careful handling of your personal data.</p></div>
        <div><div class="ft"><h3>Clear about risk</h3></div><p>We say plainly that trading can lose money, and we never promise returns.</p></div>
        <div><div class="ft"><h3>Support that answers</h3></div><p>A real team on live chat and email, around the clock.</p></div>
      </div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal"><span class="pill">Why choose us</span><h2>What you get with Prime Digital Capital</h2></div>
    <div class="fgrid reveal">
      <div><div class="ft"><h3>One account</h3></div><p>Crypto, stocks and forex from a single login and wallet.</p></div>
      <div><div class="ft"><h3>No deposit fees</h3></div><p>We don’t charge commission on deposits or withdrawals.</p></div>
      <div><div class="ft"><h3>Fast onboarding</h3></div><p>A simple registration form and quick verification.</p></div>
      <div><div class="ft"><h3>Live prices</h3></div><p>Real-time quotes for every market in your dashboard.</p></div>
      <div><div class="ft"><h3>Copy trading</h3></div><p>Mirror experienced traders with your own allocation.</p></div>
      <div><div class="ft"><h3>Contact us</h3></div><p><a class="text-link" href="mailto:support@primedigitalcap.com">support@primedigitalcap.com</a></p></div>
    </div>
  </div>
</section>

@include('frontpage.prime.cta')
@endsection
