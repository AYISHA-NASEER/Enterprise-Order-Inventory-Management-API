<?php

namespace App\Services;

use App\Models\OrderItem;

class OrderItemService
{
    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    public function find(int $id): OrderItem
    {
        return OrderItem::with([
            'order',
            'product',
        ])->findOrFail($id);
    }

    public function all()
    {
        return OrderItem::with([
            'order',
            'product',
        ])->latest()->get();
    }
}