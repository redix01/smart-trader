<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watchlist extends Model
{
    protected $fillable = ['user_id', 'market_pair_id', 'label'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marketPair(): BelongsTo
    {
        return $this->belongsTo(MarketPair::class);
    }
}
