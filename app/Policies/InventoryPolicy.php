<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    /**
     * Admin, Manager and Warehouse can view inventory.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isWarehouse();
    }

    /**
     * Admin, Manager and Warehouse can view a specific inventory record.
     */
    public function view(User $user, Inventory $inventory): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isWarehouse();
    }

    /**
     * Only Admin can create inventory records.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin, Manager and Warehouse can update inventory.
     */
    public function update(User $user, Inventory $inventory): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isWarehouse();
    }

    /**
     * Only Admin can delete inventory.
     */
    public function delete(User $user, Inventory $inventory): bool
    {
        return $user->isAdmin();
    }
}