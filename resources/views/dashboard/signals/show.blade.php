@extends('dashboard.layout.app')

@section('content')
<div class="space-y-4 sm:space-y-6 max-w-5xl">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Signals</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">{{ $signal->title }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">Entry, targets and analysis for this signal. Copy it to share or place the trade yourself.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.signals.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Back to signals</a>
            <button id="copySignalBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Copy signal</button>
        </div>
    </div>

    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $signal->type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : ($signal->type === 'sell' ? 'bg-red-500/15 text-red-400' : 'bg-gray-700 text-gray-300') }}">{{ strtoupper($signal->type) }}</span>
            <span class="font-mono text-white font-semibold">{{ $signal->symbol }}</span>
            <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $signal->status === 'active' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-gray-700 text-gray-300' }}">{{ ucfirst($signal->status) }}</span>
            <span class="ml-auto text-sm text-amber-300" title="Signal strength">{{ $signal->signal_strength_stars }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <div class="text-xs text-gray-400">Entry</div>
                <div class="text-xl font-semibold text-white font-mono mt-1">{{ $signal->formatted_entry_price }}</div>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <div class="text-xs text-gray-400">Stop loss</div>
                <div class="text-xl font-semibold text-red-400 font-mono mt-1">{{ $signal->formatted_stop_loss ?? '—' }}</div>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <div class="text-xs text-gray-400">Take profit</div>
                <div class="text-xl font-semibold text-emerald-400 font-mono mt-1">{{ $signal->formatted_take_profit ?? '—' }}</div>
            </div>
        </div>

        @if($signal->tradingview_link)
            <a href="{{ $signal->tradingview_link }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gold-400 hover:text-gold-300">Open chart on TradingView
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        @endif

        @if($signal->analysis)
            <div class="border-t border-gray-700 pt-4">
                <h2 class="text-base font-semibold text-white">Analysis</h2>
                <div class="text-sm text-gray-300 whitespace-pre-line mt-2 break-words">{{ $signal->analysis }}</div>
            </div>
        @endif
    </section>
</div>

<script>
// Copy via controller (AJAX) for the same formatting used elsewhere
document.getElementById('copySignalBtn').addEventListener('click', async () => {
    try {
        const res = await fetch("{{ route('user.signals.copy', $signal) }}", {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
        const data = await res.json();
        if (data.success) {
            await navigator.clipboard.writeText(data.signal_text);
            uiAlert('Signal copied to clipboard', { tone: 'success', title: 'Done' });
        } else {
            uiAlert(data.error || 'Failed to copy', { tone: 'danger', title: 'Something went wrong' });
        }
    } catch (e) {
        uiAlert('Failed to copy', { tone: 'danger', title: 'Something went wrong' });
    }
});
</script>
@endsection
