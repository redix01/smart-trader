@extends('admin.layouts.app')

@section('content')
@php
    $tabs = ['' => 'All', 'pending' => 'Pending', 'open' => 'Open', 'closed' => 'Closed', 'liquidated' => 'Liquidated', 'cancelled' => 'Cancelled'];
    $badge = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'open' => 'bg-blue-100 text-blue-800',
        'closed' => 'bg-green-100 text-green-800',
        'liquidated' => 'bg-red-100 text-red-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
    ];
@endphp
<div class="px-4 pt-5">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Trade Room Orders</h1>
        <p class="text-gray-600 dark:text-gray-400">Every order users place in the dashboard Trade Room. Amounts are shown in each user's own currency.</p>
    </div>

    @include('admin.partials.user-filter')
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.live-trades.index', array_filter(['status' => $key, 'q' => $search])) }}"
               class="px-3 py-1.5 text-sm rounded-lg border {{ ($status ?? '') === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700' }}">
                {{ $label }}@if($key !== '') <span class="opacity-70">({{ $counts[$key] ?? 0 }})</span>@endif
            </a>
        @endforeach
        <form method="GET" action="{{ route('admin.live-trades.index') }}" class="flex w-full gap-2 sm:ml-auto sm:w-auto">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <input type="text" name="q" value="{{ $search }}" placeholder="Symbol, name or email" class="min-w-0 flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button class="px-3 py-1.5 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Search</button>
        </form>
    </div>

    <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @foreach(['User', 'Market', 'Side', 'Type', 'Margin', 'Lev.', 'Entry', 'Exit', 'P&L', 'Status', 'Placed', 'Actions'] as $h)
                        <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-white">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($trades as $t)
                    @php $u = $t->user; @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="p-3 text-sm text-gray-900 dark:text-white">
                            <div class="min-w-0">
                                <div class="font-medium">{{ $u?->name ?? 'Deleted user' }}</div>
                                <div class="text-xs text-gray-500 break-all">{{ $u?->email }}</div>
                            </div>
                        </td>
                        <td class="p-3 text-sm font-medium text-gray-900 dark:text-white">{{ $t->symbol }} <span class="text-xs text-gray-500">{{ $t->asset_type }}</span></td>
                        <td class="p-3 text-sm"><span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $t->side === 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($t->side) }}</span></td>
                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($t->order_type) }}</td>
                        <td class="p-3 text-sm text-gray-900 dark:text-white">{{ money($t->amount, 2, $u) }}</td>
                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ rtrim(rtrim(number_format((float) $t->leverage, 2), '0'), '.') }}x</td>
                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ $t->entry_price ? money($t->entry_price, 2, $u) : ($t->price ? money($t->price, 2, $u).' (limit)' : '—') }}</td>
                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ $t->exit_price ? money($t->exit_price, 2, $u) : '—' }}</td>
                        <td class="p-3 text-sm font-semibold {{ (float) $t->realized_pnl >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($t->realized_pnl !== null){{ (float) $t->realized_pnl >= 0 ? '+' : '-' }}{{ money(abs((float) $t->realized_pnl), 2, $u) }}@else — @endif
                        </td>
                        <td class="p-3 text-sm">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $badge[$t->status] ?? 'bg-gray-100 text-gray-800' }}">{{ ucfirst($t->status) }}</span>
                            @if($t->close_reason)<div class="text-xs text-gray-500">{{ str_replace('_', ' ', $t->close_reason) }}</div>@endif
                        </td>
                        <td class="p-3 text-xs text-gray-500 whitespace-nowrap">{{ $t->created_at?->format('M d, Y H:i') }}</td>
                        <td class="p-3 text-sm">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($t->status === 'open')
                                    <form method="POST" action="{{ route('admin.live-trades.close', $t) }}" class="flex items-center gap-1" data-confirm="Close this position? The margin plus profit/loss is paid to the user's trading balance." data-confirm-tone="info">
                                        @csrf
                                        <input type="number" step="0.01" name="profit_loss" placeholder="P&L (blank = market)" class="w-36 px-2 py-1 text-xs border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <button class="px-2 py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Close</button>
                                    </form>
                                @elseif($t->status === 'pending')
                                    <form method="POST" action="{{ route('admin.live-trades.cancel', $t) }}" data-confirm="Cancel this order and refund the margin to the user's trading balance?" data-confirm-tone="info">
                                        @csrf
                                        <button class="px-2 py-1 text-xs text-white bg-yellow-600 rounded hover:bg-yellow-700">Cancel &amp; refund</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.live-trades.destroy', $t) }}" data-confirm="Remove this trade from history? This cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-2 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="p-8 text-center text-gray-500 dark:text-gray-400">No Trade Room orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $trades->links() }}</div>
</div>
@endsection
