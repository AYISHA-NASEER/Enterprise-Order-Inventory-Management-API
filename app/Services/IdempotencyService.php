<?php

namespace App\Services;

use App\Models\IdempotencyKey;

class IdempotencyService
{
    public function find(string $key): ?IdempotencyKey
    {
        return IdempotencyKey::where('key', $key)->first();
    }

    public function create(
        string $key,
        ?int $userId = null
    ): IdempotencyKey {
        return IdempotencyKey::create([
            'key' => $key,
            'user_id' => $userId,
            'status' => 'processing',
        ]);
    }

    public function markCompleted(
        IdempotencyKey $idempotencyKey,
        int $orderId
    ): IdempotencyKey {
        $idempotencyKey->update([
            'order_id' => $orderId,
            'status' => 'completed',
        ]);

        return $idempotencyKey->fresh();
    }
}