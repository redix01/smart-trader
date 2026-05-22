<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\DepositMethod;
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

        $this->actingAs($admin)
            ->post(route('admin.deposits.approve', $deposit->id))
            ->assertSessionHas('success');

        $this->actingAs($user)
            ->get(route('assets'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Assets')
                ->has('deposits', 1, fn ($deposit) => $deposit
                    ->where('method', 'USDT (TRC20)')
                    ->where('currency', 'USDT')
                    ->where('net', '500.00')
                    ->etc()
                )
            );
    }
}
