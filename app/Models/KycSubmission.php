<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycSubmission extends Model
{
    /** @use HasFactory<KycSubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'id_document_path', 'id_document_type',
        'selfie_path', 'address_proof_path', 'rejection_reason',
        'reviewed_at', 'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
