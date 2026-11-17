@extends('frontpage.prime.layout')
@section('title', $trader->name . ' — AI trader — Prime Digital Capital')

@section('content')
<header class="page-head">
  <div class="wrap">
    @if($trader->aiTraderPlan)
      <a href="{{ route('ai-traders.plan', $trader->aiTraderPlan) }}" class="back">← {{ $trader->aiTraderPlan->name }}</a>
    @else
      <a href="{{ route('ai-traders.index') }}" class="back">← All AI traders</a>
    @endif
    <p class="eyebrow">AI trader</p>
    <h1>{{ $trader->name }}</h1>
    <p class="lead">AI-assisted stock trading with {{ $trader->ai_model }}.</p>
    <div class="tags" style="margin-top:20px">
      <span class="badge">{{ $trader->ai_model }}</span>
      <span class="badge">{{ ucfirst($trader->trading_strategy) }}</span>
      <span class="badge">{{ ucfirst($trader->ai_confidence) }} confidence</span>
      <span class="badge">{{ ucfirst($trader->ai_learning_mode) }} learning</span>
    </div>
    <div class="stats" style="margin-top:36px;grid-template-columns:repeat(2,1fr)">
      <div class="stat"><div class="n {{ $trader->current_performance >= 0 ? 'pos' : 'neg' }}">{{ $trader->formatted_performance }}</div><div class="l">Current performance</div></div>
      <div class="stat"><div class="n">{{ $trader->formatted_win_rate }}</div><div class="l">Win rate</div></div>
    </div>
  </div>
</header>

<section>
  <div class="wrap">
    <p class="eyebrow">Performance</p>
    <h2>Performance overview.</h2>
    <div class="card" style="margin-top:32px"><div style="position:relative;height:320px"><canvas id="performanceChart"></canvas></div><p id="chartMsg" class="small muted" hidden>Performance history is not available right now.</p></div>
  </div>
</section>

<section class="tight">
  <div class="wrap split split-top">
    <div class="stack">
      <div class="card">
        <h3 style="margin-top:0">Trading configuration</h3>
        <div>
          <dl style="display:grid;grid-template-columns:1fr auto;gap:10px 16px;font-size:15px">
            <dt class="muted">AI model</dt><dd>{{ $trader->ai_model }}</dd>
            <dt class="muted">Confidence level</dt><dd>{{ ucfirst($trader->ai_confidence) }}</dd>
            <dt class="muted">Learning mode</dt><dd>{{ ucfirst($trader->ai_learning_mode) }}</dd>
            <dt class="muted">Strategy</dt><dd>{{ ucfirst($trader->trading_strategy) }}</dd>
            <dt class="muted">Risk tolerance</dt><dd>{{ $trader->risk_tolerance }}</dd>
            <dt class="muted">Max positions</dt><dd>{{ $trader->max_positions }}</dd>
          </dl>
        </div>
      </div>
      <div class="card">
        <h3 style="margin-top:0">Risk management</h3>
        <dl class="kv" style="grid-template-columns:repeat(3,1fr)">
          <div><dt>Stop loss</dt><dd>{{ $trader->stop_loss_percentage }}%</dd></div>
          <div><dt>Take profit</dt><dd>{{ $trader->take_profit_percentage }}%</dd></div>
          <div><dt>Position size</dt><dd>{{ $trader->position_size_percentage }}%</dd></div>
        </dl>
        @if(!empty($trader->stocks_to_trade))
          <p class="small muted">Stocks traded</p>
          <div class="tags" style="margin-top:8px">@foreach($trader->stocks_to_trade as $stock)<span class="badge">{{ $stock }}</span>@endforeach</div>
        @endif
      </div>
    </div>

    <div class="stack">
      <div class="card">
        <h3 style="margin-top:0">Trading statistics</h3>
        <dl style="display:grid;grid-template-columns:1fr auto;gap:10px 16px;font-size:15px">
          <dt class="muted">Total trades</dt><dd>{{ $trader->total_trades }}</dd>
          <dt class="muted">Winning trades</dt><dd>{{ $trader->winning_trades }}</dd>
          <dt class="muted">Losing trades</dt><dd>{{ $trader->total_trades - $trader->winning_trades }}</dd>
          <dt class="muted">Win rate</dt><dd>{{ $trader->formatted_win_rate }}</dd>
          <dt class="muted">Current performance</dt><dd class="{{ $trader->current_performance >= 0 ? 'pos' : 'neg' }}">{{ $trader->formatted_performance }}</dd>
        </dl>
      </div>
      <div class="card">
        <h3 style="margin-top:0">Plan</h3>
        <dl style="display:grid;grid-template-columns:1fr auto;gap:10px 16px;font-size:15px">
          <dt class="muted">Plan</dt><dd>{{ $trader->aiTraderPlan ? $trader->aiTraderPlan->name : 'No plan' }}</dd>
          <dt class="muted">Monthly cost</dt><dd>{{ $trader->aiTraderPlan ? $trader->aiTraderPlan->formatted_price : 'N/A' }}</dd>
          <dt class="muted">Minimum investment</dt><dd>{{ $trader->aiTraderPlan ? '$'.number_format($trader->aiTraderPlan->investment_amount, 0) : 'N/A' }}</dd>
        </dl>
        <span class="more" style="display:block;margin-top:22px">
          @auth
            <a href="{{ route('user.dashboard') }}" class="btn btn-navy btn-block">Subscribe from your dashboard</a>
          @else
            <a href="{{ route('register') }}" class="btn btn-navy btn-block">Open an account to subscribe</a>
          @endauth
        </span>
      </div>
    </div>
  </div>
</section>

@if($similarTraders->count() > 0)
<section class="tight">
  <div class="wrap">
    <p class="eyebrow">Compare</p>
    <h2>Similar AI traders.</h2>
    <div class="grid grid-3">
      @foreach($similarTraders as $similarTrader)
        <div class="card">
          <span class="k">{{ ucfirst($similarTrader->trading_strategy) }}</span>
          <h3>{{ $similarTrader->name }}</h3>
          <dl class="kv">
            <div><dt>Performance</dt><dd class="{{ $similarTrader->current_performance >= 0 ? 'pos' : 'neg' }}">{{ $similarTrader->formatted_performance }}</dd></div>
            <div><dt>Win rate</dt><dd>{{ $similarTrader->formatted_win_rate }}</dd></div>
          </dl>
          <span class="more"><a href="{{ route('ai-traders.trader', $similarTrader) }}" class="btn btn-line btn-block">View details</a></span>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="tight">
  <div class="wrap"><div class="callout"><p>Displayed performance is historical and does not guarantee future results. <a href="{{ route('risk') }}" class="text-link">Read the risk disclosure</a>.</p></div></div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var ctx = document.getElementById('performanceChart').getContext('2d');
  fetch('{{ route("ai-traders.performance", $trader) }}')
    .then(function (r) { if (!r.ok) throw new Error('bad response'); return r.json(); })
    .then(function (data) {
      (data.datasets || []).forEach(function (d) { d.borderColor = d.borderColor || '#b08a3e'; });
      new Chart(ctx, {
        type: 'line', data: data,
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false }, title: { display: true, text: '8-week performance history' } },
          scales: { y: { grid: { color: '#e3ddcf' } }, x: { grid: { display: false } } },
          elements: { point: { radius: 3, hoverRadius: 5 }, line: { tension: .35 } }
        }
      });
    })
    .catch(function () {
      document.getElementById('performanceChart').hidden = true;
      document.getElementById('chartMsg').hidden = false;
    });
});
</script>
@endpush
