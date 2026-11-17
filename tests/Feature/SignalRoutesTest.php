<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignalRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixed_paths_are_not_swallowed_by_wildcards(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/user/signals/statistics')->assertOk();
        $this->actingAs($user)->get('/user/signal-subscriptions/active')->assertRedirect('/user/signal-subscriptions');
        $this->actingAs($user)->get('/user/signal-subscriptions/history')->assertRedirect('/user/signal-subscriptions');
        $this->actingAs($user)->get('/user/staking/statistics')->assertOk();
        $this->actingAs($user)->get('/user/mining/statistics')->assertOk();
    }
}
