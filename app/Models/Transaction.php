<?php

namespace App\Models;

use App\Enums\PaymentProvider;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'provider',
        'transaction_id',
        'order_id',
        'amount',
        'status',
        'requested_payload',
        'response_payload',
    ];

    protected $casts = [
        'provider'          => PaymentProvider::class,
        'status'            => TransactionStatus::class,
        'requested_payload' => 'array',
        'response_payload'  => 'array',
    ];
}
