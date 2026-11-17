<?php

namespace Tests\Feature\Admin;

use App\Models\LiveTrade;
use App\Models\Trade;
use App\Models\TradePair;

/** Trade history / trade CRUD: legacy admin-managed trades and the Trade Room orders users place themselves. */
class AdminTradesTest extends AdminTestCase
{
    private function pair(): TradePair
    {
        return TradePair::create(['type' => 'crypto', 'pair' => 'BTC/USD']);
    }

    private function trade($user, array $attrs = []): Trade
    {
        return Trade::create($attrs + ['user_id' => $user->id, 'trade_pair_id' => $this->pair()->id, 'amount' => 100, 'status' => 'open', 'leverage' => 5, 'duration' => 10, 'action_type' => 'buy', 'profit_loss' => 0]);
    }

    public function test_create_takes_the_stake_and_refuses_when_balance_is_short(): void
    {
        $user = $this->member(['balance' => 150]);
        $payload = ['user_id' => $user->id, 'trade_pair_id' => $this->pair()->id, 'amount' => 100, 'leverage' => 5, 'duration' => 10, 'action_type' => 'buy'];

        $this->asAdmin()->post(route('admin.trade.store'), $payload)->assertRedirect(route('admin.trade.history'));
        $this->assertEqualsWithDelta(50, (float) $user->fresh()->balance, 0.001);

        $this->asAdmin()->post(route('admin.trade.store'), $payload)->assertSessionHas('error');
        $this->assertSame(1, Trade::count());
        $this->assertEqualsWithDelta(50, (float) $user->fresh()->balance, 0.001);
    }

    public function test_creating_an_already_closed_trade_settles_it_but_never_pays_below_zero(): void
    {
        $user = $this->member(['balance' => 500]);
        $base = ['user_id' => $user->id, 'trade_pair_id' => $this->pair()->id, 'amount' => 100, 'leverage' => 5, 'duration' => 10, 'action_type' => 'sell', 'status' => 'closed'];

        $this->asAdmin()->post(route('admin.trade.store'), $base + ['profit_loss' => 30]);
        $this->assertEqualsWithDelta(530, (float) $user->fresh()->balance, 0.001);

        $this->asAdmin()->post(route('admin.trade.store'), $base + ['profit_loss' => -250]); // loses more than the stake
        $this->assertEqualsWithDelta(430, (float) $user->fresh()->balance, 0.001); // only the stake is lost
    }

    public function test_close_pays_stake_plus_profit_once_and_loss_button_is_a_loss(): void
    {
        $user = $this->member(['balance' => 0]);
        $win = $this->trade($user);
        $lose = $this->trade($user);

        $this->asAdmin()->post(route('admin.trade.close', $win->id), ['profit_loss' => 25, 'action' => 'profit'])->assertRedirect(route('admin.trade.history'));
        $this->assertEqualsWithDelta(125, (float) $user->fresh()->balance, 0.001);

        // "Loss" button sends a positive number; it must be booked as -40, not +40.
        $this->asAdmin()->post(route('admin.trade.close', $lose->id), ['profit_loss' => 40, 'action' => 'loss']);
        $this->assertEqualsWithDelta(-40, (float) $lose->fresh()->profit_loss, 0.001);
        $this->assertEqualsWithDelta(185, (float) $user->fresh()->balance, 0.001);

        // Closing again (double click / second admin) must not pay again.
        $this->asAdmin()->post(route('admin.trade.close', $win->id), ['profit_loss' => 25, 'action' => 'profit'])->assertSessionHas('error');
        $this->assertEqualsWithDelta(185, (float) $user->fresh()->balance, 0.001);
    }

    public function test_close_redirects_to_trade_history_and_the_old_list_urls_land_there_too(): void
    {
        $user = $this->member();
        $t = $this->trade($user);

        $this->asAdmin()->post(route('admin.trade.close', $t->id), ['profit_loss' => 5, 'action' => 'profit'])->assertRedirect(route('admin.trade.history'));
        $this->asAdmin()->get('/admin/open-trades')->assertRedirect('/admin/trade-history');
        $this->asAdmin()->get('/admin/closed-trades')->assertRedirect('/admin/trade-history');
        $this->asAdmin()->get(route('admin.trade.history', ['user' => $user->id]))->assertOk()
            ->assertViewHas('closedTrades', fn ($c) => $c->count() === 1);
    }

    public function test_editing_pnl_of_a_closed_trade_moves_the_balance_by_the_difference_only(): void
    {
        $user = $this->member(['balance' => 0]);
        $open = $this->trade($user, ['profit_loss' => 5]);
        $closed = $this->trade($user, ['status' => 'closed', 'profit_loss' => 10]);

        $this->asAdmin()->post(route('admin.trade.edit-pnl', $open->id), ['profit_loss' => 50])->assertJson(['success' => true]);
        $this->assertEqualsWithDelta(0, (float) $user->fresh()->balance, 0.001); // open: paid at close

        $this->asAdmin()->post(route('admin.trade.edit-pnl', $closed->id), ['profit_loss' => 30])->assertJson(['success' => true]);
        $this->assertEqualsWithDelta(20, (float) $user->fresh()->balance, 0.001);
    }

    public function test_deleting_an_open_trade_returns_the_stake_a_closed_one_returns_nothing(): void
    {
        $user = $this->member(['balance' => 0]);
        $open = $this->trade($user);
        $closed = $this->trade($user, ['status' => 'closed']);

        $this->asAdmin()->delete(route('admin.trade.destroy', $open->id))->assertSessionHas('success');
        $this->assertEqualsWithDelta(100, (float) $user->fresh()->balance, 0.001);

        $this->asAdmin()->delete(route('admin.trade.destroy', $closed->id));
        $this->assertEqualsWithDelta(100, (float) $user->fresh()->balance, 0.001);
        $this->assertSame(0, Trade::count());
    }

    private function live(array $attrs = []): LiveTrade
    {
        return LiveTrade::create($attrs + [
            'asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'market', 'side' => 'buy', 'quantity' => 0.01,
            'amount' => 100, 'leverage' => 5, 'status' => 'open', 'entry_price' => 50000, 'filled_at' => now(),
        ]);
    }

    public function test_admin_sees_trade_room_orders_and_filters_them(): void
    {
        $a = $this->member();
        $b = $this->member();
        $this->live(['user_id' => $a->id, 'symbol' => 'BTC']);
        $this->live(['user_id' => $b->id, 'symbol' => 'ETH', 'status' => 'closed', 'realized_pnl' => 12]);

        $this->asAdmin()->get(route('admin.live-trades.index'))->assertOk()->assertSee('BTC')->assertSee('ETH');
        $this->asAdmin()->get(route('admin.live-trades.index', ['status' => 'closed']))->assertOk()->assertSee('ETH')->assertDontSee($a->email);
        $this->asAdmin()->get(route('admin.live-trades.index', ['user' => $a->id]))->assertOk()->assertSee('BTC')->assertDontSee($b->email);
        $this->asAdmin()->get(route('admin.live-trades.index', ['q' => 'eth']))->assertOk()->assertSee('ETH');
    }

    public function test_admin_can_settle_an_open_trade_room_position_into_the_trading_balance(): void
    {
        $user = $this->member(['trading_balance' => 0]);
        $win = $this->live(['user_id' => $user->id]);
        $lose = $this->live(['user_id' => $user->id]);
        $wipeout = $this->live(['user_id' => $user->id]);

        $this->asAdmin()->post(route('admin.live-trades.close', $win), ['profit_loss' => 40])->assertSessionHas('success');
        $this->assertEqualsWithDelta(140, (float) $user->fresh()->trading_balance, 0.001);
        $this->assertSame('closed', $win->fresh()->status);
        $this->assertEqualsWithDelta(40, (float) $win->fresh()->realized_pnl, 0.001);

        $this->asAdmin()->post(route('admin.live-trades.close', $lose), ['profit_loss' => -30]);
        $this->assertEqualsWithDelta(210, (float) $user->fresh()->trading_balance, 0.001);

        $this->asAdmin()->post(route('admin.live-trades.close', $wipeout), ['profit_loss' => -999]); // can lose at most the margin
        $this->assertEqualsWithDelta(210, (float) $user->fresh()->trading_balance, 0.001);
        $this->assertEqualsWithDelta(-100, (float) $wipeout->fresh()->realized_pnl, 0.001);

        $this->assertDatabaseHas('user_notifications', ['user_id' => $user->id, 'type' => 'trade_closed']);
    }

    public function test_settling_twice_pays_once(): void
    {
        $user = $this->member(['trading_balance' => 0]);
        $t = $this->live(['user_id' => $user->id]);

        $this->asAdmin()->post(route('admin.live-trades.close', $t), ['profit_loss' => 10]);
        $this->asAdmin()->post(route('admin.live-trades.close', $t), ['profit_loss' => 10])->assertSessionHas('error');

        $this->assertEqualsWithDelta(110, (float) $user->fresh()->trading_balance, 0.001);
    }

    public function test_cancelling_a_pending_order_refunds_margin_and_open_ones_cannot_be_cancelled(): void
    {
        $user = $this->member(['trading_balance' => 0]);
        $pending = $this->live(['user_id' => $user->id, 'status' => 'pending', 'order_type' => 'limit', 'price' => 49000, 'entry_price' => null, 'filled_at' => null]);
        $open = $this->live(['user_id' => $user->id]);

        $this->asAdmin()->post(route('admin.live-trades.cancel', $pending))->assertSessionHas('success');
        $this->assertEqualsWithDelta(100, (float) $user->fresh()->trading_balance, 0.001);
        $this->assertSame('cancelled', $pending->fresh()->status);

        $this->asAdmin()->post(route('admin.live-trades.cancel', $open))->assertSessionHas('error');
        $this->assertEqualsWithDelta(100, (float) $user->fresh()->trading_balance, 0.001);
    }

    public function test_only_finished_trade_room_orders_can_be_deleted(): void
    {
        $user = $this->member();
        $open = $this->live(['user_id' => $user->id]);
        $closed = $this->live(['user_id' => $user->id, 'status' => 'closed']);

        $this->asAdmin()->delete(route('admin.live-trades.destroy', $open))->assertSessionHas('error');
        $this->assertDatabaseHas('live_trades', ['id' => $open->id]);

        $this->asAdmin()->delete(route('admin.live-trades.destroy', $closed))->assertSessionHas('success');
        $this->assertDatabaseMissing('live_trades', ['id' => $closed->id]);
    }

    public function test_admin_dashboard_counts_trade_room_orders(): void
    {
        $user = $this->member();
        $this->live(['user_id' => $user->id]);
        $this->live(['user_id' => $user->id, 'status' => 'liquidated']);

        $this->asAdmin()->get(route('admin.dashboard'))->assertOk()->assertViewHas('activeTrades', 1)->assertViewHas('closedTrades', 1);
    }
}
