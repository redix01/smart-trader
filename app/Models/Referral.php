<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_user_id',
        'amount',
        'status',
        'description',
        'rewarded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'rewarded_at' => 'datetime',
        ];
    }

    /**
     * The user who referred someone.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * The user who was referred.
     */
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
