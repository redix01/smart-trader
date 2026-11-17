<?php

namespace Tests\Feature\Admin;

use App\Models\AiTrader;
use App\Models\AiTraderPlan;
use App\Models\UserAiTrader;

class AdminAiAndBotTest extends AdminTestCase
{
    private function traderPayload(int $planId): array
    {
        return [
            'ai_trader_plan_id' => $planId, 'name' => 'Alpha One', 'trading_strategy' => 'moderate', 'ai_model' => 'GPT-4o-Trader',
            'ai_confidence' => 'medium', 'ai_learning_mode' => 'adaptive', 'stocks_to_trade' => ['AAPL', 'TSLA'], 'risk_tolerance' => 0.5,
            'stop_loss_percentage' => 5, 'take_profit_percentage' => 10, 'max_positions' => 5, 'position_size_percentage' => 20,
        ];
    }

    public function test_plan_and_trader_created_by_admin_appear_on_the_user_side(): void
    {
        $this->asAdmin()->postJson(route('admin.ai-trader-plans.store'), [
            'name' => 'Starter AI', 'description' => 'd', 'price' => 99, 'number_of_traders' => 1, 'stocks_trading' => ['AAPL'], 'investment_amount' => 1000,
        ])->assertJson(['success' => true]);
        $plan = AiTraderPlan::first();

        $this->asAdmin()->postJson(route('admin.ai-traders.store'), $this->traderPayload($plan->id))->assertJson(['success' => true]);
        $trader = AiTrader::first();

        $this->asAdmin()->get(route('admin.ai-trader-plans.index'))->assertOk()->assertSee('Starter AI');
        $this->asAdmin()->get(route('admin.ai-traders.index'))->assertOk()->assertSee('Alpha One');
        $this->asAdmin()->getJson(route('admin.ai-traders.show', $trader))->assertOk()->assertJsonPath('trader.name', 'Alpha One');
        $this->asAdmin()->getJson(route('admin.ai-traders.by-plan', $plan))->assertOk()->assertJsonPath('traders.0.name', 'Alpha One');
        $this->actingAs($this->member())->get(route('user.aiTraders.index'))->assertOk()->assertSee('Starter AI');

        $this->asAdmin()->postJson(route('admin.ai-traders.toggle-status', $trader))->assertJson(['success' => true]);
        $this->asAdmin()->putJson(route('admin.ai-traders.update', $trader), array_merge($this->traderPayload($plan->id), ['is_active' => true, 'name' => 'Alpha Two']))->assertJson(['success' => true]);
        $this->assertSame('Alpha Two', $trader->fresh()->name);
    }

    public function test_admin_can_book_performance_for_a_subscribed_user(): void
    {
        $user = $this->member();
        $plan = AiTraderPlan::create(['name' => 'P', 'description' => 'd', 'price' => 1, 'number_of_traders' => 1, 'stocks_trading' => ['AAPL'], 'investment_amount' => 100, 'is_active' => true]);
        $ai = AiTrader::create($this->traderPayload($plan->id));
        $sub = UserAiTrader::create(['user_id' => $user->id, 'ai_trader_id' => $ai->id, 'ai_trader_plan_id' => $plan->id, 'investment_amount' => 100, 'status' => 'active', 'activated_at' => now(), 'current_balance' => 100, 'total_profit_loss' => 0, 'total_trades_executed' => 0, 'winning_trades' => 0, 'win_rate' => 0]);

        $this->asAdmin()->get(route('admin.ai-traders.management'))->assertOk();
        $this->asAdmin()->get(route('admin.ai-traders.history', $sub))->assertOk();
        $this->asAdmin()->postJson(route('admin.ai-traders.create-trade', $sub), ['stock_symbol' => 'AAPL', 'trade_type' => 'buy', 'quantity' => 2, 'price' => 150, 'profit_loss' => 25, 'trade_date' => now()->toDateString()])->assertJson(['success' => true]);

        $this->assertEqualsWithDelta(125, (float) $sub->fresh()->current_balance, 0.001);
        $this->assertSame(1, (int) $sub->fresh()->winning_trades);
        $this->asAdmin()->getJson(route('admin.ai-traders.performance-data', $sub))->assertOk();
    }

    public function test_bot_trade_booked_by_admin_updates_bot_stats_and_notifies_the_user(): void
    {
        $user = $this->member();
        $bot = \App\Models\BotTrading::create(['user_id' => $user->id, 'name' => 'GridBot', 'base_asset' => 'BTC', 'quote_asset' => 'USDT', 'trading_type' => 'crypto', 'leverage' => 1, 'trade_duration' => 60, 'target_yield_percentage' => 5, 'auto_close' => true, 'strategy' => 'grid', 'status' => 'active', 'strategy_config' => ['x' => 1], 'max_investment' => 1000, 'min_trade_amount' => 10, 'max_trade_amount' => 100]);

        $this->asAdmin()->get(route('admin.bot-trading.index'))->assertOk()->assertSee('GridBot');
        $this->asAdmin()->get(route('admin.bot-trading.stats'))->assertOk();
        $this->asAdmin()->postJson(route('admin.bot-trading.create-trade', $bot), [
            'type' => 'buy', 'status' => 'executed', 'base_asset' => 'BTC', 'quote_asset' => 'USDT', 'base_amount' => 0.5, 'price' => 60000, 'quote_amount' => 30000, 'profit_loss' => 120,
        ])->assertJson(['success' => true]);

        $bot->refresh();
        $this->assertSame(1, (int) $bot->total_trades);
        $this->assertEqualsWithDelta(120, (float) $bot->total_profit, 0.001);
        $this->assertDatabaseHas('user_notifications', ['user_id' => $user->id, 'type' => 'bot_trade_executed']);
        $this->asAdmin()->get(route('admin.bot-trading.show', $bot))->assertOk();
    }
}
