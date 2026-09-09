<?php

namespace App\Listeners;

use App\Events\PaymentCaptured;
use App\Jobs\CreateShipmentJob;
use App\Jobs\SendPaymentCapturedNotificationJob;

class CreateShipmentAfterPaymentCaptured
{
    public function handle(PaymentCaptured $event): void
    {
        CreateShipmentJob::dispatch(
            $event->payment->order_id
        );

        SendPaymentCapturedNotificationJob::dispatch(
            $event->payment->id
        );
    }
}