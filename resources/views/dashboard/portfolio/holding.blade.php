@extends('dashboard.layout.app')

@php
use App\Helpers\AssetHelper;
@endphp

@section('content')
@php
    $holdingsList = $holdings ?? collect();
    $pnlClass = fn ($v) => $v >= 0 ? 'text-emerald-400' : 'text-red-400';
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Portfolio</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Holdings</h1>
            <p class="text-sm text-gray-400 mt-0.5">The crypto and stocks you own, what they are worth now and your profit or loss.</p>
        </div>
        <button id="buyAssetBtn" type="button" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            <span>Buy asset</span>
        </button>
    </div>

    @include('dashboard.portfolio.partials.footer-menu')

    <!-- Portfolio summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Current value</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white font-mono break-all">{{ money($total_value ?? 0, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total invested</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white font-mono break-all">{{ money($total_invested ?? 0, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Unrealised P&amp;L</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold font-mono break-all {{ $pnlClass($total_pnl ?? 0) }}">{{ money($total_pnl ?? 0, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Return</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold font-mono {{ $pnlClass($total_pnl_percentage ?? 0) }}">{{ number_format($total_pnl_percentage ?? 0, 2) }}%</div>
        </div>
    </div>

    <!-- Holdings -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 border-b border-gray-700 flex items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-white">Your holdings</h2>
            <button id="refreshHoldingsBtn" type="button" class="inline-flex items-center gap-1.5 min-h-[40px] px-2 text-sm font-semibold text-gold-400 hover:text-gold-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Refresh</span>
            </button>
        </div>

        @if(count($holdingsList))
            {{-- Phones: cards --}}
            <ul class="md:hidden divide-y divide-gray-700">
                @foreach($holdingsList as $holding)
                    <li class="p-4" data-holding-id="{{ $holding->id }}" data-asset-id="{{ $holding->asset->id }}">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 shrink-0 rounded-full flex items-center justify-center {{ AssetHelper::getAssetIconClass($holding->asset->symbol) }}">
                                <span class="text-sm font-bold text-white">{{ AssetHelper::getAssetInitial($holding->asset->symbol) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-white truncate">{{ $holding->asset->name }}</div>
                                <div class="text-xs text-gray-400 font-mono truncate">{{ number_format($holding->quantity, 8) }} {{ $holding->asset->symbol }}</div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-semibold text-white font-mono">{{ money($holding->current_value, 2) }}</div>
                                <div class="text-xs font-mono {{ $pnlClass($holding->unrealized_pnl) }}">{{ money($holding->unrealized_pnl, 2) }} ({{ number_format($holding->unrealized_pnl_percentage, 2) }}%)</div>
                            </div>
                        </div>
                        <dl class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div class="rounded-lg bg-gray-900/50 px-3 py-2 min-w-0"><dt class="text-gray-400">Avg buy price</dt><dd class="mt-0.5 font-mono text-white break-all">{{ money($holding->average_buy_price, 8) }}</dd></div>
                            <div class="rounded-lg bg-gray-900/50 px-3 py-2 min-w-0"><dt class="text-gray-400">Current price</dt><dd class="mt-0.5 font-mono text-white break-all">{{ money_from($holding->asset->current_price, 'USD', 8) }}</dd></div>
                        </dl>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <a href="{{ route('user.holding.asset-chart', $holding->asset->symbol) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Chart</a>
                            <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/10 text-sm font-semibold" onclick="openSellModal({{ $holding->id }}, '{{ $holding->asset->symbol }}', {{ $holding->quantity }}, {{ round(auth()->user()->convertFrom($holding->asset->current_price), 8) }})">Sell</button>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- md+: table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900/50">
                        <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <th class="px-6 py-3">Asset</th>
                            <th class="px-6 py-3">Quantity</th>
                            <th class="px-6 py-3">Avg buy price</th>
                            <th class="px-6 py-3">Current price</th>
                            <th class="px-6 py-3">Value</th>
                            <th class="px-6 py-3">P&amp;L</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="holdingsTableBody" class="divide-y divide-gray-700">
                        @foreach($holdingsList as $holding)
                        <tr class="hover:bg-gray-700/40" data-holding-id="{{ $holding->id }}" data-asset-id="{{ $holding->asset->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 shrink-0 rounded-full flex items-center justify-center {{ AssetHelper::getAssetIconClass($holding->asset->symbol) }}">
                                        <span class="text-sm font-bold text-white">{{ AssetHelper::getAssetInitial($holding->asset->symbol) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-white">{{ $holding->asset->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $holding->asset->symbol }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ number_format($holding->quantity, 8) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($holding->average_buy_price, 8) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ money_from($holding->asset->current_price, 'USD', 8) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($holding->current_value, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono {{ $pnlClass($holding->unrealized_pnl) }}">
                                {{ money($holding->unrealized_pnl, 2) }}
                                <span class="text-xs">({{ number_format($holding->unrealized_pnl_percentage, 2) }}%)</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('user.holding.asset-chart', $holding->asset->symbol) }}" class="font-semibold text-gold-400 hover:text-gold-300 mr-4">Chart</a>
                                <button type="button" class="font-semibold text-red-400 hover:text-red-300" onclick="openSellModal({{ $holding->id }}, '{{ $holding->asset->symbol }}', {{ $holding->quantity }}, {{ round(auth()->user()->convertFrom($holding->asset->current_price), 8) }})">Sell</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-4 py-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4M3 17l9 4 9-4"/></svg>
                </div>
                <div class="mt-3 text-white font-medium">No holdings yet</div>
                <p class="mt-1 text-sm text-gray-400">Buy your first crypto or stock and it will show up here.</p>
                <a href="{{ route('user.holding.buy-assets') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Buy an asset</a>
            </div>
        @endif
    </div>
</div>

<!-- Sell Modal -->
<div id="sellModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true" aria-labelledby="sellModalTitle">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4">
        <div class="w-full max-w-lg rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="sellModalTitle" class="text-lg font-semibold text-white">Sell asset</h3>
                    <button type="button" onclick="closeSellModal()" class="w-10 h-10 -mr-2 grid place-items-center rounded-lg text-gray-400 hover:text-white hover:bg-gray-700" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <input type="hidden" id="sellHoldingId">
                    <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4 space-y-2 text-sm">
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-gray-400">Asset</span>
                            <span class="text-white font-semibold" id="sellSymbol"></span>
                        </div>
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-gray-400">Available</span>
                            <span class="text-white font-mono break-all text-right" id="sellQuantity"></span>
                        </div>
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-gray-400">Current price</span>
                            <span class="text-white font-mono break-all text-right" id="sellCurrentPrice"></span>
                        </div>
                    </div>

                    <div>
                        <label for="sellQuantityInput" class="block text-sm font-medium text-gray-300 mb-1.5">Quantity to sell</label>
                        <div class="flex gap-2">
                            <input type="number" id="sellQuantityInput" step="0.00000001" min="0.00000001" inputmode="decimal"
                                   class="min-w-0 flex-1 px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   oninput="updateSellTotal()">
                            <button type="button" onclick="setMaxQuantity()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                                Max
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Max: <span id="sellMaxQuantity"></span></p>
                    </div>

                    <div>
                        <label for="sellPriceInput" class="block text-sm font-medium text-gray-300 mb-1.5">Price per unit</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm pointer-events-none">{{ currency_symbol() }}</span>
                            <input type="number" id="sellPriceInput" step="0.00000001" min="0.00000001" inputmode="decimal"
                                   class="w-full pl-8 pr-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   oninput="updateSellTotal()">
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-sm text-gray-400">You receive</span>
                            <span class="text-white font-semibold font-mono break-all text-right" id="sellTotalValue">{{ currency_symbol() }}0.00</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-6">
                    <button type="button" onclick="closeSellModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                        Cancel
                    </button>
                    <button type="button" id="sellAssetBtn" onclick="sellAsset()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold">
                        Sell asset
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4">
        <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-500/15 text-emerald-400 grid place-items-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2" id="successTitle">Success!</h3>
                <p class="text-sm text-gray-400 mb-6" id="successMessage">Operation completed successfully.</p>
                <button type="button" onclick="document.getElementById('successModal').classList.add('hidden')" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4">
        <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-500/15 text-red-400 grid place-items-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2" id="errorTitle">Error!</h3>
                <p class="text-sm text-gray-400 mb-6" id="errorMessage">An error occurred. Please try again.</p>
                <button type="button" onclick="document.getElementById('errorModal').classList.add('hidden')" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Refresh holdings
document.getElementById('refreshHoldingsBtn').addEventListener('click', function() {
    location.reload();
});

// Buy asset button
document.getElementById('buyAssetBtn').addEventListener('click', function() {
    window.location.href = '{{ route("user.holding.buy-assets") }}';
});

function openSellModal(holdingId, symbol, quantity, currentPrice) {
    // Set modal data
    document.getElementById('sellHoldingId').value = holdingId;
    document.getElementById('sellSymbol').textContent = symbol;
    document.getElementById('sellQuantity').textContent = quantity;
    document.getElementById('sellCurrentPrice').textContent = CURRENCY_SYMBOL + parseFloat(currentPrice).toFixed(8);
    document.getElementById('sellQuantityInput').value = quantity;
    document.getElementById('sellPriceInput').value = currentPrice;
    document.getElementById('sellMaxQuantity').textContent = quantity;
    
    // Calculate total value
    const totalValue = quantity * currentPrice;
    document.getElementById('sellTotalValue').textContent = CURRENCY_SYMBOL + totalValue.toFixed(2);
    
    // Show modal
    document.getElementById('sellModal').classList.remove('hidden');
}

function closeSellModal() {
    document.getElementById('sellModal').classList.add('hidden');
}

function updateSellTotal() {
    const quantity = parseFloat(document.getElementById('sellQuantityInput').value) || 0;
    const price = parseFloat(document.getElementById('sellPriceInput').value) || 0;
    const maxQuantity = parseFloat(document.getElementById('sellMaxQuantity').textContent) || 0;
    
    // Validate quantity
    if (quantity > maxQuantity) {
        document.getElementById('sellQuantityInput').value = maxQuantity;
        quantity = maxQuantity;
    }
    
    const total = quantity * price;
    document.getElementById('sellTotalValue').textContent = CURRENCY_SYMBOL + total.toFixed(2);
}

function setMaxQuantity() {
    const maxQuantity = parseFloat(document.getElementById('sellMaxQuantity').textContent) || 0;
    document.getElementById('sellQuantityInput').value = maxQuantity;
    updateSellTotal();
}

function sellAsset() {
    const holdingId = document.getElementById('sellHoldingId').value;
    const quantity = parseFloat(document.getElementById('sellQuantityInput').value);
    const price = parseFloat(document.getElementById('sellPriceInput').value);
    
    if (!quantity || quantity <= 0) {
        uiAlert('Please enter a valid quantity', { title: 'Check your details' });
        return;
    }
    
    if (!price || price <= 0) {
        uiAlert('Please enter a valid price', { title: 'Check your details' });
        return;
    }
    
    // Get the asset ID from the holding
    const assetId = document.querySelector(`[data-holding-id="${holdingId}"]`).getAttribute('data-asset-id');
    
    // Show loading state
    const sellBtn = document.getElementById('sellAssetBtn');
    const originalText = sellBtn.innerHTML;
    sellBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Selling...';
    sellBtn.disabled = true;
    
    fetch('/user/holding/sell', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            asset_id: assetId,
            quantity: quantity,
            price_per_unit: price
        })
    })
    .then(response => response.json())
            .then(data => {
            if (data.success) {
                closeSellModal();
                showSuccessModal('Success!', data.message + ' Funds have been added to your trading balance. You can transfer to main balance via the withdrawal section if needed.');
                // Refresh the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showErrorModal('Error!', data.message);
            }
        })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Error!', 'Failed to sell asset. Please try again.');
    })
    .finally(() => {
        // Reset button state
        sellBtn.innerHTML = originalText;
        sellBtn.disabled = false;
    });
}

function showSuccessModal(title, message) {
    document.getElementById('successTitle').textContent = title;
    document.getElementById('successMessage').textContent = message;
    document.getElementById('successModal').classList.remove('hidden');
}

function showErrorModal(title, message) {
    document.getElementById('errorTitle').textContent = title;
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorModal').classList.remove('hidden');
}
</script>

@endsection
