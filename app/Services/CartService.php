<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use RuntimeException;

class CartService
{
    /**
     * Get the user's active cart.
     */
    public function getCart(int $userId): Cart
    {
        return Cart::with('items.product')
            ->firstOrCreate(
                [
                    'user_id' => $userId,
                    'status' => 'active',
                ]
            );
    }

    /**
     * Add a product to the cart.
     */
    public function addItem(
        int $userId,
        int $productId,
        int $quantity
    ): CartItem {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Quantity must be greater than zero.'
            );
        }

        $product = Product::findOrFail($productId);

        if ($product->status !== 'active') {
            throw new RuntimeException('Product is not active.');
        }

        $cart = $this->getCart($userId);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $item->increment('quantity', $quantity);

            return $item->fresh('product');
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(
        int $userId,
        int $cartItemId,
        int $quantity
    ): CartItem {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Quantity must be greater than zero.'
            );
        }

        $cart = $this->getCart($userId);

        $item = CartItem::where('id', $cartItemId)
            ->where('cart_id', $cart->id)
            ->firstOrFail();

        $item->update([
            'quantity' => $quantity,
        ]);

        return $item->fresh('product');
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(
        int $userId,
        int $cartItemId
    ): void {
        $cart = $this->getCart($userId);

        $item = CartItem::where('id', $cartItemId)
            ->where('cart_id', $cart->id)
            ->firstOrFail();

        $item->delete();
    }

    /**
     * Empty the cart.
     */
    public function clear(int $userId): void
    {
        $cart = $this->getCart($userId);

        $cart->items()->delete();
    }
}