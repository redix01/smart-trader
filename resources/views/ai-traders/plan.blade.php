@extends('frontpage.nano.layout')
@section('title', $plan->name . ' — AI traders — Nano Market Pro')

@section('content')
<header class="page-head">
  <div class="wrap">
    <a href="{{ route('ai-traders.index') }}" class="back">← All AI traders</a>
    <p class="eyebrow">AI trader plan</p>
    <h1>{{ $plan->name }}</h1>
    <p class="lead">{{ $plan->description }}</p>
    <div class="stats" style="margin-top:36px;grid-template-columns:repeat(3,1fr)">
      <div class="stat"><div class="n">{{ $plan->formatted_price }}</div><div class="l">Monthly subscription</div></div>
      <div class="stat"><div class="n">{{ $plan->number_of_traders }}</div><div class="l">AI traders available</div></div>
      <div class="stat"><div class="n">${{ number_format($plan->investment_amount, 0) }}</div><div class="l">Minimum investment</div></div>
    </div>
  </div>
</header>

<section id="traders">
  <div class="wrap">
    <p class="eyebrow">Traders</p>
    <h2>Available AI traders.</h2>
    @if($traders->count() > 0)
      <div class="grid grid-3">
        @foreach($traders as $trader)
          <div class="card">
            <span class="k">{{ ucfirst($trader->trading_strategy) }}</span>
            <h3>{{ $trader->name }}</h3>
            <div class="tags" style="margin:0"><span class="badge">{{ $trader->ai_model }}</span><span class="badge badge-gold">{{ $trader->ai_confidence }}</span></div>
            <dl class="kv">
              <div><dt>Performance</dt><dd class="{{ $trader->current_performance >= 0 ? 'pos' : 'neg' }}">{{ $trader->formatted_performance }}</dd></div>
              <div><dt>Win rate</dt><dd>{{ $trader->formatted_win_rate }}</dd></div>
              <div><dt>Total trades</dt><dd>{{ $trader->total_trades }}</dd></div>
              <div><dt>Max positions</dt><dd>{{ $trader->max_positions }}</dd></div>
            </dl>
            <p class="small muted">Stop loss {{ $trader->stop_loss_percentage }}% · Take profit {{ $trader->take_profit_percentage }}% · Position size {{ $trader->position_size_percentage }}%</p>
            @if(!empty($trader->stocks_to_trade))
              <div class="tags" style="margin-top:14px">@foreach($trader->stocks_to_trade as $stock)<span class="badge">{{ $stock }}</span>@endforeach</div>
            @endif
            <span class="more"><a href="{{ route('ai-traders.trader', $trader) }}" class="btn btn-navy btn-block">View details</a></span>
          </div>
        @endforeach
      </div>
    @else
      <div class="callout"><p><strong>No AI traders available.</strong> This plan has no active AI traders at the moment.</p></div>
    @endif
  </div>
</section>

@if($plan->features)
<section class="tight">
  <div class="wrap">
    <p class="eyebrow">Included</p>
    <h2>Plan features.</h2>
    <div class="grid grid-3">
      @foreach($plan->features as $feature)
        <div class="card"><h3 style="margin-top:0;font-size:20px">{{ $feature }}</h3></div>
      @endforeach
    </div>
  </div>
</section>
@endif

@include('frontpage.nano._cta')
@endsection
