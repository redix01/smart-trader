@php $signal = $signal ?? null; $selectedPlan = $selectedPlan ?? null; @endphp
<form action="{{ $signal ? route('admin.signals.update', $signal) : route('admin.signals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($signal) @method('PUT') @endif
    @if($errors->any())
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
            <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif


                    <!-- Plan Selection -->
                    <div>
                        <label for="plan_id" class="block text-sm font-medium text-gray-300 mb-2">Signal Plan <span class="text-red-500">*</span></label>
                        <select id="plan_id" name="plan_id" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a Signal Plan</option>
                            @foreach($signalPlans as $plan)
                                <option value="{{ $plan->id }}" {{ (string) old('plan_id', $signal?->plan_id ?? $selectedPlan?->id) === (string) $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} @if($plan->signal_market_type)({{ $plan->signal_market_type }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Signal Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $signal?->title) }}" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., BTC/USD Bullish Breakout">
                        </div>

                        <div>
                            <label for="trade_pair" class="block text-sm font-medium text-gray-300 mb-2">Trading Pair <span class="text-red-500">*</span></label>
                            <input type="text" id="trade_pair" name="trade_pair" value="{{ old('trade_pair', $signal?->symbol) }}" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., BTC/USD">
                        </div>
                    </div>

                    <!-- Signal Type and Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="signal_type" class="block text-sm font-medium text-gray-300 mb-2">Signal Type <span class="text-red-500">*</span></label>
                            <select id="signal_type" name="signal_type" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Signal Type</option>
                                <option value="buy" {{ old('signal_type', $signal?->type) == 'buy' ? 'selected' : '' }}>Buy (Long)</option>
                                <option value="sell" {{ old('signal_type', $signal?->type) == 'sell' ? 'selected' : '' }}>Sell (Short)</option>
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active" {{ old('status', $signal?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $signal?->status ?? 'active') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $signal?->status ?? 'active') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="expired" {{ old('status', $signal?->status ?? 'active') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="entry_price" class="block text-sm font-medium text-gray-300 mb-2">Entry Price <span class="text-red-500">*</span></label>
                            <input type="number" id="entry_price" name="entry_price" value="{{ old('entry_price', $signal?->entry_price) }}" step="0.000001" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 45000.00">
                        </div>

                        <div>
                            <label for="target_price" class="block text-sm font-medium text-gray-300 mb-2">Target Price <span class="text-red-500">*</span></label>
                            <input type="number" id="target_price" name="target_price" value="{{ old('target_price', $signal?->take_profit) }}" step="0.000001" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 48000.00">
                        </div>

                        <div>
                            <label for="stop_loss" class="block text-sm font-medium text-gray-300 mb-2">Stop Loss <span class="text-red-500">*</span></label>
                            <input type="number" id="stop_loss" name="stop_loss" value="{{ old('stop_loss', $signal?->stop_loss) }}" step="0.000001" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 43000.00">
                        </div>
                    </div>

                    <!-- Expiry -->
                    <div>
                        <label for="expiry_time" class="block text-sm font-medium text-gray-300 mb-2">Expires At <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="expiry_time" name="expiry_time" value="{{ old('expiry_time', ($signal?->expires_at ?? now()->addDay())->format('Y-m-d\TH:i')) }}" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Users stop seeing the signal as active after this time.</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Provide a detailed description of the signal...">{{ old('description', $signal?->analysis) }}</textarea>
                    </div>

                    <!-- Chart Image -->
                    <div>
                        <label for="chart_image" class="block text-sm font-medium text-gray-300 mb-2">Chart Image</label>
                        <input type="file" id="chart_image" name="chart_image" accept="image/*" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if($signal?->chart_image)<img src="{{ asset('storage/'.$signal->chart_image) }}" alt="Current chart" class="mt-2 h-24 rounded border border-gray-600">@endif
                        <p class="text-xs text-gray-400 mt-1">Upload a chart image (PNG, JPG, JPEG)</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-700">
                        <a href="{{ route('admin.signals.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                            {{ $signal ? 'Save Changes' : 'Create Signal' }}
                        </button>
                    </div>
                </form>
