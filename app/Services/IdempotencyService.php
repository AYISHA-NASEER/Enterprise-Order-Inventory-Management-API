<?php

namespace App\Services;

use App\Models\IdempotencyKey;

class IdempotencyService
{
    public function find(
        string $key,
        int $userId
    ): ?IdempotencyKey {
        return IdempotencyKey::where('key', $key)
            ->where('user_id', $userId)
            ->first();
    }

    public function create(
        string $key,
        int $userId,
        ?string $requestFingerprint = null
    ): IdempotencyKey {
        return IdempotencyKey::create([
            'key' => $key,
            'user_id' => $userId,
            'request_fingerprint' => $requestFingerprint,
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