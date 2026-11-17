{{-- Trading section switcher: floating bottom bar (also included by dashboard/copy-trade). --}}
@php
    $tradingTabs = [
        ['route' => 'user.overview.index', 'label' => 'Overview', 'icon' => '<path d="M4 6h16M4 12h16M4 18h10"/>'],
        ['route' => 'user.liveTrading.index', 'label' => 'Live trade', 'icon' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>'],
        ['route' => 'user.copyTrading.index', 'label' => 'Copy trade', 'icon' => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M16 4.5a3.5 3.5 0 010 7M22 20a7 7 0 00-4-6.3"/>'],
        ['route' => 'user.botTrading.index', 'label' => 'Bot trade', 'icon' => '<rect x="5" y="8" width="14" height="12" rx="2"/><path d="M12 4v4M9 14h.01M15 14h.01"/>'],
    ];
@endphp
<nav aria-label="Trading sections" class="fixed z-40 bottom-3 inset-x-3 sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:w-[30rem] rounded-2xl border border-gray-700 bg-gray-800/95 backdrop-blur shadow-lg shadow-black/40" style="margin-bottom: env(safe-area-inset-bottom);">
    <div class="grid grid-cols-4 gap-1 p-1">
        @foreach($tradingTabs as $tab)
            @php $active = request()->routeIs($tab['route']); @endphp
            <a href="{{ route($tab['route']) }}" @if($active) aria-current="page" @endif
               class="flex flex-col items-center justify-center gap-1 min-h-[52px] rounded-xl text-[11px] font-semibold whitespace-nowrap transition-colors {{ $active ? 'bg-gray-950 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/60' }}">
                <svg class="w-5 h-5 {{ $active ? 'text-gold-400' : '' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>

<!-- Bottom padding so the bar never covers content -->
<div class="h-20" aria-hidden="true"></div>
