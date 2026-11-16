<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'asset_type',
        'symbol',
        'order_type',
        'side',
        'quantity',
        'price',
        'amount',
        'leverage',
        'status',
        'filled_at',
        'entry_price',
        'exit_price',
        'take_profit',
        'stop_loss',
        'realized_pnl',
        'close_reason',
        'closed_at',
    ];

    public const STATUS_PENDING = 'pending';       // limit order waiting for its price
    public const STATUS_OPEN = 'open';             // filled: a live position
    public const STATUS_CLOSED = 'closed';
    public const STATUS_LIQUIDATED = 'liquidated';
    public const STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'quantity' => 'decimal:8',
        'price' => 'decimal:8',
        'amount' => 'decimal:8',
        'leverage' => 'decimal:2',
        'filled_at' => 'datetime',
        'entry_price' => 'decimal:8',
        'exit_price' => 'decimal:8',
        'take_profit' => 'decimal:8',
        'stop_loss' => 'decimal:8',
        'realized_pnl' => 'decimal:8',
        'closed_at' => 'datetime',
    ];

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /** Profit or loss at $price, in the user's currency (same unit as amount). */
    public function pnlAt(float $price): float
    {
        if (! $this->entry_price || ! $this->quantity) {
            return 0.0;
        }
        $direction = $this->side === 'sell' ? -1 : 1;

        return $direction * ($price - (float) $this->entry_price) * (float) $this->quantity;
    }

    /** Price at which losses equal the margin (position is liquidated). */
    public function liquidationPrice(): ?float
    {
        if (! $this->entry_price || ! $this->quantity) {
            return null;
        }
        $move = (float) $this->amount / (float) $this->quantity;

        return $this->side === 'sell' ? (float) $this->entry_price + $move : max(0, (float) $this->entry_price - $move);
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper Methods
    public function isLimitOrder(): bool
    {
        return $this->order_type === 'limit';
    }

    public function isMarketOrder(): bool
    {
        return $this->order_type === 'market';
    }

    public function isBuyOrder(): bool
    {
        return $this->side === 'buy';
    }

    public function isSellOrder(): bool
    {
        return $this->side === 'sell';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFilled(): bool
    {
        return in_array($this->status, ['open', 'closed', 'liquidated', 'filled'], true);
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'open', 'filled' => 'bg-blue-100 text-blue-800',
            'closed' => 'bg-green-100 text-green-800',
            'liquidated' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getSideBadgeAttribute(): string
    {
        return $this->isBuyOrder() 
            ? 'bg-green-100 text-green-800' 
            : 'bg-red-100 text-red-800';
    }

    public function getOrderTypeBadgeAttribute(): string
    {
        return $this->isLimitOrder() 
            ? 'bg-blue-100 text-blue-800' 
            : 'bg-purple-100 text-purple-800';
    }
}
