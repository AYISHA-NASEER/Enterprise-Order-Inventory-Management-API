<?php

namespace App\Jobs;

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
            'pending',
            'processing',
            'shipped',
        ])->get();

        foreach ($shipments as $shipment) {
            $shippingService->getStatus(
                $shipment->id
            );
        }
    }
}