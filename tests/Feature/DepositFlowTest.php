<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\DepositMethod;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DepositFlowTest extends TestCase
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

        DepositMethod::factory()->create(['id' => 1, 'currency' => 'USD']);
    }

    public function test_user_can_view_deposit_page(): void
    {
        $this->actingAs($this->user)
            ->get(route('deposit'))
            ->assertOk();
    }

    public function test_user_can_submit_deposit(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)
            ->post(route('deposit.store'), [
                'deposit_method_id' => 1,
                'amount' => 1000,
                'currency' => 'USD',
                'proof' => UploadedFile::fake()->image('proof.jpg'),
            ])->assertSessionHas('success');

        $this->assertDatabaseHas('deposits', [
            'user_id' => $this->user->id,
            'amount' => 1000,
            'currency' => 'USD',
            'status' => 'pending',
        ]);
    }

    public function test_guest_cannot_submit_deposit(): void
    {
        $this->post(route('deposit.store'), [
            'deposit_method_id' => 1,
            'amount' => 1000,
            'currency' => 'USD',
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_approve_deposit(): void
    {
        $admin = User::factory()->create(['account_tier' => 'admin']);
        $deposit = Deposit::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'currency' => 'USD',
            'amount' => 1000,
            'fee' => 0,
            'net_amount' => 1000,
        ]);

        $wallet = Wallet::factory()->create([
            'user_id' => $this->user->id,
            'currency' => 'USD',
            'balance' => 2500,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.deposits.approve', $deposit->id))
            ->assertSessionHas('success');

        $this->assertEquals('approved', $deposit->fresh()->status);
        $this->assertEquals('3500.00000000', $wallet->fresh()->balance);
    }

    public function test_admin_can_reject_deposit(): void
    {
        $admin = User::factory()->create(['account_tier' => 'admin']);
        $deposit = Deposit::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.deposits.reject', $deposit->id), [
                'reason' => 'Invalid proof document',
            ])->assertSessionHas('success');

        $this->assertEquals('rejected', $deposit->fresh()->status);
    }
}
