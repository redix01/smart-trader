<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'sender_email',
        'sender_name',
        'recipient_source',
        'recipients',
        'recipient_count',
        'subject',
        'message',
        'header_color',
        'accent_label',
        'footer_note',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'recipients' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
