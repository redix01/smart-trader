<?php

namespace App\Services;

use App\Models\User;
use App\Models\SwapQuote;
use Illuminate\Database\Eloquent\Collection;

class SwapService
{
    private const FEES_PERCENT = 0.05;

    public function getSupportedPairs(): array
    {
        return [
            ['from' => 'USD', 'to' => 'BTC', 'rate' => 0.000010],
            ['from' => 'USD', 'to' => 'ETH', 'rate' => 0.000320],
            ['from' => 'USD', 'to' => 'USDT', 'rate' => 1.00],
            ['from' => 'BTC', 'to' => 'USD', 'rate' => 94560.20],
            ['from' => 'BTC', 'to' => 'ETH', 'rate' => 30.25],
            ['from' => 'ETH', 'to' => 'USD', 'rate' => 3120.45],
            ['from' => 'ETH', 'to' => 'BTC', 'rate' => 0.033],
            ['from' => 'USDT', 'to' => 'USD', 'rate' => 1.00],
        ];
    }

    public function getQuote(string $from, string $to, float $amount): ?array
    {
        $pairs = $this->getSupportedPairs();
        $pair = collect($pairs)->firstWhere(fn ($p) => $p['from'] === $from && $p['to'] === $to);

        if (!$pair) {
            return null;
        }

        $toAmount = $amount * $pair['rate'];
        $fee = $toAmount * (self::FEES_PERCENT / 100);

        return [
            'from_currency' => $from,
            'to_currency' => $to,
            'from_amount' => $amount,
            'to_amount' => $toAmount - $fee,
            'rate' => $pair['rate'],
            'fee' => $fee,
            'fee_percent' => self::FEES_PERCENT,
        ];
    }

    public function executeSwap(User $user, string $from, string $to, float $amount): ?SwapQuote
    {
        $quote = $this->getQuote($from, $to, $amount);

        if (!$quote) {
            return null;
        }

        return SwapQuote::create([
            'user_id' => $user->id,
            'from_currency' => $from,
            'to_currency' => $to,
            'from_amount' => $amount,
            'to_amount' => $quote['to_amount'],
            'rate' => $quote['rate'],
            'fee' => $quote['fee'],
            'status' => 'completed',
        ]);
    }

    public function getUserSwaps(User $user): Collection
    {
        return $user->swapQuotes()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (SwapQuote $q) => [
                'id' => $q->id,
                'from' => $q->from_currency,
                'to' => $q->to_currency,
                'from_amount' => number_format((float) $q->from_amount, 6),
                'to_amount' => number_format((float) $q->to_amount, 6),
                'rate' => $q->rate,
                'status' => $q->status,
                'date' => $q->created_at->format('Y-m-d H:i'),
            ]);
    }
}
