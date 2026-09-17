<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Mail\DailySalesReportMail;
use Illuminate\Support\Facades\Mail;

class DailyReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $today = now()->startOfDay();

        $orders = Order::where('created_at', '>=', $today)
            ->count();

        $paidOrders = Order::where('created_at', '>=', $today)
            ->where('status', 'paid')
            ->count();

        $revenue = Payment::where('created_at', '>=', $today)
            ->where('status', 'paid')
            ->sum('amount');

            
        Mail::to('admin@test.com')->send(
            new DailySalesReportMail(
                $today->toDateString(),
                $orders,
                $paidOrders,
                (float) $revenue
            )
        );

        Log::info('Daily order report', [
            'date' => $today->toDateString(),
            'orders' => $orders,
            'paid_orders' => $paidOrders,
            'revenue' => $revenue,
        ]);
    }
}