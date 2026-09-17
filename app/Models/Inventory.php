<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'low_stock_alert_sent_at',
    ];
    protected $casts = [
        'low_stock_alert_sent_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}