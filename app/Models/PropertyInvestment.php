<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyInvestment extends Model
{
    protected $fillable = [
        'user_id', 'property_project_id', 'amount', 'expected_return',
        'payout_received', 'status', 'disclosure_signed', 'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expected_return' => 'decimal:2',
            'payout_received' => 'decimal:2',
            'disclosure_signed' => 'boolean',
            'signed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(PropertyProject::class, 'property_project_id');
    }
}
