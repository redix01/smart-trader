<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_guest_notification_poll_does_not_become_the_intended_url(): void
    {
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->get('/user/notifications/unread-count');

        $response->assertUnauthorized();
        $this->assertNull(session('url.intended'));
    }

    public function test_new_users_can_register(): void
    {
        $response = $this
            ->withHeader('User-Agent', 'Mozilla/5.0 Registration Test')
            ->withSession([
                'url.intended' => '/user/notifications/unread-count',
            ])
            ->post('/register', [
                'name' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@fortismarketpro.com',
                'phone' => '+1234567890',
                'country' => 'United States',
                'currency' => 'USD',
                'password' => 'password',
                'password_confirmation' => 'password',
                'registration_time' => time() - 4,
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('user.dashboard'));
        $this->assertNull(session('url.intended'));
    }
}
