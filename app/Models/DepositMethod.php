<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepositMethod extends Model
{
    /** @use HasFactory<DepositMethodFactory> */
    use HasFactory;

    protected $fillable = [
        'currency', 'network', 'label', 'wallet_address', 'icon',
        'min_amount', 'max_amount', 'fee_fixed', 'fee_percent', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'fee_fixed' => 'decimal:8',
            'fee_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }
}
