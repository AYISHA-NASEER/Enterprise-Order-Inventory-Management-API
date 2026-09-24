<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case PENDING = 'pending';
    case IN_TRANSIT = 'in_transit';
    case SHIPPED = 'shipped';
}