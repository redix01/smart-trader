<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class TradingStrengthTest extends TestCase
{
    public function test_trading_strength_is_zero_with_no_balance(): void
    {
        $user = new User(['trading_balance' => 0]);

        $this->assertEquals(0.0, $user->trading_strength);
    }

    public function test_trading_strength_scales_with_balance(): void
    {
        $user = new User(['trading_balance' => 25000]);

        $this->assertEquals(25.0, $user->trading_strength);
    }

    public function test_trading_strength_is_capped_at_one_hundred(): void
    {
        $user = new User(['trading_balance' => 200000]);

        $this->assertEquals(100.0, $user->trading_strength);
    }

    public function test_trading_strength_label_matches_strength(): void
    {
        $this->assertEquals('Learning Phase', (new User(['trading_balance' => 0]))->trading_strength_label);
        $this->assertEquals('Good Performance', (new User(['trading_balance' => 30000]))->trading_strength_label);
        $this->assertEquals('Strong Performance', (new User(['trading_balance' => 60000]))->trading_strength_label);
        $this->assertEquals('Elite Performance', (new User(['trading_balance' => 100000]))->trading_strength_label);
    }
}
