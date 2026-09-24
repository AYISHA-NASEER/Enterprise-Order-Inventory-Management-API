<?php

namespace App\Jobs;

use App\Enums\ShipmentStatus;
use App\Integrations\Shipping\ShippingClient;
use App\Models\Order;
use App\Models\Shipment;
use App\Notifications\ShipmentCreatedNotification;
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
        // Get the order and its customer
        $order = Order::with('user')->findOrFail($this->orderId);

        // Create local shipment record
        $shipment = Shipment::firstOrCreate(
            [
                'order_id' => $this->orderId,
            ],
            [
                'status' => ShipmentStatus::PENDING,
            ]
        );

        // Do not create another shipment if provider ID already exists.
        if ($shipment->provider_shipment_id) {
            return;
        }

        // Call external shipping API
        $response = $shippingClient->createShipment([
            'order_id' => $order->id,
        ]);

        // Convert external shipping status to our local enum.
        $status = match ($response['status'] ?? null) {
            'pending' => ShipmentStatus::PENDING,
            'in_transit' => ShipmentStatus::IN_TRANSIT,
            'shipped' => ShipmentStatus::SHIPPED,
            default => ShipmentStatus::PENDING,
        };

        // Save shipping details
        $shipment->update([
            'provider_shipment_id' => $response['shipment_id'] ?? null,
            'status' => $status,
            'tracking_number' => $response['tracking_number'] ?? null,
            'carrier' => $response['carrier'] ?? null,
        ]);

        // Send notification to the customer
        if ($order->user) {
            $order->user->notify(
                new ShipmentCreatedNotification(
                    $shipment->fresh()
                )
            );
        }
    }}
