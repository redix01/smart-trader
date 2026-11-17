{{-- Portfolio section tabs (included at the top of each portfolio page). Scrolls inside itself on phones. --}}
@php
    $portfolioTabs = [
        ['route' => 'user.portfolio.trade', 'label' => 'Plans', 'icon' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/>'],
        ['route' => 'user.portfolio.staking', 'label' => 'Staking', 'icon' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h3.75a1.75 1.75 0 010 3.5h-2.5a1.75 1.75 0 000 3.5H14.5"/>'],
        ['route' => 'user.portfolio.mining', 'label' => 'Mining', 'icon' => '<path d="M13 3L5 14h6l-1 7 8-11h-6l1-7z"/>'],
        ['route' => 'user.portfolio.holding', 'label' => 'Holdings', 'icon' => '<path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4M3 17l9 4 9-4"/>'],
        ['route' => 'user.portfolio.signal', 'label' => 'Signals', 'icon' => '<path d="M4 18h2M9 18V12M14 18V8M19 18V4"/>'],
    ];
    $onHoldingIndex = request()->routeIs('user.holding.index');
@endphp
<nav aria-label="Portfolio sections">
    <div class="grid grid-cols-5 sm:inline-flex gap-1 p-1 w-full sm:w-auto rounded-xl border border-gray-700 bg-gray-800">
        @foreach($portfolioTabs as $tab)
            @php $active = request()->routeIs($tab['route']) || ($tab['route'] === 'user.portfolio.holding' && $onHoldingIndex); @endphp
            <a href="{{ route($tab['route']) }}"
               @if($active) aria-current="page" @endif
               class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5 px-1 sm:px-4 py-1.5 sm:py-0 min-h-[44px] sm:min-h-[40px] rounded-lg text-[11px] sm:text-sm font-semibold whitespace-nowrap transition-colors {{ $active ? 'bg-gray-950 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/60' }}">
                <svg class="w-4 h-4 shrink-0 {{ $active ? 'text-gold-400' : '' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
