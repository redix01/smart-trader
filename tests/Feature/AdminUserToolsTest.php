<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Deposit;
use App\Models\LiveTrade;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** Admin acts on a user: in-app messages, trades placed for them, and Deposits-balance credits. */
class AdminUserToolsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Http::fake(fn () => Http::response([['symbol' => 'BTCUSDT', 'price' => '100']]));
        Asset::create(['symbol' => 'BTC', 'name' => 'Bitcoin', 'type' => 'crypto', 'current_price' => 100, 'is_active' => true]);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function customer(array $attrs = []): User
    {
        $u = User::factory()->create(['role' => 'user']);
        $u->forceFill($attrs + ['currency' => 'USD'])->save();

        return $u->fresh();
    }

    public function test_admin_message_reaches_selected_users_and_shows_on_dashboard(): void
    {
        $a = $this->customer();
        $b = $this->customer();
        $other = $this->customer();

        $this->actingAs($this->admin())->post(route('admin.user-messages.send'), [
            'audience' => 'selected',
            'user_ids' => [$a->id, $b->id],
            'title' => 'Account review done',
            'message' => 'Your account is fully verified.',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame(1, UserNotification::where('user_id', $a->id)->where('type', 'admin_message')->whereNull('read_at')->count());
        $this->assertSame(1, UserNotification::where('user_id', $b->id)->count());
        $this->assertSame(0, UserNotification::where('user_id', $other->id)->count());

        $this->actingAs($a)->get(route('user.dashboard'))->assertOk()
            ->assertSee('Account review done')->assertSee('Your account is fully verified.');
    }

    public function test_admin_message_to_all_users(): void
    {
        $this->customer();
        $this->customer();

        $this->actingAs($this->admin())->post(route('admin.user-messages.send'), [
            'audience' => 'all', 'title' => 'Maintenance', 'message' => 'Tonight 2am.',
        ])->assertSessionHas('success');

        $this->assertSame(2, UserNotification::where('type', 'admin_message')->count());
    }

    public function test_non_admin_cannot_send_messages_or_deposit(): void
    {
        $user = $this->customer();

        $this->actingAs($user)->post(route('admin.user-messages.send'), [
            'audience' => 'all', 'title' => 'x', 'message' => 'y',
        ])->assertStatus(302);
        $this->actingAs($user)->post(route('admin.user.deposit', $user->id), ['amount' => 100]);

        $this->assertSame(0, UserNotification::count());
        $this->assertEqualsWithDelta(0, (float) $user->fresh()->deposit_balance, 0.001);
    }

    public function test_admin_deposit_credits_deposits_balance_and_shows_on_dashboard(): void
    {
        $user = $this->customer();

        $this->actingAs($this->admin())->post(route('admin.user.deposit', $user->id), ['amount' => 250.50, 'note' => 'Wire received'])
            ->assertRedirect()->assertSessionHas('success');

        $this->assertEqualsWithDelta(250.50, (float) $user->fresh()->deposit_balance, 0.001);
        $this->assertSame(1, Deposit::where('user_id', $user->id)->where('status', 1)->where('wallet_type', 'deposit')->count());
        $this->assertSame(1, UserNotification::where('user_id', $user->id)->where('type', 'deposit')->count());

        $this->actingAs($user)->get(route('user.dashboard'))->assertOk()
            ->assertSee('Deposits')->assertSee('$250.50');
    }

    public function test_admin_places_trade_for_user_from_trading_balance(): void
    {
        $user = $this->customer(['trading_balance' => 1000]);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.user-trades.store'), [
            'user_id' => $user->id, 'asset_type' => 'crypto', 'symbol' => 'BTC', 'side' => 'buy',
            'amount' => 200, 'leverage' => 5, 'fund_from' => 'trading_balance',
        ])->assertRedirect(route('admin.user-trades.index'))->assertSessionHas('success');

        $trade = LiveTrade::sole();
        $this->assertSame($user->id, $trade->user_id);
        $this->assertSame($admin->id, $trade->placed_by);
        $this->assertSame(LiveTrade::STATUS_OPEN, $trade->status);
        $this->assertEqualsWithDelta(800, (float) $user->fresh()->trading_balance, 0.001);
        $this->assertSame(1, UserNotification::where('user_id', $user->id)->where('type', 'trade')->count());

        $this->actingAs($user)->get(route('user.dashboard'))->assertOk()
            ->assertSee('BTC')->assertSee('Placed by your account manager');
    }

    public function test_admin_trade_can_be_funded_from_deposits_and_is_refused_when_short(): void
    {
        $user = $this->customer(['deposit_balance' => 300, 'trading_balance' => 0]);
        $payload = ['user_id' => $user->id, 'asset_type' => 'crypto', 'symbol' => 'BTC', 'side' => 'sell', 'leverage' => 1, 'fund_from' => 'deposit_balance'];

        $this->actingAs($this->admin())->post(route('admin.user-trades.store'), $payload + ['amount' => 500])
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Insufficient Deposits balance'));
        $this->assertSame(0, LiveTrade::count());

        $this->actingAs($this->admin())->post(route('admin.user-trades.store'), $payload + ['amount' => 100])
            ->assertSessionHas('success');
        $user->refresh();
        $this->assertEqualsWithDelta(200, (float) $user->deposit_balance, 0.001);
        $this->assertEqualsWithDelta(0, (float) $user->trading_balance, 0.001);
        $this->assertSame(1, LiveTrade::count());
    }

    public function test_admin_can_close_a_trade_it_placed(): void
    {
        $user = $this->customer(['trading_balance' => 1000]);
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.user-trades.store'), [
            'user_id' => $user->id, 'asset_type' => 'crypto', 'symbol' => 'BTC', 'side' => 'buy',
            'amount' => 100, 'leverage' => 1, 'fund_from' => 'trading_balance',
        ]);

        $this->actingAs($admin)->post(route('admin.user-trades.close', LiveTrade::sole()))->assertSessionHas('success');

        $this->assertSame(LiveTrade::STATUS_CLOSED, LiveTrade::sole()->status);
        $this->assertEqualsWithDelta(1000, (float) $user->fresh()->trading_balance, 0.001);
    }

    public function test_admin_pages_render(): void
    {
        $user = $this->customer();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.user-messages.index', ['user' => $user->id]))->assertOk()->assertSee('User Messages');
        $this->actingAs($admin)->get(route('admin.user-trades.index', ['user' => $user->id]))->assertOk()->assertSee('Trade for User');
        $this->actingAs($admin)->get(route('admin.user.show', $user->id))->assertOk()
            ->assertSee('Deposit to Deposits balance')->assertSee('Send message')->assertSee('Place a trade for this user');
    }
}
