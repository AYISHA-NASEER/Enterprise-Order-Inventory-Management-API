<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryReservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    /**
     * Create an inventory record.
     */
    public function create(array $data): Inventory
    {
        return Inventory::create($data);
    }

    /**
     * Get inventory for a product.
     */
    public function findByProduct(int $productId): Inventory
    {
        return Inventory::with('product')
            ->where('product_id', $productId)
            ->firstOrFail();
    }

    /**
     * Lock an inventory row.
     *
     * This is useful when another service needs to perform
     * multiple inventory operations inside an existing transaction.
     */
    public function lockForCheckout(int $productId): Inventory
    {
        return Inventory::with('product')
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * Set the exact inventory quantity.
     *
     * Example:
     *
     * Current stock = 10
     * updateQuantity(1, 20)
     *
     * New stock = 20
     */
    public function updateQuantity(
        int $productId,
        int $quantity
    ): Inventory {
        if ($quantity < 0) {
            throw new RuntimeException(
                'Inventory quantity cannot be negative.'
            );
        }

        return DB::transaction(function () use ($productId, $quantity) {
            $inventory = Inventory::where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            $inventory->update([
                'quantity' => $quantity,
            ]);

            return $inventory->fresh();
        });
    }

    /**
     * Add stock.
     *
     * Used when warehouse receives new stock.
     */
    public function increaseStock(
        int $productId,
        int $quantity
    ): Inventory {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Quantity must be greater than zero.'
            );
        }

        return DB::transaction(function () use ($productId, $quantity) {
            $inventory = Inventory::where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            $inventory->increment(
                'quantity',
                $quantity
            );

            return $inventory->fresh();
        });
    }

    /**
     * Permanently remove available stock.
     *
     * This is NOT used for checkout reservations.
     *
     * A reservation already reduces available stock.
     */
    public function decreaseStock(
        int $productId,
        int $quantity
    ): Inventory {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Quantity must be greater than zero.'
            );
        }

        return DB::transaction(function () use ($productId, $quantity) {
            $inventory = Inventory::where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($inventory->quantity < $quantity) {
                throw new RuntimeException(
                    'Insufficient stock.'
                );
            }

            $inventory->decrement(
                'quantity',
                $quantity
            );

            return $inventory->fresh();
        });
    }

    /**
     * Reserve inventory for a customer.
     *
     * This operation:
     *
     * 1. Locks inventory row
     * 2. Checks product status
     * 3. Checks available stock
     * 4. Reduces available stock
     * 5. Creates reservation
     *
     * Reservation expires after 15 minutes.
     */
    public function createReservation(
        int $productId,
        int $userId,
        int $quantity,
        ?int $orderId = null
    ): InventoryReservation {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Reservation quantity must be greater than zero.'
            );
        }

        return DB::transaction(function () use ($productId, $userId, $quantity, $orderId) {
            /*
             * Lock the inventory row.
             *
             * This is important for concurrency.
             */
            $inventory = Inventory::with('product')
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Product must be active.
             */
            if ($inventory->product->status !== 'active') {
                throw new RuntimeException(
                    'Product is not active.'
                );
            }

            /*
             * Check available stock.
             */
            if ($inventory->quantity < $quantity) {
                throw new RuntimeException(
                    'Insufficient stock.'
                );
            }

            /*
             * Remove the reserved quantity
             * from available inventory.
             */
            $inventory->decrement(
                'quantity',
                $quantity
            );

            /*
             * Create reservation.
             */
            return InventoryReservation::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'order_id' => $orderId,
                'quantity' => $quantity,
                'status' => 'active',
                'expires_at' => Carbon::now()->addMinutes(15),
            ]);
        });
    }

    /**
     * Attach an existing reservation to an order.
     */
    public function attachReservationToOrder(
        int $reservationId,
        int $orderId
    ): InventoryReservation {
        return DB::transaction(function () use ($reservationId, $orderId) {
            $reservation = InventoryReservation::where(
                'id',
                $reservationId
            )
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Only active reservations can be attached.
             */
            if ($reservation->status !== 'active') {
                throw new RuntimeException(
                    'Only active reservations can be attached to an order.'
                );
            }

            /*
             * Don't allow a reservation to be moved
             * to another order.
             */
            if (
                $reservation->order_id !== null &&
                $reservation->order_id !== $orderId
            ) {
                throw new RuntimeException(
                    'Reservation is already attached to another order.'
                );
            }

            $reservation->update([
                'order_id' => $orderId,
            ]);

            return $reservation->fresh();
        });
    }

    /**
     * Consume a reservation after successful purchase/payment.
     *
     * IMPORTANT:
     *
     * Stock is NOT returned here.
     *
     * The stock was already removed when the reservation
     * was created.
     */
    public function consumeReservation(
        int $reservationId
    ): InventoryReservation {
        return DB::transaction(function () use ($reservationId) {

            $reservation = InventoryReservation::where(
                'id',
                $reservationId
            )
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Idempotent:
             *
             * If the same payment/webhook is processed twice,
             * don't do anything again.
             */
            if ($reservation->status === 'consumed') {
                return $reservation;
            }

            /*
             * Only active reservations can be consumed.
             */
            if ($reservation->status !== 'active') {
                throw new RuntimeException(
                    'Reservation is not active.'
                );
            }

            /*
             * Reservation must not have expired.
             */
            if ($reservation->expires_at->isPast()) {
                throw new RuntimeException(
                    'Reservation has expired.'
                );
            }

            /*
             * Mark reservation as consumed.
             *
             * DO NOT increase inventory.
             */
            $reservation->update([
                'status' => 'consumed',
            ]);

            return $reservation->fresh();
        });
    }

    /**
     * Release a reservation.
     *
     * Used for:
     *
     * - customer cancellation
     * - payment failure
     * - reservation expiry
     *
     * The reserved stock is returned to inventory.
     */
    public function releaseReservation(
        int $reservationId,
        string $status = 'released'
    ): InventoryReservation {
        $allowedStatuses = [
            'released',
            'expired',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new RuntimeException(
                'Invalid reservation release status.'
            );
        }

        return DB::transaction(function () use ($reservationId, $status) {
            /*
             * Lock reservation first.
             *
             * This prevents two workers from releasing
             * the same reservation at the same time.
             */
            $reservation = InventoryReservation::where(
                'id',
                $reservationId
            )
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * If reservation is already:
             *
             * released
             * expired
             * consumed
             *
             * don't return stock again.
             */
            if ($reservation->status !== 'active') {
                return $reservation;
            }

            /*
             * Lock inventory row.
             */
            $inventory = Inventory::where(
                'product_id',
                $reservation->product_id
            )
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Return reserved stock.
             */
            $inventory->increment(
                'quantity',
                $reservation->quantity
            );

            /*
             * Update reservation status.
             */
            $reservation->update([
                'status' => $status,
            ]);

            return $reservation->fresh();
        });
    }
}