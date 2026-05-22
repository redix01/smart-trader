<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type'];

    protected $casts = [
        'value' => 'string',
    ];
}
