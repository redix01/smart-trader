<?php

namespace App\Services;

use App\Models\LiveTrade;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Executes Trade Room orders like a margin exchange:
 *  - Market order: fills now at the server price and opens a position.
 *  - Limit order: waits (pending) until the market reaches the limit, then fills at the limit price.
 *  - Open position: P&L = direction × (price − entry) × quantity; closes manually, on take-profit /
 *    stop-loss, or is liquidated when the loss reaches the margin.
 *
 * Money: `amount` is the margin taken from trading_balance (user's currency). Position size =
 * margin × leverage. Prices are converted from USD into the user's currency.
 * Every state change locks the trade (and user) row and re-checks status, so running the
 * matcher twice or double-clicking Close can never pay out twice.
 */
class TradeEngine
{
    public function __construct(private MarketPrice $prices) {}

    /** Current price of a market in the user's currency, or null if unavailable. */
    public function priceFor(User $user, string $type, string $symbol): ?float
    {
        $usd = $this->prices->usd($type, $symbol);

        return $usd ? $user->convertFrom($usd) : null;
    }

    /**
     * @param  array{asset_type:string,symbol:string,order_type:string,side:string,amount?:float|null,price?:float|null,quantity?:float|null,leverage?:float|null,take_profit?:float|null,stop_loss?:float|null}  $o
     */
    public function place(User $user, array $o): LiveTrade
    {
        $lev = max(1, min(100, (float) ($o['leverage'] ?? 1)));
        $market = $this->priceFor($user, $o['asset_type'], $o['symbol']);
        if (! $market) {
            throw ValidationException::withMessages(['symbol' => 'Live price for '.$o['symbol'].' is unavailable right now. Please try again shortly.']);
        }

        if ($o['order_type'] === 'market') {
            $margin = round((float) $o['amount'], 8);
            $reference = $market;
        } else {
            $limit = (float) $o['price'];
            $qty = (float) $o['quantity'];
            $margin = round($limit * $qty / $lev, 8);
            $reference = $limit;
        }

        $this->guardTargets($o['side'], $reference, $o['take_profit'] ?? null, $o['stop_loss'] ?? null);

        if ($margin < 1) {
            throw ValidationException::withMessages(['amount' => 'The minimum margin per order is '.money(1).'.']);
        }

        return DB::transaction(function () use ($user, $o, $lev, $margin, $market) {
            $locked = User::whereKey($user->getKey())->lockForUpdate()->first();
            if ($margin > (float) $locked->trading_balance) {
                throw ValidationException::withMessages(['amount' => 'Insufficient trading balance. This order needs '.money($margin).' of margin.']);
            }
            $locked->decrement('trading_balance', $margin);

            $base = [
                'user_id' => $locked->id,
                'asset_type' => $o['asset_type'],
                'symbol' => strtoupper($o['symbol']),
                'order_type' => $o['order_type'],
                'side' => $o['side'],
                'amount' => $margin,
                'leverage' => $lev,
                'take_profit' => $o['take_profit'] ?? null,
                'stop_loss' => $o['stop_loss'] ?? null,
            ];

            if ($o['order_type'] === 'market') {
                return LiveTrade::create($base + [
                    'entry_price' => $market,
                    'quantity' => round($margin * $lev / $market, 8),
                    'status' => LiveTrade::STATUS_OPEN,
                    'filled_at' => now(),
                ]);
            }

            $limit = (float) $o['price'];
            $trade = LiveTrade::create($base + [
                'price' => $limit,
                'quantity' => round((float) $o['quantity'], 8),
                'status' => LiveTrade::STATUS_PENDING,
            ]);
            // A limit that is already marketable executes now, at the better of limit and market.
            $marketable = $o['side'] === 'buy' ? $market <= $limit : $market >= $limit;
            if ($marketable) {
                $fill = $o['side'] === 'buy' ? min($market, $limit) : max($market, $limit);
                $trade->update(['status' => LiveTrade::STATUS_OPEN, 'entry_price' => $fill, 'filled_at' => now()]);
            }

            return $trade->fresh();
        });
    }

    /** Cancel a pending limit order and refund its margin. Returns false if it was no longer pending. */
    public function cancel(LiveTrade $trade): bool
    {
        return DB::transaction(function () use ($trade) {
            $t = LiveTrade::whereKey($trade->getKey())->lockForUpdate()->first();
            if (! $t || $t->status !== LiveTrade::STATUS_PENDING) {
                return false;
            }
            User::whereKey($t->user_id)->lockForUpdate()->first()->increment('trading_balance', (float) $t->amount);
            $t->update(['status' => LiveTrade::STATUS_CANCELLED, 'closed_at' => now()]);

            return true;
        });
    }

    /**
     * Close an open position at $price (user's currency). Credits margin + P&L (never below zero).
     * Returns the closed trade, or null if it was not open any more.
     */
    public function close(LiveTrade $trade, ?float $price = null, string $reason = 'manual'): ?LiveTrade
    {
        return DB::transaction(function () use ($trade, $price, $reason) {
            $t = LiveTrade::whereKey($trade->getKey())->lockForUpdate()->first();
            if (! $t || $t->status !== LiveTrade::STATUS_OPEN) {
                return null;
            }
            $user = User::whereKey($t->user_id)->lockForUpdate()->first();
            $price ??= $this->priceFor($user, $t->asset_type, $t->symbol);
            if (! $price) {
                throw ValidationException::withMessages(['symbol' => 'Live price for '.$t->symbol.' is unavailable right now. Please try again shortly.']);
            }

            $pnl = $t->pnlAt($price);
            $payout = max(0.0, (float) $t->amount + $pnl);
            if ($payout > 0) {
                $user->increment('trading_balance', round($payout, 8));
            }
            $t->update([
                'status' => $reason === 'liquidation' ? LiveTrade::STATUS_LIQUIDATED : LiveTrade::STATUS_CLOSED,
                'exit_price' => $price,
                'realized_pnl' => round($payout - (float) $t->amount, 8),
                'close_reason' => $reason,
                'closed_at' => now(),
            ]);

            return $t;
        });
    }

    /**
     * Admin settlement: close an open position with an explicit profit/loss (user's currency)
     * instead of a market price. Pays margin + P&L into trading_balance (never below zero),
     * exactly like a normal close, so the user's history and balance stay consistent.
     * Returns null if the position was not open any more.
     */
    public function settle(LiveTrade $trade, float $pnl, string $reason = 'admin'): ?LiveTrade
    {
        return DB::transaction(function () use ($trade, $pnl, $reason) {
            $t = LiveTrade::whereKey($trade->getKey())->lockForUpdate()->first();
            if (! $t || $t->status !== LiveTrade::STATUS_OPEN) {
                return null;
            }
            $user = User::whereKey($t->user_id)->lockForUpdate()->first();

            $payout = max(0.0, (float) $t->amount + $pnl);
            if ($payout > 0) {
                $user->increment('trading_balance', round($payout, 8));
            }
            $direction = $t->side === 'sell' ? -1 : 1;
            $exit = ($t->entry_price && (float) $t->quantity > 0)
                ? max(0.0, (float) $t->entry_price + $pnl / ((float) $t->quantity * $direction))
                : null;
            $t->update([
                'status' => LiveTrade::STATUS_CLOSED,
                'exit_price' => $exit,
                'realized_pnl' => round($payout - (float) $t->amount, 8),
                'close_reason' => $reason,
                'closed_at' => now(),
            ]);

            return $t;
        });
    }

    /**
     * Fill pending limit orders that the market has reached and close positions that hit
     * take-profit, stop-loss or liquidation. Safe to run repeatedly and concurrently.
     *
     * @return array{filled:int, closed:int}
     */
    public function match(?User $onlyUser = null): array
    {
        $stats = ['filled' => 0, 'closed' => 0];

        $query = LiveTrade::query()->whereIn('status', [LiveTrade::STATUS_PENDING, LiveTrade::STATUS_OPEN]);
        if ($onlyUser) {
            $query->where('user_id', $onlyUser->getKey());
        }

        $query->with('user')->orderBy('id')->chunkById(200, function (Collection $trades) use (&$stats) {
            $usd = $this->prices->many($trades->map(fn ($t) => [$t->asset_type, $t->symbol])->unique()->values()->all());

            foreach ($trades as $t) {
                $p = $usd[$this->prices->key($t->asset_type, $t->symbol)] ?? null;
                if (! $p || ! $t->user) {
                    continue;
                }
                $price = $t->user->convertFrom($p);

                if ($t->status === LiveTrade::STATUS_PENDING) {
                    $stats['filled'] += $this->fillIfReached($t, $price) ? 1 : 0;
                    continue;
                }

                if ($reason = $this->exitReason($t, $price)) {
                    // Take-profit / stop-loss execute at their trigger level; liquidation at the market.
                    $at = match ($reason) {
                        'take_profit' => (float) $t->take_profit,
                        'stop_loss' => (float) $t->stop_loss,
                        default => $price,
                    };
                    $stats['closed'] += $this->close($t, $at, $reason) ? 1 : 0;
                }
            }
        });

        return $stats;
    }

    private function fillIfReached(LiveTrade $trade, float $price): bool
    {
        $limit = (float) $trade->price;
        $reached = $trade->side === 'buy' ? $price <= $limit : $price >= $limit;
        if (! $reached) {
            return false;
        }

        return DB::transaction(function () use ($trade, $limit) {
            $t = LiveTrade::whereKey($trade->getKey())->lockForUpdate()->first();
            if (! $t || $t->status !== LiveTrade::STATUS_PENDING) {
                return false;
            }
            $t->update(['status' => LiveTrade::STATUS_OPEN, 'entry_price' => $limit, 'filled_at' => now()]);

            return true;
        });
    }

    private function exitReason(LiveTrade $t, float $price): ?string
    {
        if ($t->pnlAt($price) <= -(float) $t->amount) {
            return 'liquidation';
        }
        $buy = $t->side !== 'sell';
        if ($t->take_profit && ($buy ? $price >= $t->take_profit : $price <= $t->take_profit)) {
            return 'take_profit';
        }
        if ($t->stop_loss && ($buy ? $price <= $t->stop_loss : $price >= $t->stop_loss)) {
            return 'stop_loss';
        }

        return null;
    }

    private function guardTargets(string $side, float $reference, $tp, $sl): void
    {
        $buy = $side === 'buy';
        if ($tp !== null && $tp !== '' && ($buy ? $tp <= $reference : $tp >= $reference)) {
            throw ValidationException::withMessages(['take_profit' => 'Take profit must be '.($buy ? 'above' : 'below').' the entry price ('.money($reference).').']);
        }
        if ($sl !== null && $sl !== '' && ($buy ? $sl >= $reference : $sl <= $reference)) {
            throw ValidationException::withMessages(['stop_loss' => 'Stop loss must be '.($buy ? 'below' : 'above').' the entry price ('.money($reference).').']);
        }
    }
}
