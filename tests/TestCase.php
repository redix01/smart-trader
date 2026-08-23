<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // HoneypotMiddleware treats missing/short User-Agent strings as bot
        // traffic on auth routes; the test client sends none by default.
        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Test Suite) AppleWebKit/537.36',
        ]);
    }
}
