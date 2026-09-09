<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private IdempotencyService $idempotencyService,
        private OrderService $orderService,
        private OrderItemService $orderItemService,
        private InventoryService $inventoryService,
    ) {
    }

    public function checkout(
        int $userId,
        string $idempotencyKey
    ): Order {

        return DB::transaction(function () use ($userId, $idempotencyKey) {

            /*
             * 1. Find and lock the user's active cart.
             *
             * Locking prevents two checkout requests
             * from processing the same cart simultaneously.
             */
            $cart = Cart::where('user_id', $userId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (!$cart) {
                throw new RuntimeException(
                    'Active cart not found.'
                );
            }

            /*
             * 2. Check the idempotency key.
             */
            $existingKey = $this->idempotencyService->find(
                $idempotencyKey
            );

            /*
             * Same request was already completed.
             * Return the original order.
             */
            if (
                $existingKey &&
                $existingKey->status === 'completed'
            ) {
                return Order::with('items.product')
                    ->findOrFail($existingKey->order_id);
            }

            /*
             * Same request is currently processing.
             */
            if (
                $existingKey &&
                $existingKey->status === 'processing'
            ) {
                throw new RuntimeException(
                    'This checkout request is already being processed.'
                );
            }

            /*
             * 3. Load cart items after locking the cart.
             */
            $cart->load('items.product');

            if ($cart->items->isEmpty()) {
                throw new RuntimeException(
                    'Your cart is empty.'
                );
            }

            /*
             * 4. Create the idempotency record.
             */
            $idempotency = $this->idempotencyService->create(
                $idempotencyKey,
                $userId
            );

            $orderItems = [];
            $total = 0;
            $reservationIds = [];

            /*
             * 5. Reserve inventory and calculate prices.
             */
            foreach ($cart->items as $item) {

                $productId = (int) $item->product_id;
                $quantity = (int) $item->quantity;

                if ($quantity <= 0) {
                    throw new RuntimeException(
                        'Quantity must be greater than zero.'
                    );
                }

                /*
                 * InventoryService handles inventory
                 * locking, stock checking and reservation.
                 */
                $reservation = $this->inventoryService->createReservation(
                    productId: $productId,
                    userId: $userId,
                    quantity: $quantity
                );

                $product = $reservation->product;

                /*
                 * Always use the database price.
                 * Never trust a price supplied by the browser.
                 */
                $unitPrice = (float) $product->price;

                $lineTotal = $unitPrice * $quantity;

                $total += $lineTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ];

                $reservationIds[] = $reservation->id;
            }

            /*
             * 6. Create the order.
             */
            $order = $this->orderService->create([
                'user_id' => $userId,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            /*
             * 7. Attach reservations to the order.
             */
            foreach ($reservationIds as $reservationId) {

                $this->inventoryService->attachReservationToOrder(
                    $reservationId,
                    $order->id
                );
            }

            /*
             * 8. Create order items.
             */
            foreach ($orderItems as $orderItem) {

                $this->orderItemService->create([
                    'order_id' => $order->id,
                    'product_id' => $orderItem['product_id'],
                    'quantity' => $orderItem['quantity'],
                    'unit_price' => $orderItem['unit_price'],
                    'total_price' => $orderItem['total_price'],
                ]);
            }

            /*
             * 9. Mark idempotency key as completed.
             */
            $this->idempotencyService->markCompleted(
                $idempotency,
                $order->id
            );

            /*
             * 10. Clear the cart.
             *
             * This is inside the transaction.
             * If checkout fails, the cart deletion is rolled back.
             */
            $cart->items()->delete();

            /*
             * Return the order with its items.
             */
            return $order->load('items.product');
        });
    }
}