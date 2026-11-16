@extends('dashboard.layout.app')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Signals</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Signal statistics</h1>
            <p class="text-sm text-gray-400 mt-0.5">How the signals from your plans have performed.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.signals.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Back to signals</a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach([['Total signals', $totalSignals], ['Active', $activeSignals], ['Completed', $completedSignals], ['Success rate', number_format($successRate, 1).'%']] as [$label, $value])
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <div class="text-xs text-gray-400">{{ $label }}</div>
                <div class="text-xl sm:text-2xl font-semibold text-white mt-1">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <section class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-white">Recent signals</h2>
        </div>
        <ul class="divide-y divide-gray-700">
            @forelse($recentSignals as $signal)
                <li class="p-4 sm:px-6 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-white font-medium truncate">{{ $signal->title }}</div>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                            <span class="px-2 py-0.5 rounded-md font-semibold {{ $signal->type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : ($signal->type === 'sell' ? 'bg-red-500/15 text-red-400' : 'bg-gray-700 text-gray-300') }}">{{ strtoupper($signal->type) }}</span>
                            <span class="font-mono">{{ $signal->symbol }}</span>
                        </div>
                    </div>
                    <a href="{{ route('user.signals.show', $signal) }}" class="shrink-0 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">View</a>
                </li>
            @empty
                <li class="p-8 text-center">
                    <p class="text-white font-medium">No recent signals</p>
                    <p class="text-sm text-gray-400 mt-1">Signals from your plans will show up here.</p>
                </li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
