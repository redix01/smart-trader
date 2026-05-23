<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\DepositMethod;
use App\Models\MarketPair;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_deposits_are_visible_on_assets_page(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['account_tier' => 'admin']);
        $method = DepositMethod::factory()->create([
            'currency' => 'USDT',
            'label' => 'USDT (TRC20)',
        ]);

        $deposit = Deposit::factory()->create([
            'user_id' => $user->id,
            'deposit_method_id' => $method->id,
            'currency' => 'USDT',
            'amount' => 500,
            'fee' => 0,
            'net_amount' => 500,
            'status' => 'pending',
        ]);

        Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'USDT',
            'balance' => 0,
        ]);

        MarketPair::create([
            'base_currency' => 'USDT',
            'quote_currency' => 'USDT',
            'current_price' => 1,
            'price_change_24h' => 0,
            'high_24h' => 1,
            'low_24h' => 1,
            'volume_24h' => 0,
            'market_cap' => 0,
            'is_active' => true,
            'sort_order' => 1,
            'icon' => 'https://assets.coingecko.com/coins/images/325/small/tether.png',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.deposits.approve', $deposit->id))
            ->assertSessionHas('success');

        $this->actingAs($user)
            ->get(route('assets'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Assets')
                ->has('assets', 1, fn ($asset) => $asset
                    ->where('symbol', 'USDT')
                    ->where('price_usd', 1)
                    ->where('value_usd', 500)
                    ->etc()
                )
                ->has('deposits', 1, fn ($deposit) => $deposit
                    ->where('method', 'USDT (TRC20)')
                    ->where('currency', 'USDT')
                    ->where('net', '500.00')
                    ->etc()
                )
            );
    }
}
