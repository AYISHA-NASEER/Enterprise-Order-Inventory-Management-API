<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    protected $fillable = [
        'event_id',
        'event_type',
        'razorpay_payment_id',
        'razorpay_order_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
