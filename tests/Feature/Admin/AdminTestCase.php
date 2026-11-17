<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    protected function member(array $attrs = []): User
    {
        return User::factory()->create($attrs + [
            'currency' => 'USD', 'balance' => 0, 'trading_balance' => 0, 'mining_balance' => 0,
            'holding_balance' => 0, 'staking_balance' => 0, 'referral_balance' => 0,
        ]);
    }

    protected function asAdmin()
    {
        return $this->actingAs($this->admin);
    }
}
