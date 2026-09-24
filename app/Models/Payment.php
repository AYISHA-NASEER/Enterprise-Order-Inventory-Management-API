<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PaymentStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function markAsPaid(): void
    {
        if ($this->status !== PaymentStatus::CREATED) {
            throw new \RuntimeException(
                'Only created payments can be marked as paid.'
            );
        }

        $this->status = PaymentStatus::PAID;
        $this->save();
    }
    public function markAsFailed(): void
    {
        if ($this->status !== PaymentStatus::CREATED) {
            throw new \RuntimeException(
                'Only created payments can be marked as failed.'
            );
        }

        $this->status = PaymentStatus::FAILED;
        $this->save();
    }
}
