<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DepositHistoryTest extends TestCase
{
    use RefreshDatabase;

    private function activePaymentMethod(): PaymentMethod
    {
        return PaymentMethod::create([
            'wallet' => 'Bitcoin',
            'crypto_type' => 'bitcoin',
            'address' => 'bc1qtestaddress',
            'is_active' => true,
        ]);
    }

    public function test_submitted_deposit_appears_in_user_and_admin_history(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $method = $this->activePaymentMethod();

        $response = $this->actingAs($user)->post(route('user.payment'), [
            'amount' => 150.00,
            'wallet_type' => 'balance',
            'payment_method_id' => $method->id,
            'proof' => UploadedFile::fake()->image('proof.png'),
        ]);

        $response->assertRedirect(route('user.deposit'));
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        // The success message must render in the always-visible page-level
        // feedback block (not only inside the hidden deposit modal).
        $page = $this->actingAs($user)->followRedirects($response);
        $page->assertSee('id="deposit-page-feedback"', false);
        $page->assertSee('Deposit submitted successfully');

        $this->assertDatabaseHas('deposits', [
            'user_id' => $user->id,
            'amount' => 150.00,
            'wallet_type' => 'balance',
            'payment_method_id' => $method->id,
            'status' => 0,
        ]);

        // Same query as TransactionController@index (user transaction history)
        $userDeposits = Deposit::where('user_id', $user->id)
            ->with('payment_method')
            ->latest()
            ->get();
        $this->assertCount(1, $userDeposits);

        // Same query as DepositController@deposit (deposit page history)
        $depositPageDeposits = Deposit::whereUserId($user->id)
            ->with('payment_method')
            ->latest()
            ->get();
        $this->assertCount(1, $depositPageDeposits);

        // Same query as Admin\TransactionController@deposits (admin history)
        $adminDeposits = Deposit::with(['user', 'payment_method'])
            ->latest()
            ->get();
        $this->assertCount(1, $adminDeposits);
        $this->assertEquals($user->id, $adminDeposits->first()->user_id);
    }

    public function test_every_wallet_type_persists_and_reads_back(): void
    {
        $user = User::factory()->create();

        foreach (['balance', 'trading', 'holding', 'staking'] as $wallet) {
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => 25.00,
                'payment_method_id' => null,
                'wallet_type' => $wallet,
                'proof' => 'deposits/test.png',
                'status' => 0,
            ]);

            $this->assertEquals($wallet, $deposit->fresh()->wallet_type);
        }

        $this->assertEquals(
            4,
            Deposit::where('user_id', $user->id)->count()
        );
    }
}
