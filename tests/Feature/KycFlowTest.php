<?php

namespace Tests\Feature;

use App\Models\KycSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KycFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_tier' => 'user',
            'kyc_level' => 'unverified',
        ]);

        $this->admin = User::factory()->create([
            'account_tier' => 'admin',
            'kyc_level' => 'verified',
        ]);
    }

    public function test_admin_can_view_kyc_list(): void
    {
        KycSubmission::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.kyc.index'))
            ->assertOk()
            ->assertSee($this->user->name);
    }

    public function test_admin_can_approve_kyc(): void
    {
        $kyc = KycSubmission::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.kyc.approve', $kyc->id))
            ->assertSessionHas('success');

        $this->assertEquals('approved', $kyc->fresh()->status);
        $this->assertEquals('verified', $this->user->fresh()->kyc_level);
    }

    public function test_admin_can_reject_kyc(): void
    {
        $kyc = KycSubmission::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.kyc.reject', $kyc->id), [
                'reason' => 'Invalid ID document',
            ])->assertSessionHas('success');

        $this->assertEquals('rejected', $kyc->fresh()->status);
    }

    public function test_non_admin_cannot_approve_kyc(): void
    {
        $kyc = KycSubmission::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user)
            ->post(route('admin.kyc.approve', $kyc->id))
            ->assertForbidden();
    }
}
