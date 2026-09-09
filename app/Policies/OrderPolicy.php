<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the list of orders.
     *
     * Manager and Admin can view all orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager();
    }

    /**
     * Determine whether the user can view a specific order.
     *
     * Admin/Manager:
     *     Can view any order.
     *
     * Customer:
     *     Can view only their own order.
     *
     * Warehouse:
     *     Can view orders for fulfillment.
     */
    public function view(User $user, Order $order): bool
    {
        // Admin can view any order
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can view any order
        if ($user->isManager()) {
            return true;
        }

        // Warehouse staff can view orders
        if ($user->isWarehouse()) {
            return true;
        }

        // Customer can view only their own order
        if ($user->isCustomer()) {
            return $order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create an order.
     *
     * Normal customer checkout should use CheckoutService.
     * Direct order creation is restricted to Admin/Manager.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager();
    }

    /**
     * Determine whether the user can update an order.
     *
     * Admin and Manager can update orders.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin()
            || $user->isManager();
    }

    /**
     * Determine whether the user can delete an order.
     *
     * Admin only.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore an order.
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete an order.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}