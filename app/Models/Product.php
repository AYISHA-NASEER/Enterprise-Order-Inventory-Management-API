<?php

namespace App\Models;
use App\Models\Category;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'price',
        'description',
        'status',
        'source',
        'external_id',
        'external_data',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'external_data' => 'array',
        ];
    }




    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
