<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    /** @use HasFactory<WalletFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'currency', 'label', 'balance', 'locked_balance', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:8',
            'locked_balance' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvailableBalanceAttribute(): float
    {
        return max(0, (float) $this->balance - (float) $this->locked_balance);
    }
}
