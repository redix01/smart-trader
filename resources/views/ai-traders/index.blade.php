@extends('frontpage.nano.layout')
@section('title', 'AI traders — Nano Market Pro')

@section('content')
<header class="page-head">
  <div class="wrap">
    <p class="eyebrow">AI traders</p>
    <h1>AI-assisted trading, <em>with risk controls</em></h1>
    <p class="lead">Technology that executes predefined strategies consistently, around the clock. Choose a plan, pick a trader, and keep your own limits in place.</p>
    <div class="tags" style="margin-top:24px">
      <span class="badge">24/7 execution</span><span class="badge">Real-time analysis</span><span class="badge">Built-in stop-loss &amp; take-profit</span>
    </div>
  </div>
</header>

<section id="plans">
  <div class="wrap">
    <p class="eyebrow">Plans</p>
    <h2>Choose your plan.</h2>
    <div class="grid grid-3">
      @foreach($plans as $plan)
        <div class="card">
          <span class="k">{{ $plan->number_of_traders }} AI trader{{ $plan->number_of_traders > 1 ? 's' : '' }}</span>
          <h3>{{ $plan->name }}</h3>
          <p>{{ $plan->description }}</p>
          <dl class="kv">
            <div><dt>Price</dt><dd>{{ $plan->formatted_price }}</dd></div>
            <div><dt>Min. investment</dt><dd>${{ number_format($plan->investment_amount, 0) }}</dd></div>
          </dl>
          @if(!empty($plan->stocks_trading))
            <p class="small muted">Markets covered</p>
            <div class="tags" style="margin-top:8px">
              @foreach($plan->stocks_trading as $stock)<span class="badge">{{ $stock }}</span>@endforeach
            </div>
          @endif
          @if($plan->features)
            <ul class="small" style="margin:18px 0 0;padding-left:18px;color:var(--soft)">
              @foreach($plan->features as $feature)<li>{{ $feature }}</li>@endforeach
            </ul>
          @endif
          <span class="more"><a href="{{ route('ai-traders.plan', $plan) }}" class="btn btn-navy btn-block">View AI traders</a></span>
        </div>
      @endforeach
    </div>
  </div>
</section>

<section class="dark">
  <div class="wrap">
    <p class="eyebrow">How it works</p>
    <h2>Three steps, fully visible.</h2>
    <div class="flow" style="grid-template-columns:repeat(3,1fr)">
      <div class="step"><span class="i">01</span><h3>Analysis</h3><p>Models analyse market data and indicators in real time.</p></div>
      <div class="step"><span class="i">02</span><h3>Execution</h3><p>Trades follow predefined strategies and risk parameters.</p></div>
      <div class="step"><span class="i">03</span><h3>Monitoring</h3><p>Track performance from your dashboard and adjust at any time.</p></div>
    </div>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="callout"><p>AI traders do not guarantee profits. Past performance is not a guide to future results and you may lose capital. <a href="{{ route('risk') }}" class="text-link">Read the risk disclosure</a>.</p></div>
  </div>
</section>

@include('frontpage.nano._cta')
@endsection
