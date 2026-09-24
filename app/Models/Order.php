<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\OrderStatus;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'status',
    ];
    protected $casts = [
        'total_amount' => 'decimal:2',
        'status' => OrderStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }
    // 👇 Add the method here
    public function markAsPaid(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new \RuntimeException(
                'Only pending orders can be marked as paid.'
            );
        }

        $this->status = OrderStatus::PAID;
        $this->save();
    }
    public function markAsPaymentFailed(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new \RuntimeException(
                'Only pending orders can be marked as payment failed.'
            );
        }

        $this->status = OrderStatus::PAYMENT_FAILED;
        $this->save();
    }

}