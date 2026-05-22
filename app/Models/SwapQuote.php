<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwapQuote extends Model
{
    protected $fillable = [
        'user_id', 'from_currency', 'to_currency',
        'from_amount', 'to_amount', 'rate', 'fee', 'status',
    ];

    protected function casts(): array
    {
        return [
            'from_amount' => 'decimal:8',
            'to_amount' => 'decimal:8',
            'rate' => 'decimal:8',
            'fee' => 'decimal:8',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
