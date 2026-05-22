<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CopySubscription extends Model
{
    protected $fillable = [
        'user_id', 'expert_id', 'allocation_amount',
        'current_value', 'status', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'allocation_amount' => 'decimal:2',
            'current_value' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }
}
