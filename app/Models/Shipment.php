<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'provider_shipment_id',
        'status',
        'tracking_number',
        'carrier',
    ];

    protected $casts = [
        'status' => ShipmentStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function markAsShipped(): void
    {
        if ($this->status !== ShipmentStatus::IN_TRANSIT) {
            throw new \RuntimeException(
                'Only shipments in transit can be marked as shipped.'
            );
        }

        $this->status = ShipmentStatus::SHIPPED;
        $this->save();
    }
}
