<?php

namespace App\Policies;


use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Can the user view the product list?
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Can the user view a product?
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Can the user create a product?
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Can the user update a product?
     */
    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    /**
     * Can the user delete a product?
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }
}