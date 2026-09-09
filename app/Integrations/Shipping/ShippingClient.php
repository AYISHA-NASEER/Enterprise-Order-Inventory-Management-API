<?php

namespace App\Integrations\Shipping;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ShippingClient
{
    public function createShipment(array $data): array
    {
        $response = Http::timeout(10)
            ->retry(3, 200)
            ->post(
                config('services.shipping.base_url') . '/shipments',
                $data
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Shipping API failed: ' . $response->status()
            );
        }

        return $response->json();
    }

    public function getShipment(string $shipmentId): array
    {
        $response = Http::timeout(10)
            ->retry(3, 200)
            ->get(
                config('services.shipping.base_url') .
                '/shipments/' . $shipmentId
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Shipping API failed: ' . $response->status()
            );
        }

        return $response->json();
    }
}