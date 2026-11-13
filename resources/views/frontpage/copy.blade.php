@extends('frontpage.nano.layout')
@section('title', 'Copy trading — Nano Market Pro')

@section('content')
<header class="page-head">
  <div class="wrap">
    <p class="eyebrow">Copy trading</p>
    <h1>Follow experienced traders, <em>automatically</em></h1>
    <p class="lead">Choose an expert, decide how much to allocate, and your account mirrors their positions in real time — with your own limits in place.</p>
  </div>
</header>

<section>
  <div class="wrap">
    <p class="eyebrow">How it works</p>
    <h2>Three steps to start copying.</h2>
    <div class="steps3">
      <div class="card"><span class="k">Step 1</span><h3>Choose an expert</h3><p>Browse our selected traders and read each expert's individual performance page.</p></div>
      <div class="card"><span class="k">Step 2</span><h3>Set your allocation</h3><p>Decide how much of your account to allocate. You can change or stop it at any time.</p></div>
      <div class="card"><span class="k">Step 3</span><h3>Trades are copied</h3><p>When your expert opens or closes a position, the same trade is placed in your account, scaled to your allocation.</p></div>
    </div>
  </div>
</section>

<section class="tight">
  <div class="wrap split split-top">
    <div>
      <p class="eyebrow">The experts</p>
      <p class="statement" style="margin-top:18px">We select experts carefully. <em>You decide who to follow.</em></p>
    </div>
    <div class="prose">
      <p>We get to know each applicant as a trader and review their performance over a period of time. We also look for experts who already have a following, as further evidence of their competence.</p>
      <p>The copier works on a percentage basis. If an expert opens a position worth 10% of their account and you are fully allocated to them, your account opens a position worth 10% of yours. When they exit, you exit.</p>
      <p>Keep enough available balance to cover each trade and meet the minimum order size, otherwise some trades may be missed.</p>
    </div>
  </div>
</section>

<section class="tight">
  <div class="wrap">
    <div class="callout"><p>Copying a trader does not guarantee profits. Their past results are not a guide to future performance, and you can lose capital. <a href="{{ route('risk') }}" class="text-link">Read the risk disclosure</a>.</p></div>
  </div>
</section>

@include('frontpage.nano._cta')
@endsection
