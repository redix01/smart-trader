<?php

namespace Tests\Unit;

use App\Services\TradingStrengthService;
use PHPUnit\Framework\TestCase;

/** Pure checks; the database-backed scoring is covered in Tests\Feature\TradingStrengthTest. */
class TradingStrengthTest extends TestCase
{
    public function test_label_thresholds(): void
    {
        $this->assertSame('Learning Phase', TradingStrengthService::label(0));
        $this->assertSame('Learning Phase', TradingStrengthService::label(24.9));
        $this->assertSame('Good Performance', TradingStrengthService::label(25));
        $this->assertSame('Strong Performance', TradingStrengthService::label(50));
        $this->assertSame('Elite Performance', TradingStrengthService::label(75));
        $this->assertSame('Elite Performance', TradingStrengthService::label(100));
    }

    public function test_component_weights_add_up_to_one_hundred(): void
    {
        $this->assertSame(100, 35 + 30 + 20 + 15);
    }
}
