<?php

namespace App\Jobs;

use App\Models\InventoryReservation;
use App\Services\InventoryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireInventoryReservationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(InventoryService $inventoryService): void
    {
        InventoryReservation::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->chunkById(100, function ($reservations) use ($inventoryService) {

                foreach ($reservations as $reservation) {

                    $inventoryService->releaseReservation(
                        $reservation->id,
                        'expired'
                    );
                }
            });
    }
}