@extends('admin.layouts.app')

@section('content')
<div class="px-4 pt-5 max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Trade</h1>
            <p class="text-gray-600 dark:text-gray-400">Select a user, then enter the trade details. The trade shows up on the user's dashboard immediately.</p>
        </div>
        <a href="{{ route('admin.trade.history') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
            &larr; Back to Trade History
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-800 dark:border-green-600 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-800 dark:border-red-600 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-800 dark:border-red-600 dark:text-red-300">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Trade Details</h3>
        </div>

        <form action="{{ route('admin.trade.store') }}" method="POST" class="px-6 py-6 space-y-6">
            @csrf

            <!-- Step 1: Select user -->
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3">1. Select user to trade for</h4>
                <label for="user_search" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Search user</label>
                <input type="text" id="user_search" placeholder="Type name or email to filter..." class="mb-2 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                <select id="user_id" name="user_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="" disabled {{ old('user_id') ? '' : 'selected' }}>Select User</option>
                    @foreach($users as $item)
                        <option value="{{ $item->id }}" data-balance="{{ $item->balance }}" data-search="{{ strtolower($item->name . ' ' . $item->email) }}" {{ old('user_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->email }}) — Bal: ${{ number_format($item->balance, 2) }}
                        </option>
                    @endforeach
                </select>
                <p id="user_balance_hint" class="mt-2 text-sm text-gray-500 dark:text-gray-400"></p>
                @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Step 2: Trade details -->
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3">2. Enter trade details</h4>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="market" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Market</label>
                        <select id="market" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">All markets</option>
                            <option value="crypto">Crypto</option>
                            <option value="forex">Forex</option>
                            <option value="stock">Stock</option>
                        </select>
                    </div>
                    <div>
                        <label for="trade_pair_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Trading Pair</label>
                        <select id="trade_pair_id" name="trade_pair_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="" disabled {{ old('trade_pair_id') ? '' : 'selected' }}>Select Pair</option>
                            @foreach($pairs as $pair)
                                <option value="{{ $pair->id }}" data-type="{{ $pair->type }}" {{ old('trade_pair_id') == $pair->id ? 'selected' : '' }}>
                                    {{ $pair->pair }} ({{ ucfirst($pair->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('trade_pair_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Amount (USD)</label>
                        <input type="number" name="amount" id="amount" min="0.01" step="0.01" value="{{ old('amount', 100) }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="100">
                        @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="leverage" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Leverage (<span id="leverage_label">{{ old('leverage', 10) }}</span>x)</label>
                        <div class="flex items-center gap-3">
                            <input id="leverage_range" type="range" value="{{ old('leverage', 10) }}" min="1" max="500" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                            <input type="number" name="leverage" id="leverage" value="{{ old('leverage', 10) }}" min="1" max="500" required class="w-24 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        @error('leverage')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="duration" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Duration</label>
                        <select id="duration" name="duration" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @php $durations = [1 => '1 Minute', 2 => '2 Minutes', 3 => '3 Minutes', 4 => '4 Minutes', 5 => '5 Minutes', 10 => '10 Minutes', 15 => '15 Minutes', 30 => '30 Minutes', 60 => '1 Hour', 120 => '2 Hours', 180 => '4 Hours', 360 => '6 Hours', 720 => '12 Hours', 1440 => '1 Day', 2880 => '2 Days', 5320 => '3 Days', 7200 => '5 Days', 10080 => '7 Days']; @endphp
                            @foreach($durations as $mins => $label)
                                <option value="{{ $mins }}" {{ old('duration', 5) == $mins ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('duration')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Side</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="action_type" value="buy" {{ old('action_type', 'buy') === 'buy' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">Buy (Long)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="action_type" value="sell" {{ old('action_type') === 'sell' ? 'checked' : '' }} class="w-4 h-4 text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">Sell (Short)</span>
                            </label>
                        </div>
                        @error('action_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed (settle PnL immediately)</option>
                        </select>
                    </div>

                    <div>
                        <label for="profit_loss" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Initial Profit/Loss (USD)</label>
                        <input type="number" name="profit_loss" id="profit_loss" step="0.01" value="{{ old('profit_loss', 0) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0.00">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Applied immediately if status is Closed; otherwise editable later via "Edit PnL".</p>
                    </div>

                    <div>
                        <label for="stop_loss" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stop Loss (optional)</label>
                        <input type="number" name="stop_loss" id="stop_loss" step="0.0001" value="{{ old('stop_loss') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Optional">
                    </div>

                    <div>
                        <label for="take_profit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Take Profit (optional)</label>
                        <input type="number" name="take_profit" id="take_profit" step="0.0001" value="{{ old('take_profit') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Optional">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.trade.history') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">Create Trade</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var userSearch = document.getElementById('user_search');
    var userSelect = document.getElementById('user_id');
    var balanceHint = document.getElementById('user_balance_hint');
    var allUserOptions = Array.prototype.slice.call(userSelect.querySelectorAll('option[value]'));

    function updateBalanceHint() {
        var selected = userSelect.options[userSelect.selectedIndex];
        if (selected && selected.getAttribute('data-balance') !== null) {
            balanceHint.textContent = 'Available balance: $' + Number(selected.getAttribute('data-balance')).toFixed(2);
        } else {
            balanceHint.textContent = '';
        }
    }

    userSearch.addEventListener('input', function () {
        var q = this.value.toLowerCase().trim();
        allUserOptions.forEach(function (opt) {
            if (!q || (opt.getAttribute('data-search') || '').indexOf(q) !== -1) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    });

    userSelect.addEventListener('change', updateBalanceHint);
    updateBalanceHint();

    // Market -> pair filtering
    var market = document.getElementById('market');
    var pairSelect = document.getElementById('trade_pair_id');
    var allPairOptions = Array.prototype.slice.call(pairSelect.querySelectorAll('option[value]'));
    market.addEventListener('change', function () {
        var type = this.value;
        allPairOptions.forEach(function (opt) {
            if (!type || opt.getAttribute('data-type') === type) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
        var current = pairSelect.options[pairSelect.selectedIndex];
        if (current && current.style.display === 'none') {
            pairSelect.selectedIndex = 0;
        }
    });

    // Leverage slider sync
    var range = document.getElementById('leverage_range');
    var leverage = document.getElementById('leverage');
    var label = document.getElementById('leverage_label');
    range.addEventListener('input', function () {
        leverage.value = this.value;
        label.textContent = this.value;
    });
    leverage.addEventListener('input', function () {
        range.value = this.value;
        label.textContent = this.value;
    });
});
</script>
@endpush
