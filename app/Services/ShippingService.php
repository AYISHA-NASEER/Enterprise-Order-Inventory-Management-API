<?php

namespace App\Services;

use App\Integrations\Shipping\ShippingClient;
use App\Models\Shipment;
use RuntimeException;

class ShippingService
{
    public function __construct(
        private ShippingClient $shippingClient
    ) {
    }

    public function getStatus(int $shipmentId): Shipment
    {
        $shipment = Shipment::findOrFail($shipmentId);

        if (!$shipment->provider_shipment_id) {
            throw new RuntimeException(
                'Shipment does not have a provider shipment ID yet.'
            );
        }

        // Ask the external shipping API for the latest status
        $response = $this->shippingClient->getShipment(
            $shipment->provider_shipment_id
        );

        // Convert external shipping statuses
        // to our application's statuses.
        $status = match ($response['status'] ?? null) {
            'pending' => 'processing',
            'in_transit' => 'shipped',
            'delivered' => 'delivered',
            default => $shipment->status,
        };

        // Update our local shipment record
        $shipment->update([
            'status' => $status,

            'tracking_number' => $response['tracking_number']
                ?? $shipment->tracking_number,

            'carrier' => $response['carrier']
                ?? $shipment->carrier,
        ]);

        return $shipment->fresh();
    }
}