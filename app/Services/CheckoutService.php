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
             * 1. Create a fingerprint for this checkout request.
             *
             * The checkout endpoint currently does not receive
             * product or quantity data in the request body.
             *
             * The actual products come from the user's active cart.
             *
             * Therefore, the fingerprint represents the endpoint
             * and request payload.
             */
            $fingerprintData = [
                'endpoint' => 'POST /api/v1/checkout',
                'payload' => [],
            ];

            $requestFingerprint = hash(
                'sha256',
                json_encode($fingerprintData)
            );

            /*
             * 2. Check the idempotency key for this user.
             */
            $existingKey = $this->idempotencyService->find(
                $idempotencyKey,
                $userId
            );

            /*
             * Same user + same key + same request
             * that already completed.
             *
             * Return the original order.
             */
            if (
                $existingKey &&
                $existingKey->request_fingerprint === $requestFingerprint &&
                $existingKey->status === 'completed'
            ) {
                return Order::with('items.product')
                    ->findOrFail($existingKey->order_id);
            }

            /*
             * Same user + same key,
             * but different request.
             */
            if (
                $existingKey &&
                $existingKey->request_fingerprint !== $requestFingerprint
            ) {
                throw new RuntimeException(
                    'This idempotency key was already used for a different checkout request.'
                );
            }

            /*
             * Same request is currently being processed.
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
             * 3. Find and lock the user's active cart.
             *
             * Locking prevents two checkout requests from
             * processing the same cart simultaneously.
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
             * 4. Load cart items.
             */
            $cart->load('items.product');

            if ($cart->items->isEmpty()) {
                throw new RuntimeException(
                    'Your cart is empty.'
                );
            }

            /*
             * 5. Create the idempotency record.
             */
            $idempotency = $this->idempotencyService->create(
                key: $idempotencyKey,
                userId: $userId,
                requestFingerprint: $requestFingerprint
            );

            $orderItems = [];
            $total = 0;
            $reservationIds = [];

            /*
             * 6. Reserve inventory and calculate prices.
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
                 * InventoryService handles:
                 * - inventory locking
                 * - stock checking
                 * - reservation creation
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
             * 7. Create the order.
             */
            $order = $this->orderService->create([
                'user_id' => $userId,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            /*
             * 8. Attach reservations to the order.
             */
            foreach ($reservationIds as $reservationId) {

                $this->inventoryService->attachReservationToOrder(
                    $reservationId,
                    $order->id
                );
            }

            /*
             * 9. Create order items.
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
             * 10. Mark idempotency key as completed.
             */
            $this->idempotencyService->markCompleted(
                $idempotency,
                $order->id
            );

            /*
             * 11. Clear the cart.
             *
             * This happens inside the transaction.
             * If checkout fails, the transaction rolls back.
             */
            $cart->items()->delete();

            /*
             * 12. Return the order with its items.
             */
            return $order->load('items.product');
        });
    }
}

