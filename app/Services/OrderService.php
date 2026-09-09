<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function create(array $data): Order
{
    $data['order_number'] ??= 'ORD-' . strtoupper(\Illuminate\Support\Str::random(10));

    return Order::create($data);
}

    public function find(int $id): Order
    {
        return Order::with([
            'user',
            'items.product',
        ])->findOrFail($id);
    }

    public function all()
    {
        return Order::with([
            'user',
            'items.product',
        ])->latest()->get();
    }
}

