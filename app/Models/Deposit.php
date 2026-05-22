<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    /** @use HasFactory<DepositFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'deposit_method_id', 'amount', 'fee', 'net_amount',
        'currency', 'status', 'proof_path', 'proof_mime',
        'admin_notes', 'approved_at', 'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:8',
            'net_amount' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function depositMethod(): BelongsTo
    {
        return $this->belongsTo(DepositMethod::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
