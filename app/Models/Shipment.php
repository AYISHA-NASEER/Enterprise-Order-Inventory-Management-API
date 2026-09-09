<?php

namespace App\Models;
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
