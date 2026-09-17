<?php

namespace App\Jobs;

use App\Enums\UserRole;
use App\Models\Inventory;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckLowStockJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {

        $lowStockLimit = 10;


        $inventories = Inventory::with('product')->get();

        foreach ($inventories as $inventory) {


            if ($inventory->quantity <= $lowStockLimit) {


                if ($inventory->low_stock_alert_sent_at !== null) {
                    continue;
                }


                $users = User::whereIn('role', [
                    UserRole::Admin,
                    UserRole::Manager,
                ])->get();


                foreach ($users as $user) {
                    $user->notify(
                        new LowStockNotification($inventory)
                    );
                }


                $inventory->update([
                    'low_stock_alert_sent_at' => now(),
                ]);
            }

            // Product is no longer low in stock
            else {


                $inventory->update([
                    'low_stock_alert_sent_at' => null,
                ]);
            }
        }
    }
}