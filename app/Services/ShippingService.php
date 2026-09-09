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

        $response = $this->shippingClient->getShipment(
            $shipment->provider_shipment_id
        );

        $shipment->update([
            'status' => $response['status'] ?? $shipment->status,
            'tracking_number' => $response['tracking_number']
                ?? $shipment->tracking_number,
            'carrier' => $response['carrier']
                ?? $shipment->carrier,
        ]);

        return $shipment->fresh();
    }
}