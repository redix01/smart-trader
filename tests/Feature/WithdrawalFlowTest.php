<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_tier' => 'user',
            'kyc_level' => 'verified',
        ]);

        Wallet::factory()->create([
            'user_id' => $this->user->id,
            'currency' => 'USD',
            'balance' => 10000,
        ]);
    }

    public function test_user_can_view_withdrawal_page(): void
    {
        $this->actingAs($this->user)
            ->get(route('withdraw'))
            ->assertOk();
    }

    public function test_user_can_submit_withdrawal(): void
    {
        $this->actingAs($this->user)
            ->post(route('withdraw.store'), [
                'method' => 'wallet',
                'amount' => 500,
                'currency' => 'USD',
                'destination' => ['wallet_address' => '0x1234567890abcdef'],
            ])->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $this->user->id,
            'amount' => 500,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_withdrawal(): void
    {
        $admin = User::factory()->create(['account_tier' => 'admin']);
        $withdrawal = Withdrawal::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.approve', $withdrawal->id))
            ->assertSessionHas('success');

        $this->assertEquals('approved', $withdrawal->fresh()->status);
    }

    public function test_admin_can_reject_withdrawal(): void
    {
        $admin = User::factory()->create(['account_tier' => 'admin']);
        $withdrawal = Withdrawal::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.reject', $withdrawal->id), [
                'reason' => 'Suspicious activity',
            ])->assertSessionHas('success');

        $this->assertEquals('rejected', $withdrawal->fresh()->status);
    }
}
