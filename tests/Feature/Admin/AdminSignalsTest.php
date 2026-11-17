<?php

namespace Tests\Feature\Admin;

use App\Models\Plan;
use App\Models\TradingSignal;

class AdminSignalsTest extends AdminTestCase
{
    private function signalPlan(): Plan
    {
        return Plan::create(['name' => 'Signals', 'type' => 'signal', 'price' => 10, 'currency' => 'USD', 'is_active' => true]);
    }

    private function payload(Plan $plan, array $over = []): array
    {
        return $over + [
            'plan_id' => $plan->id, 'name' => 'BTC breakout', 'trade_pair' => 'btc/usd', 'signal_type' => 'buy', 'status' => 'active',
            'entry_price' => 60000, 'target_price' => 65000, 'stop_loss' => 58000, 'description' => 'Strong momentum',
            'expiry_time' => now()->addDay()->format('Y-m-d\TH:i'),
        ];
    }

    public function test_the_create_form_creates_a_signal_the_user_dashboard_columns_can_read(): void
    {
        $plan = $this->signalPlan();

        $this->asAdmin()->get(route('admin.signals.create'))->assertOk()->assertSee('name="expiry_time"', false);
        $this->asAdmin()->post(route('admin.signals.store'), $this->payload($plan))->assertRedirect(route('admin.signals.index'))->assertSessionHasNoErrors();

        $s = TradingSignal::first();
        $this->assertSame('BTC breakout', $s->title);
        $this->assertSame('BTC/USD', $s->symbol);
        $this->assertSame('buy', $s->type);
        $this->assertEqualsWithDelta(65000, (float) $s->take_profit, 0.001);
        $this->assertSame('Strong momentum', $s->analysis);
        $this->assertSame($this->admin->id, $s->created_by);
        $this->assertTrue($s->is_active);
    }

    public function test_validation_errors_come_back_to_the_form(): void
    {
        $plan = $this->signalPlan();

        $this->asAdmin()->post(route('admin.signals.store'), $this->payload($plan, ['signal_type' => 'hold', 'expiry_time' => now()->subDay()->format('Y-m-d\TH:i')]))
            ->assertSessionHasErrors(['signal_type', 'expiry_time']);
        $this->assertSame(0, TradingSignal::count());
    }

    public function test_view_edit_update_and_delete_work(): void
    {
        $plan = $this->signalPlan();
        $this->asAdmin()->post(route('admin.signals.store'), $this->payload($plan));
        $s = TradingSignal::first();

        $this->asAdmin()->get(route('admin.signals.index'))->assertOk()->assertSee('BTC breakout');
        $this->asAdmin()->get(route('admin.signals.show', $s))->assertOk()->assertSee('BTC breakout')->assertSee('Strong momentum');
        $this->asAdmin()->get(route('admin.signals.edit', $s))->assertOk()->assertSee('value="BTC breakout"', false);

        $this->asAdmin()->put(route('admin.signals.update', $s), $this->payload($plan, ['name' => 'BTC target hit', 'status' => 'completed']))
            ->assertRedirect(route('admin.signals.index'))->assertSessionHasNoErrors();
        $this->assertSame('BTC target hit', $s->fresh()->title);
        $this->assertSame('completed', $s->fresh()->status);

        $this->asAdmin()->delete(route('admin.signals.destroy', $s))->assertRedirect(route('admin.signals.index'));
        $this->assertSame(0, TradingSignal::count());
    }
}
