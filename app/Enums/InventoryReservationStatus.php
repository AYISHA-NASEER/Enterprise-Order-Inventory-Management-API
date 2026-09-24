<?php

namespace App\Enums;

enum InventoryReservationStatus: string
{
    case ACTIVE = 'active';
    case CONSUMED = 'consumed';
    case RELEASED = 'released';
    case EXPIRED = 'expired';
}

