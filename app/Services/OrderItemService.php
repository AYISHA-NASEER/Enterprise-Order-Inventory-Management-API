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



    public function allForUser(int $userId)
    {
        return OrderItem::with([
            'order',
            'product',
        ])
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->get();
    }
}