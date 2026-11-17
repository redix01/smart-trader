@php
  $copyTraders = $copyTraders ?? (function () { try { return \App\Models\CopyTrader::where('status', 'active')->orderByDesc('win_rate')->take(3)->get(); } catch (\Throwable $e) { return collect(); } })();
  $money = $money ?? fn ($v) => '$'.number_format((float) $v, ((float) $v == floor((float) $v)) ? 0 : 2);
  $go = $go ?? fn ($userRoute) => auth()->check() ? route($userRoute) : route('register');
@endphp
    @if($copyTraders->isNotEmpty())
      <div class="cards3">
        @foreach($copyTraders as $t)
          @php $total = max(1, $t->win + $t->loss); @endphp
          <div class="tcard reveal">
            <div class="trader-hd">
              <div class="av">{{ collect(explode(' ', $t->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}</div>
              <div><b>{{ $t->name }}</b><small>{{ number_format($total) }} trades</small></div>
            </div>
            <div class="wr"><div class="row"><span>Win rate</span><b class="up">{{ $t->win_rate }}%</b></div><div class="track"><i style="width:{{ min(100, $t->win_rate) }}%"></i></div></div>
            <div class="tstats" style="margin-top:20px">
              <div><small>Wins</small><b>{{ number_format($t->win) }}</b></div>
              <div><small>Losses</small><b>{{ number_format($t->loss) }}</b></div>
              <div><small>Profit share</small><b>{{ rtrim(rtrim(number_format($t->profit_share, 2), '0'), '.') }}%</b></div>
            </div>
            <div class="tfoot"><span>Min. to copy <b>{{ $money($t->amount) }}</b></span><a href="{{ $go('user.copyTrading.index') }}" class="btn btn-primary btn-sm">Copy</a></div>
          </div>
        @endforeach
      </div>
    @else
      <div class="steps">
        <div class="step reveal"><span class="n">01</span><h3>Compare traders</h3><p>Win rate, wins and losses, profit share and the minimum to start — all on one card.</p></div>
        <div class="step reveal"><span class="n">02</span><h3>Choose your amount</h3><p>Your copied positions are sized to what you allocate.</p></div>
        <div class="step reveal"><span class="n">03</span><h3>Stop any time</h3><p>End a copy from your dashboard whenever you want.</p></div>
      </div>
    @endif
