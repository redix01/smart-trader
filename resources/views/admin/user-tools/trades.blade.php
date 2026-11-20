@extends('admin.layouts.app')

@php
    $field = 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2';
    $chosenUser = old('user_id', $selectedUser);
@endphp

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Trade for User</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Opens a market position in the user's Trade Room at the live price. The margin comes from the wallet you choose, and the user is notified.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.user-trades.store') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="md:col-span-2 xl:col-span-1">
                <label for="user_id" class="{{ $label }}">User</label>
                <select id="user_id" name="user_id" required class="{{ $field }}">
                    <option value="">Choose a user</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" data-trading="{{ $u->formatAmount($u->trading_balance) }}" data-deposit="{{ $u->formatAmount($u->deposit_balance) }}" {{ (string) $chosenUser === (string) $u->id ? 'selected' : '' }}>{{ $u->name }} — {{ $u->email }}</option>
                    @endforeach
                </select>
                <p id="userBalances" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
            </div>

            <div>
                <label for="market" class="{{ $label }}">Market</label>
                <select id="market" required class="{{ $field }}">
                    <option value="">Choose a market</option>
                    @foreach($assets->groupBy('type') as $type => $group)
                        <optgroup label="{{ $type === 'crypto' ? 'Crypto' : 'Stocks' }}">
                            @foreach($group as $a)
                                <option value="{{ $a->type }}|{{ $a->symbol }}" {{ old('asset_type').'|'.old('symbol') === $a->type.'|'.$a->symbol ? 'selected' : '' }}>{{ $a->symbol }} — {{ $a->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <input type="hidden" name="asset_type" id="asset_type" value="{{ old('asset_type') }}">
                <input type="hidden" name="symbol" id="symbol" value="{{ old('symbol') }}">
            </div>

            <div>
                <span class="{{ $label }}">Direction</span>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 py-2 text-sm font-medium text-green-700 dark:text-green-400 has-[:checked]:bg-green-50 dark:has-[:checked]:bg-green-900/30 has-[:checked]:border-green-500"><input type="radio" name="side" value="buy" {{ old('side', 'buy') === 'buy' ? 'checked' : '' }}> Buy / Long</label>
                    <label class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 py-2 text-sm font-medium text-red-700 dark:text-red-400 has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-900/30 has-[:checked]:border-red-500"><input type="radio" name="side" value="sell" {{ old('side') === 'sell' ? 'checked' : '' }}> Sell / Short</label>
                </div>
            </div>

            <div>
                <label for="amount" class="{{ $label }}">Margin (user's currency)</label>
                <input type="number" step="0.01" min="1" id="amount" name="amount" required value="{{ old('amount') }}" class="{{ $field }}" placeholder="e.g. 500">
            </div>

            <div>
                <label for="leverage" class="{{ $label }}">Leverage</label>
                <select id="leverage" name="leverage" class="{{ $field }}">
                    @foreach([1, 2, 5, 10, 20, 50, 100] as $lev)
                        <option value="{{ $lev }}" {{ (int) old('leverage', 1) === $lev ? 'selected' : '' }}>{{ $lev }}x</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="fund_from" class="{{ $label }}">Take margin from</label>
                <select id="fund_from" name="fund_from" class="{{ $field }}">
                    <option value="trading_balance" {{ old('fund_from') === 'trading_balance' ? 'selected' : '' }}>Trading balance</option>
                    <option value="deposit_balance" {{ old('fund_from') === 'deposit_balance' ? 'selected' : '' }}>Deposits balance</option>
                </select>
            </div>

            <div>
                <label for="take_profit" class="{{ $label }}">Take profit price <span class="text-gray-400">(optional)</span></label>
                <input type="number" step="any" id="take_profit" name="take_profit" value="{{ old('take_profit') }}" class="{{ $field }}">
            </div>

            <div>
                <label for="stop_loss" class="{{ $label }}">Stop loss price <span class="text-gray-400">(optional)</span></label>
                <input type="number" step="any" id="stop_loss" name="stop_loss" value="{{ old('stop_loss') }}" class="{{ $field }}">
            </div>
        </div>

        <button type="submit" class="mt-5 w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg">Open trade</button>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white p-4 sm:px-6">Trades placed by admin</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-left text-xs uppercase text-gray-500 dark:text-gray-400">
                    <tr><th class="px-4 py-3">User</th><th class="px-4 py-3">Market</th><th class="px-4 py-3">Side</th><th class="px-4 py-3">Margin</th><th class="px-4 py-3">Entry</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">P&amp;L</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                    @forelse($trades as $t)
                        @php $owner = $t->user; @endphp
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $owner->name ?? '—' }}<span class="block text-xs text-gray-500">{{ $t->created_at->format('M d, H:i') }}</span></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $t->symbol }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-semibold {{ $t->side === 'buy' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">{{ strtoupper($t->side) }} {{ (float) $t->leverage }}x</span></td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $owner ? $owner->formatAmount($t->amount) : $t->amount }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $owner && $t->entry_price ? $owner->formatAmount($t->entry_price, 4) : '—' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($t->status) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap {{ (float) $t->realized_pnl >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $t->realized_pnl !== null && $owner ? $owner->formatAmount($t->realized_pnl) : '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($t->isOpen())
                                    <form method="POST" action="{{ route('admin.user-trades.close', $t) }}">
                                        @csrf
                                        <button type="submit" data-confirm="Close this {{ $t->symbol }} position at the live price?" class="text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-400">Close</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No trades placed by admin yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $trades->links() }}</div>
    </div>
</div>

<script>
(function () {
    const user = document.getElementById('user_id');
    const out = document.getElementById('userBalances');
    const market = document.getElementById('market');
    const showBalances = () => {
        const o = user.selectedOptions[0];
        out.textContent = o && o.value ? 'Trading ' + o.dataset.trading + ' · Deposits ' + o.dataset.deposit : '';
    };
    const syncMarket = () => {
        const [type, sym] = (market.value || '|').split('|');
        document.getElementById('asset_type').value = type;
        document.getElementById('symbol').value = sym;
    };
    user.addEventListener('change', showBalances);
    market.addEventListener('change', syncMarket);
    showBalances();
    if (market.value) syncMarket();
})();
</script>
@endsection
