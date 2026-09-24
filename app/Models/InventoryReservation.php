<?php

namespace App\Models;

use App\Enums\InventoryReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryReservation extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'quantity',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'status' => InventoryReservationStatus::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function consume(): void
    {
        if ($this->status !== InventoryReservationStatus::ACTIVE) {
            throw new \RuntimeException(
                'Only active reservations can be consumed.'
            );
        }

        $this->status = InventoryReservationStatus::CONSUMED;
        $this->save();
    }
    public function release(): void
    {
        if ($this->status !== InventoryReservationStatus::ACTIVE) {
            throw new \RuntimeException(
                'Only active reservations can be released.'
            );
        }

        $this->status = InventoryReservationStatus::RELEASED;
        $this->save();
    }
    public function expire(): void
    {
        if ($this->status !== InventoryReservationStatus::ACTIVE) {
            throw new \RuntimeException(
                'Only active reservations can expire.'
            );
        }

        $this->status = InventoryReservationStatus::EXPIRED;
        $this->save();
    }
}
