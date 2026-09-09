<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin     = 'admin';
    case Manager   = 'manager';
    case Warehouse = 'warehouse';
    case Customer  = 'customer';
}