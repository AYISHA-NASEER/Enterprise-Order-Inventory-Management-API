<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case INITIATED = 'initiated';
    case CREATED = 'created';
    case PAID = 'paid';
    case FAILED = 'failed';
}