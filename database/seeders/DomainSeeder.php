<?php

namespace Database\Seeders;

use App\Models\DepositMethod;
use App\Models\Expert;
use App\Models\MarketPair;
use App\Models\MiningPlan;
use App\Models\PropertyProject;
use App\Models\StakingPlan;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        // wallets for test user (id=1)
        Wallet::insert([
            ['user_id' => 1, 'currency' => 'BTC', 'balance' => 0.4285, 'locked_balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'currency' => 'ETH', 'balance' => 5.120, 'locked_balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'currency' => 'USDT', 'balance' => 850.00, 'locked_balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'currency' => 'USD', 'balance' => 1200.00, 'locked_balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'currency' => 'SOL', 'balance' => 15.00, 'locked_balance' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // market pairs
        MarketPair::insert([
            ['base_currency' => 'BTC', 'quote_currency' => 'USDT', 'current_price' => 94560.20, 'price_change_24h' => 2.4, 'volume_24h' => 34200000000, 'high_24h' => 98200, 'low_24h' => 92100, 'market_cap' => 1800000000000, 'icon' => 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['base_currency' => 'ETH', 'quote_currency' => 'USDT', 'current_price' => 3120.45, 'price_change_24h' => 1.8, 'volume_24h' => 12100000000, 'high_24h' => 3250, 'low_24h' => 2980, 'market_cap' => 420000000000, 'icon' => 'https://assets.coingecko.com/coins/images/279/small/ethereum.png', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['base_currency' => 'SOL', 'quote_currency' => 'USDT', 'current_price' => 142.12, 'price_change_24h' => 5.7, 'volume_24h' => 4500000000, 'high_24h' => 145, 'low_24h' => 130, 'market_cap' => 65000000000, 'icon' => 'https://assets.coingecko.com/coins/images/4128/small/solana.png', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['base_currency' => 'ADA', 'quote_currency' => 'USDT', 'current_price' => 0.45, 'price_change_24h' => -1.2, 'volume_24h' => 800000000, 'high_24h' => 0.48, 'low_24h' => 0.42, 'market_cap' => 16000000000, 'icon' => 'https://assets.coingecko.com/coins/images/975/small/cardano.png', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['base_currency' => 'XRP', 'quote_currency' => 'USDT', 'current_price' => 0.62, 'price_change_24h' => -0.5, 'volume_24h' => 1200000000, 'high_24h' => 0.65, 'low_24h' => 0.58, 'market_cap' => 34000000000, 'icon' => 'https://assets.coingecko.com/coins/images/44/small/xrp.png', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['base_currency' => 'DOT', 'quote_currency' => 'USDT', 'current_price' => 7.85, 'price_change_24h' => 2.1, 'volume_24h' => 400000000, 'high_24h' => 8.10, 'low_24h' => 7.50, 'market_cap' => 11000000000, 'icon' => 'https://assets.coingecko.com/coins/images/12171/small/polkadot.png', 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // deposit methods
        DepositMethod::insert([
            ['currency' => 'BTC', 'network' => 'BTC', 'label' => 'Bitcoin (BTC)', 'wallet_address' => '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa', 'icon' => 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png', 'min_amount' => 50, 'max_amount' => 100000, 'fee_fixed' => 0, 'fee_percent' => 0, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['currency' => 'ETH', 'network' => 'ERC20', 'label' => 'Ethereum (ERC20)', 'wallet_address' => '0x742d35Cc6634C0532925a3b844Bc454e4438f44e', 'icon' => 'https://assets.coingecko.com/coins/images/279/small/ethereum.png', 'min_amount' => 50, 'max_amount' => 100000, 'fee_fixed' => 0, 'fee_percent' => 0, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['currency' => 'USDT', 'network' => 'TRC20', 'label' => 'Tether (TRC20)', 'wallet_address' => 'T9yD98JwGL3fVnAA9WhD9JwGL3fVnAA9WhD', 'icon' => 'https://assets.coingecko.com/coins/images/325/small/tether.png', 'min_amount' => 50, 'max_amount' => 100000, 'fee_fixed' => 0, 'fee_percent' => 0, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['currency' => 'SOL', 'network' => 'SOL', 'label' => 'Solana (SOL)', 'wallet_address' => 'Evh1p36DnqTu9UqpPsd1J9xXG3NQuJp3xEvh1p36Dn', 'icon' => 'https://assets.coingecko.com/coins/images/4128/small/solana.png', 'min_amount' => 20, 'max_amount' => 100000, 'fee_fixed' => 0, 'fee_percent' => 0, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['currency' => 'LTC', 'network' => 'LTC', 'label' => 'Litecoin (LTC)', 'wallet_address' => 'ltc1qtrfwlprv6ja373ek3af8u990g8unqs9cchqfvd', 'icon' => 'https://assets.coingecko.com/coins/images/2/small/litecoin.png', 'min_amount' => 50, 'max_amount' => 100000, 'fee_fixed' => 0, 'fee_percent' => 0, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // staking plans
        StakingPlan::insert([
            ['name' => 'Polygon', 'currency' => 'MATIC', 'icon' => 'https://assets.coingecko.com/coins/images/4713/small/matic_token.png', 'min_amount' => 1, 'max_amount' => 1000, 'apy' => 12.5, 'payout_cycle' => 'weekly', 'duration_days' => 30, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bitcoin', 'currency' => 'BTC', 'icon' => 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png', 'min_amount' => 0.001, 'max_amount' => 10, 'apy' => 8.2, 'payout_cycle' => 'weekly', 'duration_days' => 30, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ethereum', 'currency' => 'ETH', 'icon' => 'https://assets.coingecko.com/coins/images/279/small/ethereum.png', 'min_amount' => 0.1, 'max_amount' => 300, 'apy' => 10.8, 'payout_cycle' => 'weekly', 'duration_days' => 30, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Solana', 'currency' => 'SOL', 'icon' => 'https://assets.coingecko.com/coins/images/4128/small/solana.png', 'min_amount' => 10, 'max_amount' => 73, 'apy' => 14.5, 'payout_cycle' => 'weekly', 'duration_days' => 30, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Avalanche', 'currency' => 'AVAX', 'icon' => 'https://assets.coingecko.com/coins/images/12559/small/Avalanche_Circle_RedWhite_Trans.png', 'min_amount' => 13, 'max_amount' => 500, 'apy' => 11.3, 'payout_cycle' => 'weekly', 'duration_days' => 30, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tether', 'currency' => 'USDT', 'icon' => 'https://assets.coingecko.com/coins/images/325/small/tether.png', 'min_amount' => 500, 'max_amount' => 1000000, 'apy' => 6.0, 'payout_cycle' => 'weekly', 'duration_days' => 30, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // mining plans
        MiningPlan::insert([
            ['name' => 'Starter', 'min_amount' => 35000, 'max_amount' => 59500, 'roi_percent' => 15, 'duration_days' => 30, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lite', 'min_amount' => 60000, 'max_amount' => 95000, 'roi_percent' => 50, 'duration_days' => 30, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Silver', 'min_amount' => 100000, 'max_amount' => 200000, 'roi_percent' => 100, 'duration_days' => 30, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Classic', 'min_amount' => 250000, 'max_amount' => 400000, 'roi_percent' => 200, 'duration_days' => 30, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pro', 'min_amount' => 500000, 'max_amount' => 1000000, 'roi_percent' => 400, 'duration_days' => 30, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // experts
        Expert::insert([
            ['name' => 'Dan Malone', 'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Dan', 'win_rate' => 78, 'profit_share' => 12, 'status' => 'verified', 'total_volume' => 2500000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casey Valero', 'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Casey', 'win_rate' => 64, 'profit_share' => 15, 'status' => 'pro', 'total_volume' => 1800000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'The Trading Geek', 'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Geek', 'win_rate' => 82, 'profit_share' => 20, 'status' => 'institutional', 'total_volume' => 5200000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cornelia Matilda', 'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Cornelia', 'win_rate' => 71, 'profit_share' => 10, 'status' => 'verified', 'total_volume' => 1500000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kayse Martinez', 'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Kayse', 'win_rate' => 89, 'profit_share' => 25, 'status' => 'top-tier', 'total_volume' => 8900000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mariano Cornelia', 'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Mariano', 'win_rate' => 75, 'profit_share' => 14, 'status' => 'verified', 'total_volume' => 3200000, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // property projects
        PropertyProject::insert([
            ['title' => 'Bridge Labs at Pegasus Park', 'region' => 'Life Science • Dallas, TX', 'description' => 'High-profile 135,000 sq. ft. redevelopment within a thriving new biotech-focused campus.', 'strategy' => 'Fixed Income', 'min_investment' => 100000, 'target_roi' => 18.5, 'status' => 'open', 'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=600&auto=format&fit=crop', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Fabian Labs, Palo Alto', 'region' => 'Industrial • Silicon Valley, CA', 'description' => 'Undersupply of lab space in the heart of Silicon Valley, minutes from Stanford University.', 'strategy' => 'Growth & Value', 'min_investment' => 50000, 'target_roi' => 22.4, 'status' => 'open', 'image' => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?q=80&w=600&auto=format&fit=crop', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'The Skyline Residency', 'region' => 'Residential • Miami, FL', 'description' => 'Luxury multi-family complex featuring 450 units with waterfront views and high occupancy.', 'strategy' => 'Cash Flow', 'min_investment' => 25000, 'target_roi' => 12.8, 'status' => 'open', 'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?q=80&w=600&auto=format&fit=crop', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
