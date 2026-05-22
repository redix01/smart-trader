<?php

namespace App\Services;

use App\Models\MarketPair;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TradeService
{
    private const FEE_PERCENT = 0.1;

    public function __construct(private WalletService $wallets) {}

    public function getUserTradeHistory(User $user, int $limit = 20): Collection
    {
        return $user->orders()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'pair' => $order->pair,
                'side' => $order->side,
                'type' => $order->type,
                'amount' => number_format((float) $order->amount, 6),
                'price' => number_format((float) $order->price, 2),
                'total' => number_format((float) $order->total, 2),
                'fee' => number_format((float) $order->fee, 2),
                'status' => $order->status,
                'date' => $order->created_at->format('Y-m-d H:i'),
            ]);
    }

    public function placeOrder(User $user, int $pairId, string $side, string $type, float $amount, ?float $price = null): Order
    {
        return DB::transaction(function () use ($user, $pairId, $side, $type, $amount, $price) {
            $pair = MarketPair::whereKey($pairId)
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            $executionPrice = $price ?: (float) $pair->current_price;
            $subtotal = $amount * $executionPrice;
            $fee = $subtotal * (self::FEE_PERCENT / 100);

            if ($side === 'buy') {
                $this->wallets->debit($user, $pair->quote_currency, $subtotal + $fee);
                $this->wallets->credit($user, $pair->base_currency, $amount);
            } else {
                $this->wallets->debit($user, $pair->base_currency, $amount);
                $this->wallets->credit($user, $pair->quote_currency, $subtotal - $fee);
            }

            return Order::create([
                'user_id' => $user->id,
                'type' => strtolower($type),
                'side' => $side,
                'pair' => $pair->name,
                'price' => $executionPrice,
                'amount' => $amount,
                'filled' => $amount,
                'total' => $subtotal,
                'fee' => $fee,
                'status' => 'completed',
                'closed_at' => now(),
            ]);
        });
    }
}
