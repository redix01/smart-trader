<?php

namespace App\Console\Commands;

use App\Services\TradeEngine;
use Illuminate\Console\Command;

/**
 * Fills Trade Room limit orders the market has reached and closes positions that hit
 * take-profit, stop-loss or liquidation. Idempotent: every change re-checks status under a row lock.
 */
class MatchTrades extends Command
{
    protected $signature = 'trades:match';

    protected $description = 'Fill reached limit orders and apply take-profit / stop-loss / liquidation to open positions';

    public function handle(TradeEngine $engine): int
    {
        $stats = $engine->match();
        $this->info("Filled {$stats['filled']} order(s), closed {$stats['closed']} position(s).");

        return self::SUCCESS;
    }
}
