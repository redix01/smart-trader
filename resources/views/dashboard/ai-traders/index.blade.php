@extends('dashboard.layout.app')

@section('title', 'AI Stock Traders - Advanced AI-Powered Trading')

@section('content')
<!-- Hero Section -->
<section class="bg-gray-800 border border-gray-700 rounded-2xl text-white py-20 relative overflow-hidden">
        
    <div class="w-full px-4 relative z-10">
        <div class="max-w-6xl mx-auto text-center">
            <h1 class="text-5xl font-bold mb-6">
                <span class="text-white">🤖 AI Stock</span><br>
                <span class="text-blue-400">Traders</span>
            </h1>
            <p class="text-xl mb-8 text-gray-300">
                Advanced AI-powered stock trading with cutting-edge machine learning models. 
                Let artificial intelligence optimize your stock portfolio with precision and speed.
            </p>
            <div class="flex flex-wrap justify-center gap-4 text-sm">
                <div class="bg-blue-50 backdrop-blur-sm rounded-lg px-4 py-2 border border-gray-700">
                    <span class="font-semibold text-blue-400">GPT-4o</span> <span class="text-gray-300">Powered</span>
                </div>
                <div class="bg-blue-50 backdrop-blur-sm rounded-lg px-4 py-2 border border-gray-700">
                    <span class="font-semibold text-blue-400">Real-time</span> <span class="text-gray-300">Analysis</span>
                </div>
                <div class="bg-blue-50 backdrop-blur-sm rounded-lg px-4 py-2 border border-gray-700">
                    <span class="font-semibold text-blue-400">24/7</span> <span class="text-gray-300">Trading</span>
                </div>
                <div class="bg-blue-50 backdrop-blur-sm rounded-lg px-4 py-2 border border-gray-700">
                    <span class="font-semibold text-blue-400">Risk</span> <span class="text-gray-300">Managed</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- AI Trader Plans Section -->
<section class="py-16 bg-gray-800">
    <div class="w-full px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-white mb-4">Choose Your AI Trading Plan</h2>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                Select from our range of AI-powered trading plans, each designed for different investment levels and trading strategies.
            </p>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-700 hover:border-blue-300">
                <!-- Plan Header -->
                <div class="bg-blue-600 text-white p-6">
                    <h3 class="text-xl font-bold mb-2">{{ $plan->name }}</h3>
                    <div class="text-3xl font-bold mb-2">{{ $plan->formatted_price }}</div>
                    <p class="text-white/80 text-sm">{{ $plan->number_of_traders }} AI Trader{{ $plan->number_of_traders > 1 ? 's' : '' }}</p>
                </div>

                <!-- Plan Details -->
                <div class="p-6">
                    <p class="text-gray-300 mb-4">{{ $plan->description }}</p>
                    
                    <!-- Investment Amount -->
                    <div class="mb-4">
                        <div class="text-sm text-gray-400 mb-1">Minimum Investment</div>
                        <div class="text-lg font-semibold text-white">{{ money_from($plan->investment_amount, 'USD', 0) }}</div>
                    </div>

                    <!-- Stocks Trading -->
                    <div class="mb-4">
                        <div class="text-sm text-gray-400 mb-2">Market Covered</div>
                        <div class="flex flex-wrap gap-1">
                            @foreach($plan->stocks_trading as $stock)
                            <span class="bg-blue-50 text-blue-400 text-xs px-2 py-1 rounded border border-gray-700">{{ $stock }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Features -->
                    @if($plan->features)
                    <div class="mb-6">
                        <div class="text-sm text-gray-400 mb-2">Features</div>
                        <ul class="space-y-1">
                            @foreach($plan->features as $feature)
                            <li class="text-sm text-gray-300 flex items-center">
                                <svg class="w-4 h-4 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Action Button -->
                    <a href="{{ route('user.aiTraders.plan', $plan) }}" 
                       class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-all duration-300 text-center block">
                        <i class="fas fa-eye mr-2"></i>View AI Traders
                    </a>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 bg-gray-900">
    <div class="w-full px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-white mb-4">How AI Trading Works</h2>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                Our AI traders use advanced machine learning to analyze market data and execute trades automatically.
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-white">1. AI Analysis</h3>
                <p class="text-gray-300">Advanced AI models analyze market data, news sentiment, and technical indicators in real-time.</p>
            </div>

            <div class="text-center">
                <div class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-white">2. Smart Execution</h3>
                <p class="text-gray-300">AI traders execute trades automatically based on predefined strategies and risk parameters.</p>
            </div>

            <div class="text-center">
                <div class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-white">3. Monitor & Optimize</h3>
                <p class="text-gray-300">Track performance, adjust strategies, and let AI continuously optimize your trading approach.</p>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-gray-800">
    <div class="w-full px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-white mb-4">Why Choose Our AI Traders?</h2>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-700 hover:border-blue-300 transition-all duration-300">
                <div class="text-2xl mb-3">🧠</div>
                <h3 class="font-semibold mb-2 text-white">Advanced AI Models</h3>
                <p class="text-sm text-gray-300">GPT-4o, Claude 3.5 Sonnet, and other cutting-edge AI models power our trading decisions.</p>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-700 hover:border-blue-300 transition-all duration-300">
                <div class="text-2xl mb-3">📊</div>
                <h3 class="font-semibold mb-2 text-white">Real-time Analysis</h3>
                <p class="text-sm text-gray-300">Continuous market monitoring and analysis for optimal trading opportunities.</p>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-700 hover:border-blue-300 transition-all duration-300">
                <div class="text-2xl mb-3">🛡️</div>
                <h3 class="font-semibold mb-2 text-white">Risk Management</h3>
                <p class="text-sm text-gray-300">Built-in stop-loss, take-profit, and position sizing to protect your investments.</p>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-700 hover:border-blue-300 transition-all duration-300">
                <div class="text-2xl mb-3">⚡</div>
                <h3 class="font-semibold mb-2 text-white">24/7 Trading</h3>
                <p class="text-sm text-gray-300">AI traders work around the clock, never missing profitable opportunities.</p>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-blue-600 text-white relative overflow-hidden">
        <div class="absolute top-10 right-10 w-32 h-32 bg-gray-900 rounded-full filter blur-2xl"></div>
    <div class="absolute bottom-10 left-10 w-40 h-40 bg-gray-900 rounded-full filter blur-2xl"></div>
    
    <div class="w-full px-4 text-center relative z-10">
        <h2 class="text-3xl font-bold mb-4">Ready to Start AI Trading?</h2>
        <p class="text-xl mb-8 text-white/80">Join thousands of investors using AI to optimize their stock portfolios.</p>
        <a href="#plans" class="bg-gray-800 text-blue-400 px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300 border border-gray-700 hover:border-blue-300">
            Get Started Now
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endpush
