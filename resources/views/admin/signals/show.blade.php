@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">{{ $signal->title }}</h1>
                <p class="text-gray-400">{{ $signal->plan?->name ?? 'No plan' }} · created by {{ $signal->creator?->name ?? 'unknown' }} · {{ $signal->created_at?->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.signals.edit', $signal) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg">Edit</a>
                <a href="{{ route('admin.signals.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Back to Signals</a>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 grid grid-cols-2 md:grid-cols-3 gap-6 text-sm">
            @foreach([
                'Symbol' => $signal->symbol,
                'Direction' => ucfirst($signal->type),
                'Status' => ucfirst($signal->status),
                'Entry price' => number_format((float) $signal->entry_price, 2),
                'Take profit' => number_format((float) $signal->take_profit, 2),
                'Stop loss' => number_format((float) $signal->stop_loss, 2),
                'Expires' => $signal->expires_at?->format('M d, Y H:i') ?? '—',
            ] as $label => $value)
                <div><div class="text-gray-400">{{ $label }}</div><div class="text-white font-semibold">{{ $value }}</div></div>
            @endforeach
        </div>
        @if($signal->analysis)
            <div class="mt-6 bg-gray-800 rounded-xl border border-gray-700 p-6 text-gray-200 whitespace-pre-line">{{ $signal->analysis }}</div>
        @endif
        @if($signal->chart_image)
            <img src="{{ asset('storage/'.$signal->chart_image) }}" alt="Chart" class="mt-6 rounded-xl border border-gray-700 max-w-full">
        @endif
    </div>
</div>
@endsection
