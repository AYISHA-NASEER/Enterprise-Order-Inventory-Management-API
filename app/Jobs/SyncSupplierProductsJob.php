<?php

namespace App\Jobs;

use App\Services\SupplierProductSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncSupplierProductsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Maximum number of attempts.
     */
    public int $tries = 3;

    /**
     * Wait 10 seconds before retrying.
     */
    public int $backoff = 10;

    /**
     * Execute the job.
     */
    public function handle(
        SupplierProductSyncService $syncService
    ): void {
        $syncService->sync();
    }
}