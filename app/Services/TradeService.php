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
    private const STOCK_PAIRS = [
        ['id' => 101, 'name' => 'AAPL/USD', 'price' => '182.50', 'change' => '+1.20%', 'up' => true, 'icon' => ''],
        ['id' => 102, 'name' => 'TSLA/USD', 'price' => '245.30', 'change' => '-0.80%', 'up' => false, 'icon' => ''],
        ['id' => 103, 'name' => 'NVDA/USD', 'price' => '875.20', 'change' => '+2.10%', 'up' => true, 'icon' => ''],
        ['id' => 104, 'name' => 'MSFT/USD', 'price' => '415.80', 'change' => '+0.50%', 'up' => true, 'icon' => ''],
        ['id' => 105, 'name' => 'AMZN/USD', 'price' => '185.40', 'change' => '-0.30%', 'up' => false, 'icon' => ''],
        ['id' => 106, 'name' => 'GOOGL/USD', 'price' => '175.60', 'change' => '+1.00%', 'up' => true, 'icon' => ''],
    ];
    private const FOREX_PAIRS = [
        ['id' => 201, 'name' => 'EUR/USD', 'price' => '1.0845', 'change' => '+0.10%', 'up' => true, 'icon' => ''],
        ['id' => 202, 'name' => 'GBP/USD', 'price' => '1.2720', 'change' => '-0.20%', 'up' => false, 'icon' => ''],
        ['id' => 203, 'name' => 'USD/JPY', 'price' => '149.85', 'change' => '+0.30%', 'up' => true, 'icon' => ''],
        ['id' => 204, 'name' => 'AUD/USD', 'price' => '0.6542', 'change' => '-0.10%', 'up' => false, 'icon' => ''],
        ['id' => 205, 'name' => 'USD/CAD', 'price' => '1.3580', 'change' => '+0.05%', 'up' => true, 'icon' => ''],
        ['id' => 206, 'name' => 'USD/CHF', 'price' => '0.9012', 'change' => '-0.15%', 'up' => false, 'icon' => ''],
    ];

    public function __construct(
        private WalletService $wallets,
        private UserNotificationService $notifications,
    ) {}

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

    public function getStaticPairs(string $marketType): array
    {
        return match ($marketType) {
            'stocks' => self::STOCK_PAIRS,
            'forex' => self::FOREX_PAIRS,
            default => [],
        };
    }

    public function placeOrder(
        User $user,
        string $marketType,
        ?int $pairId,
        string $pairName,
        string $side,
        string $type,
        float $amount,
        ?float $price = null
    ): Order
    {
        return DB::transaction(function () use ($user, $marketType, $pairId, $pairName, $side, $type, $amount, $price) {
            $pair = $this->resolvePair($marketType, $pairId, $pairName);
            $executionPrice = $price ?: $pair['price'];
            $subtotal = $amount * $executionPrice;
            $fee = $subtotal * (self::FEE_PERCENT / 100);
            $isCashSettled = in_array($pair['quote_currency'], ['USD', 'USDT'], true);

            if ($side === 'buy') {
                if ($isCashSettled) {
                    $this->wallets->debitCash($user, $subtotal + $fee);
                } else {
                    $this->wallets->debit($user, $pair['quote_currency'], $subtotal + $fee);
                }

                $this->wallets->credit($user, $pair['base_currency'], $amount);
            } else {
                $this->wallets->debit($user, $pair['base_currency'], $amount);

                if ($isCashSettled) {
                    $this->wallets->creditCash($user, $subtotal - $fee);
                } else {
                    $this->wallets->credit($user, $pair['quote_currency'], $subtotal - $fee);
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'type' => strtolower($type),
                'side' => $side,
                'pair' => $pair['name'],
                'price' => $executionPrice,
                'amount' => $amount,
                'filled' => $amount,
                'total' => $subtotal,
                'fee' => $fee,
                'status' => 'completed',
                'closed_at' => now(),
            ]);

            DB::afterCommit(fn () => $this->notifications->sendTradeCompleted($user, $order));

            return $order;
        });
    }

    private function resolvePair(string $marketType, ?int $pairId, string $pairName): array
    {
        if ($marketType === 'crypto') {
            $pair = MarketPair::whereKey($pairId)
                ->where('is_active', true)
                ->firstOrFail();

            return [
                'name' => $pair->name,
                'base_currency' => $pair->base_currency,
                'quote_currency' => $pair->quote_currency,
                'price' => (float) $pair->current_price,
            ];
        }

        $pair = collect($this->getStaticPairs($marketType))->firstWhere('name', $pairName);

        abort_if(! $pair, 422, 'Unsupported trading pair.');

        [$baseCurrency, $quoteCurrency] = explode('/', $pair['name']);

        return [
            'name' => $pair['name'],
            'base_currency' => $baseCurrency,
            'quote_currency' => $quoteCurrency,
            'price' => (float) $pair['price'],
        ];
    }
}
