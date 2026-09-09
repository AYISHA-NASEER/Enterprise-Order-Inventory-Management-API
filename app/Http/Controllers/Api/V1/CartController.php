<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {
    }

    /**
     * Get current user's cart.
     */
    public function show(Request $request)
    {
        $cart = $this->cartService->getCart(
            $request->user()->id
        );

        return response()->json([
            'message' => 'Cart fetched successfully.',
            'data' => $cart,
        ]);
    }

    /**
     * Add product to cart.
     */
    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item = $this->cartService->addItem(
            $request->user()->id,
            $validated['product_id'],
            $validated['quantity']
        );

        return response()->json([
            'message' => 'Product added to cart.',
            'data' => $item,
        ], 201);
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(
        Request $request,
        int $cartItemId
    ) {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item = $this->cartService->updateItem(
            $request->user()->id,
            $cartItemId,
            $validated['quantity']
        );

        return response()->json([
            'message' => 'Cart item updated.',
            'data' => $item,
        ]);
    }

    /**
     * Remove cart item.
     */
    public function removeItem(
        Request $request,
        int $cartItemId
    ) {
        $this->cartService->removeItem(
            $request->user()->id,
            $cartItemId
        );

        return response()->json([
            'message' => 'Cart item removed.',
        ]);
    }

    /**
     * Empty cart.
     */
    public function clear(Request $request)
    {
        $this->cartService->clear(
            $request->user()->id
        );

        return response()->json([
            'message' => 'Cart cleared successfully.',
        ]);
    }
}