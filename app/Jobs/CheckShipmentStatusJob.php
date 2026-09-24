<?php

namespace App\Jobs;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Services\ShippingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckShipmentStatusJob implements ShouldQueue
{
    use Queueable;

    public function handle(
        ShippingService $shippingService
    ): void {
        $shipments = Shipment::whereIn('status', [
            ShipmentStatus::PENDING,
            ShipmentStatus::IN_TRANSIT,
        ])->get();

        foreach ($shipments as $shipment) {
            $shippingService->getStatus(
                $shipment->id
            );
        }
    }
}
