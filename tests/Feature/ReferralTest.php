<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    use RefreshDatabase;

    public function test_referrals_page_displays_referral_link_and_records(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $referred = User::factory()->create([
            'referred_by' => $user->id,
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->get(route('user.referrals'));

        $response->assertOk();
        $response->assertSee(route('register', ['ref' => $user->referral_code]));
        $response->assertSee($referred->name);
    }

    public function test_referral_reward_is_created_on_first_deposit(): void
    {
        $referrer = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $referred = User::factory()->create([
            'referred_by' => $referrer->id,
            'password' => Hash::make('password'),
        ]);

        $deposit = Deposit::create([
            'user_id' => $referred->id,
            'amount' => 1000,
            'wallet_type' => 'balance',
            'status' => 0,
        ]);

        $record = app(ReferralService::class)->processDepositReward($referred, $deposit);

        $this->assertNotNull($record);
        $this->assertEquals(50.00, $record->amount);
        $this->assertEquals('paid', $record->status);

        $referrer->refresh();
        $this->assertEquals(50.00, $referrer->referral_balance);
    }
}
