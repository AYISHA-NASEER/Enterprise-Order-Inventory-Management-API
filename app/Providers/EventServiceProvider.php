<?php

namespace App\Providers;

use App\Events\PaymentCaptured;
use App\Listeners\CreateShipmentAfterPaymentCaptured;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


// protected $listen = [
//     PaymentCaptured::class => [
//         CreateShipmentAfterPaymentCaptured::class,
//     ],
// ];
class EventServiceProvider extends ServiceProvider
{
    //
}
