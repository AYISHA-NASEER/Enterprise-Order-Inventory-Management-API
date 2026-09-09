<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Notifications\PaymentCapturedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPaymentCapturedNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $paymentId
    ) {
    }

    public function handle(): void
    {
        $payment = Payment::with('order.user')->find($this->paymentId);

        if (!$payment) {
            return;
        }

        $user = $payment->order?->user;

        if ($user) {
            $user->notify(
                new PaymentCapturedNotification($payment)
            );
        }
    }
}