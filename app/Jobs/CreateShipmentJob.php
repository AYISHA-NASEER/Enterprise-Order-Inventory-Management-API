<?php

namespace App\Jobs;

use App\Integrations\Shipping\ShippingClient;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateShipmentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $orderId
    ) {
    }

    public function handle(
        ShippingClient $shippingClient
    ): void {
        $order = Order::findOrFail($this->orderId);

        $shipment = Shipment::firstOrCreate(
            [
                'order_id' => $this->orderId,
            ],
            [
                'status' => 'pending',
            ]
        );

        // Do not create another shipment if provider ID already exists.
        if ($shipment->provider_shipment_id) {
            return;
        }

        $response = $shippingClient->createShipment([
            'order_id' => $order->id,
        ]);

        $shipment->update([
            'provider_shipment_id' => $response['shipment_id'] ?? null,
            'status' => $response['status'] ?? 'pending',
            'tracking_number' => $response['tracking_number'] ?? null,
            'carrier' => $response['carrier'] ?? null,
        ]);
    }
}