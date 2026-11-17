{{-- Grouped sidebar menu. Each group collapses; the group holding the current page opens automatically. --}}
@php
  $p = fn ($d) => '<svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">'.$d.'</svg>';
  $groups = [
    ['Overview', true, [
      ['Dashboard', 'user.dashboard', 'user.dashboard', '<path d="M3 11l9-8 9 8v10a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1z"/>'],
      ['Portfolio', 'user.portfolio.index', 'user.portfolio.*', '<path d="M21 12a9 9 0 11-9-9v9z"/><path d="M15 3.5A9 9 0 0120.5 9H15z"/>'],
      ['Transactions', 'user.transactions.index', 'user.transactions.*', '<path d="M7 7h13M7 7l3-3M7 7l3 3M17 17H4m13 0l-3-3m3 3l-3 3"/>'],
    ]],
    ['Wallet', true, [
      ['Deposit', 'user.deposit', 'user.deposit*', '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>'],
      ['Withdraw', 'user.withdrawal', 'user.withdrawal*', '<circle cx="12" cy="12" r="9"/><path d="M8 12h8"/>'],
      ['Holdings', 'user.holding.index', 'user.holding.*', '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18M16 15h2"/>'],
    ]],
    ['Trade', true, [
      ['Trade Room', 'user.liveTrading.index', 'user.liveTrading.*', '<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>'],
      ['Market overview', 'user.overview.index', 'user.overview.*', '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>'],
      ['Signals', 'user.signals.index', 'user.signals.*', '<path d="M2 12h3l3-8 4 16 3-8h7"/>'],
    ]],
    ['Automate', false, [
      ['Copy trading', 'user.copyTrading.index', 'user.copyTrading.*', '<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M16 4.5a3.5 3.5 0 010 7M22 20a7 7 0 00-4-6.3"/>'],
      ['AI traders', 'user.aiTraders.index', 'user.aiTraders.*', '<rect x="4" y="7" width="16" height="12" rx="2"/><path d="M12 3v4M9 13h.01M15 13h.01"/>'],
      ['Trading bots', 'user.botTrading.index', 'user.botTrading.*', '<rect x="5" y="8" width="14" height="12" rx="2"/><path d="M12 4v4M9 14h.01M15 14h.01M2 13h3M19 13h3"/>'],
    ]],
    ['Earn & plans', false, [
      ['My plans', 'user.plans.index', 'user.plans.*', '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/>'],
      ['Trading plans', 'user.plan.trading', 'user.plan.trading', '<path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/>'],
      ['Signal plans', 'user.plan.signal', 'user.plan.signal', '<path d="M5 12.5a7 7 0 0114 0M8.5 12.5a3.5 3.5 0 017 0"/><circle cx="12" cy="17" r="1.5"/>'],
      ['Staking', 'user.staking.index', 'user.staking.*', '<path d="M12 2v20M5 7l7-5 7 5M5 17l7 5 7-5"/>'],
      ['Mining', 'user.mining.index', 'user.mining.*', '<path d="M14 4l6 6-9 9H5v-6z"/><path d="M12 6l6 6"/>'],
    ]],
    ['Account', false, [
      ['Profile', 'user.profile', 'user.profile', '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/>'],
      ['Verification', 'user.kyc.index', 'user.kyc.*', '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>'],
      ['Referrals', 'user.referrals', 'user.referrals', '<path d="M20 12v9H4v-9M2 7h20v5H2zM12 21V7M12 7a3 3 0 10-3-3c0 1.7 3 3 3 3zm0 0a3 3 0 113-3c0 1.7-3 3-3 3z"/>'],
      ['Support', 'user.support.index', 'user.support.*', '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>'],
    ]],
  ];
@endphp
<nav class="side-nav flex-1 px-3 pb-6 overflow-y-auto" aria-label="Main">
  @foreach($groups as [$title, $defaultOpen, $items])
    @php
      $items = array_values(array_filter($items, fn ($i) => \Illuminate\Support\Facades\Route::has($i[1])));
      $active = collect($items)->contains(fn ($i) => request()->routeIs($i[2]));
    @endphp
    @continue(! $items)
    <details class="side-group group/g mt-3" {{ ($defaultOpen || $active) ? 'open' : '' }}>
      <summary class="flex items-center justify-between cursor-pointer select-none list-none px-3 py-1.5 rounded-md text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400 hover:text-gray-200">
        <span>{{ $title }}</span>
        <svg class="w-3.5 h-3.5 transition-transform group-open/g:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
      </summary>
      <div class="mt-1 space-y-0.5">
        @foreach($items as [$label, $route, $pattern, $icon])
          @php $on = request()->routeIs($pattern); @endphp
          <a href="{{ route($route) }}" @if($on) aria-current="page" @endif
             class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ $on ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            {!! $p($icon) !!}<span>{{ $label }}</span>
          </a>
        @endforeach
      </div>
    </details>
  @endforeach
</nav>
