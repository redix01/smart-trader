<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWithdrawalHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_withdrawals_page_renders_bank_withdrawals_and_orphans(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 50,
            'payment_method' => 'bank',
            'from_account' => 'balance',
            'bank' => ['bank_name' => 'First Bank', 'acct_name' => 'Jane', 'acct_number' => '123'],
            'status' => 0,
        ]);
        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 25,
            'payment_method' => 'bank',
            'from_account' => 'balance',
            'bank' => json_encode(['bank_name' => 'Legacy Bank']),
            'status' => 0,
        ]);
        $user->delete();

        $this->actingAs($admin)
            ->get(route('admin.transactions.withdrawals'))
            ->assertOk()
            ->assertSee('First Bank')
            ->assertSee('Legacy Bank');
    }
}
