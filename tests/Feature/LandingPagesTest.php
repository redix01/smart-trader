<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_landing_pages_can_be_rendered(): void
    {
        $pages = [
            '/',
            '/about',
            '/faqs',
            '/copy',
            '/crypto',
            '/forex',
            '/stocks',
            '/privacy-policy',
            '/rules',
        ];

        foreach ($pages as $page) {
            $this->get($page)->assertOk();
        }
    }

    public function test_main_landing_page_uses_the_quantum_extrade_design(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('QuantumExtrade', false)
            ->assertSee('Advanced Trading', false)
            ->assertSee('AI-Powered', false)
            ->assertSee('Trading Advantages', false)
            ->assertDontSee('All your trading essentials integrated into one app', false)
            ->assertDontSee('Multiple ways to trade', false);
    }
}
