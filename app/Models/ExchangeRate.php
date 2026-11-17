<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = ['base', 'currency', 'rate', 'source', 'fetched_at'];

    protected $casts = ['rate' => 'float', 'fetched_at' => 'datetime'];
}
