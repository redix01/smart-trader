<?php

namespace Tests\Feature\Admin;

use App\Models\AiTrader;
use App\Models\AiTraderPlan;
use App\Models\BotTrade;
use App\Models\BotTrading;
use App\Models\CopiedTrade;
use App\Models\CopyTrader;
use App\Models\Deposit;
use App\Models\LiveTrade;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Trade;
use App\Models\TradePair;
use App\Models\TradingSignal;
use App\Models\User;
use App\Models\UserAiTrader;
use App\Models\UserMining;
use App\Models\UserPlan;
use App\Models\UserStaking;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function seedEverything(User $user): array
    {
        $ids = ['user' => $user->id];
        $pm = PaymentMethod::create(['wallet' => 'crypto', 'crypto_type' => 'BTC', 'address' => 'bc1qxyz', 'is_active' => true]);
        $pair = TradePair::create(['type' => 'crypto', 'pair' => 'BTC/USD', 'current_price' => 60000, 'price_change_24h' => 1.2]);
        $ids['payment-method'] = $pm->id;
        $ids['trade-pair'] = $pair->id;

        foreach ([0, 1, 2] as $s) {
            Deposit::create(['user_id' => $user->id, 'amount' => 100 + $s, 'payment_method_id' => $pm->id, 'wallet_type' => ['balance', 'trading', 'staking'][$s], 'status' => $s, 'proof' => 'deposits/x.png']);
            Withdrawal::create(['user_id' => $user->id, 'amount' => 20 + $s, 'payment_method' => 'crypto', 'from_account' => 'balance', 'wallet' => 'BTC', 'address' => 'bc1q', 'status' => $s]);
        }
        foreach (['open', 'closed'] as $st) {
            Trade::create(['user_id' => $user->id, 'trade_pair_id' => $pair->id, 'amount' => 50, 'status' => $st, 'leverage' => 5, 'duration' => 10, 'action_type' => 'buy', 'profit_loss' => 5]);
        }
        foreach (['pending', 'open', 'closed', 'liquidated', 'cancelled'] as $st) {
            LiveTrade::create(['user_id' => $user->id, 'asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'market', 'side' => 'buy', 'quantity' => 0.001, 'price' => 60000, 'amount' => 10, 'leverage' => 5, 'status' => $st, 'entry_price' => 60000, 'realized_pnl' => 1]);
        }
        $ct = CopyTrader::create(['name' => 'Ace', 'amount' => 100, 'win_rate' => 80, 'profit_share' => 10, 'win' => 5, 'loss' => 1, 'status' => 'active']);
        $ids['copy-trader'] = $ct->id;
        CopiedTrade::create(['user_id' => $user->id, 'copy_trader_id' => $ct->id, 'amount' => 100, 'trade_count' => 1, 'win' => 1, 'loss' => 0, 'pnl' => 5, 'status' => 'active']);

        $bot = BotTrading::create(['user_id' => $user->id, 'name' => 'Bot1', 'base_asset' => 'BTC', 'quote_asset' => 'USDT', 'trading_type' => 'crypto', 'leverage' => 1, 'trade_duration' => 60, 'target_yield_percentage' => 5, 'auto_close' => true, 'strategy' => 'grid', 'status' => 'active', 'strategy_config' => ['x' => 1], 'max_investment' => 1000, 'min_trade_amount' => 10, 'max_trade_amount' => 100]);
        $ids['bot-trading'] = $bot->id;
        BotTrade::create(['trade_id' => 'T1', 'bot_trading_id' => $bot->id, 'user_id' => $user->id, 'type' => 'buy', 'base_asset' => 'BTC', 'quote_asset' => 'USDT', 'base_amount' => 1, 'quote_amount' => 100, 'price' => 100, 'status' => 'executed', 'execution_type' => 'market', 'profit_loss' => 1]);

        foreach (['trading', 'staking', 'mining', 'signal'] as $type) {
            $plan = Plan::create(['name' => ucfirst($type).' plan', 'type' => $type, 'description' => 'd', 'price' => 10, 'is_active' => true, 'min_funding' => 10, 'max_funding' => 1000]);
            UserPlan::create(['user_id' => $user->id, 'plan_id' => $plan->id, 'status' => 'active', 'amount_paid' => 10, 'currency' => 'USD', 'start_date' => now(), 'end_date' => now()->addMonth()]);
            $ids['plans'] ??= $plan->id;
            if ($type === 'staking') {
                UserStaking::create(['user_id' => $user->id, 'plan_id' => $plan->id, 'amount_staked' => 10, 'currency' => 'USD', 'apy_rate' => 5, 'start_date' => now(), 'end_date' => now()->addMonth(), 'status' => 'active', 'total_rewards' => 0, 'current_value' => 10]);
            }
            if ($type === 'mining') {
                $ids['mining'] = UserMining::create(['user_id' => $user->id, 'plan_id' => $plan->id, 'amount_invested' => 10, 'currency' => 'USD', 'hashrate' => 5, 'start_date' => now(), 'end_date' => now()->addMonth(), 'status' => 'active', 'total_mined' => 0, 'total_withdrawn' => 0, 'current_value' => 10])->id;
            }
            if ($type === 'signal') {
                $ids['signals'] = TradingSignal::create(['plan_id' => $plan->id, 'title' => 'Sig', 'symbol' => 'BTC', 'type' => 'buy', 'status' => 'active', 'entry_price' => 1, 'take_profit' => 2, 'stop_loss' => 0.5, 'expires_at' => now()->addDay(), 'created_by' => $user->id, 'is_active' => true])->id;
            }
        }

        $aip = AiTraderPlan::create(['name' => 'AI plan', 'description' => 'd', 'price' => 100, 'number_of_traders' => 1, 'stocks_trading' => 5, 'investment_amount' => 1000, 'is_active' => true, 'features' => ['a']]);
        $ait = AiTrader::create(['ai_trader_plan_id' => $aip->id, 'name' => 'AI1', 'trading_strategy' => 'moderate', 'ai_model' => 'gpt', 'ai_confidence' => 80, 'ai_learning_mode' => 'adaptive', 'stocks_to_trade' => ['AAPL'], 'risk_tolerance' => 0.5, 'stop_loss_percentage' => 5, 'take_profit_percentage' => 10, 'max_positions' => 5, 'position_size_percentage' => 10, 'is_active' => true, 'current_performance' => 1, 'total_trades' => 0, 'winning_trades' => 0, 'win_rate' => 0]);
        $uait = UserAiTrader::create(['user_id' => $user->id, 'ai_trader_id' => $ait->id, 'ai_trader_plan_id' => $aip->id, 'investment_amount' => 100, 'status' => 'active', 'activated_at' => now(), 'current_balance' => 100, 'total_profit_loss' => 0, 'total_trades_executed' => 0, 'winning_trades' => 0, 'win_rate' => 0]);
        $ids['ai-trader-plans'] = $aip->id;
        $ids['ai-traders'] = $ait->id;
        $ids['ai-traders-history'] = $uait->id;
        $user->createNotification('info', 'Hi', 'Body', []);

        return $ids;
    }

    public function test_every_admin_get_route_renders_with_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['balance' => 500, 'trading_balance' => 500]);
        $ids = $this->seedEverything($user);
        $failures = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (! str_starts_with($uri, 'admin/') || ! in_array('GET', $route->methods())) {
                continue;
            }
            if (str_contains($uri, 'notifications/{id}')) {
                continue; // keyed by a mail-log id, covered in AdminNotificationsTest
            }
            // Fill route params from the seeded ids by the first URL segment.
            $segment = explode('/', $uri)[1];
            $url = preg_replace('/\{[^}]+\}/', (string) ($ids[$segment] ?? $ids['user']), $uri);
            if (str_contains($uri, '/create') || str_contains($uri, 'by-plan') || str_contains($uri, 'data')) {
                $url = preg_replace('/\{[^}]+\}/', (string) ($ids['ai-trader-plans']), $uri);
            }
            $res = $this->actingAs($admin)->get('/'.$url);
            if ($res->getStatusCode() >= 400) {
                $failures[] = $url.' => '.$res->getStatusCode().' '.substr((string) ($res->exception?->getMessage()), 0, 200);
            }
        }

        $this->assertSame([], $failures, "\n".implode("\n", $failures));
    }
}
