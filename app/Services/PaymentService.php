<?php

namespace App\Services;

use App\Enums\InventoryReservationStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\PaymentCaptured;
use App\Integrations\Razorpay\RazorpayClient;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function __construct(
        private RazorpayClient $razorpayClient,
        private InventoryService $inventoryService
    ) {
    }

    /**
     * Create Razorpay order for a local order.
     */
    public function createPayment(int $orderId): Payment
    {
        return DB::transaction(function () use ($orderId) {

            // Lock this order so another payment request
            // cannot create a payment at the same time.
            $order = Order::whereKey($orderId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->payment()->exists()) {
                throw new RuntimeException(
                    'Payment already exists for this order.'
                );
            }

            if ($order->status !== OrderStatus::PENDING) {
                throw new RuntimeException(
                    'Payment can only be created for a pending order.'
                );
            }

            $amountInPaise = (int) round(
                $order->total_amount * 100
            );

            if ($amountInPaise <= 0) {
                throw new RuntimeException(
                    'Order amount must be greater than zero.'
                );
            }

            $razorpayOrder = $this->razorpayClient->createOrder(
                $amountInPaise,
                $order->order_number
            );

            return Payment::create([
                'order_id' => $order->id,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $order->total_amount,
                'status' => PaymentStatus::CREATED,
            ]);
        });
    }

    /**
     * Find payment.
     */
    public function find(int $paymentId): Payment
    {
        return Payment::with('order')
            ->findOrFail($paymentId);
    }

    /**
     * Verify Razorpay payment.
     */
    public function verifyPayment(
        int $paymentId,
        string $razorpayOrderId,
        string $razorpayPaymentId,
        string $razorpaySignature
    ): Payment {
        return DB::transaction(function () use ($paymentId, $razorpayOrderId, $razorpayPaymentId, $razorpaySignature) {

            $payment = Payment::with('order')
                ->lockForUpdate()
                ->findOrFail($paymentId);

            /*
             * Make sure the Razorpay order belongs
             * to this local payment.
             */
            if ($payment->razorpay_order_id !== $razorpayOrderId) {
                throw new RuntimeException(
                    'Razorpay order ID does not match.'
                );
            }

            /*
             * If already paid, do nothing.
             * This prevents duplicate processing.
             */
            if ($payment->status === PaymentStatus::PAID) {
                return $payment->fresh('order');
            }

            /*
             * Verify Razorpay signature.
             */
            $isValid = $this->razorpayClient->verifyPaymentSignature(
                $razorpayOrderId,
                $razorpayPaymentId,
                $razorpaySignature
            );

            if (!$isValid) {
                throw new RuntimeException(
                    'Invalid Razorpay payment signature.'
                );
            }

            /*
             * Mark payment as paid.
             */
            $payment->razorpay_payment_id = $razorpayPaymentId;
            $payment->save();

            $payment->markAsPaid();

            /*
             * Mark order as paid.
             */
            $order = $payment->order;

            $order->markAsPaid();

            /*
             * Consume active inventory reservations.
             */
            $reservations = InventoryReservation::where(
                'order_id',
                $order->id
            )
                ->where(
                    'status',
                    InventoryReservationStatus::ACTIVE->value
                )
                ->lockForUpdate()
                ->get();

            foreach ($reservations as $reservation) {
                $this->inventoryService->consumeReservation(
                    $reservation->id
                );
            }

            /*
             * Payment is successfully captured.
             *
             * PaymentCaptured implements
             * ShouldDispatchAfterCommit, so the event
             * will only be handled after this transaction
             * successfully commits.
             */
            PaymentCaptured::dispatch(
                $payment->fresh('order')
            );

            return $payment->fresh('order');
        });
    }

    /**
     * Update payment status.
     */
    public function updateStatus(
        int $paymentId,
        string $status
    ): Payment {
        $payment = Payment::findOrFail($paymentId);

        $payment->update([
            'status' => $status,
        ]);

        return $payment->fresh('order');
    }
}