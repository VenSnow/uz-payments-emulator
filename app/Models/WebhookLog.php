<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'provider',
        'url',
        'payload',
        'is_debug',
        'response_status',
        'response_body',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_debug' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
