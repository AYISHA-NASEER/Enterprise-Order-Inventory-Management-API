<?php

namespace App\Services;

use App\Integrations\Razorpay\RazorpayClient;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use App\Models\InventoryReservation;
use App\Events\PaymentCaptured;


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
        $order = Order::findOrFail($orderId);

        if ($order->payment()->exists()) {
            throw new RuntimeException(
                'Payment already exists for this order.'
            );
        }

        if ($order->status !== 'pending') {
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
            'status' => 'created',
        ]);
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
            if ($payment->status === 'paid') {
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
            $payment->update([
                'razorpay_payment_id' => $razorpayPaymentId,
                'status' => 'paid',
            ]);

            /*
             * Mark order as paid.
             */
            $order = $payment->order;

            $order->update([
                'status' => 'paid',
            ]);

            /*
             * Consume active inventory reservations.
             */
            $reservations = InventoryReservation::where(
                'order_id',
                $order->id
            )
                ->where('status', 'active')
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
     * Process Razorpay payment.captured webhook.
     */
    public function processWebhook(
        string $eventId,
        string $event,
        array $payload
    ): void {
        DB::transaction(function () use ($eventId, $event, $payload) {

            /*
             * Prevent duplicate webhook processing.
             */
            $inserted = DB::table('webhook_events')->insertOrIgnore([
                'event_id' => $eventId,
                'event' => $event,
                'payload' => json_encode($payload),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
             * If event already exists,
             * Razorpay sent the same webhook again.
             */
            if ($inserted === 0) {
                return;
            }

            /*
             * Get payment information from Razorpay payload.
             */
            $razorpayPayment = $payload['payload']['payment']['entity'] ?? null;

            if (!$razorpayPayment) {
                throw new RuntimeException(
                    'Payment information missing from webhook.'
                );
            }

            $razorpayPaymentId = $razorpayPayment['id'] ?? null;
            $razorpayOrderId = $razorpayPayment['order_id'] ?? null;
            $razorpayAmount = $razorpayPayment['amount'] ?? null;

            if (!$razorpayPaymentId || !$razorpayOrderId || !$razorpayAmount) {
                throw new RuntimeException(
                    'Invalid Razorpay payment payload.'
                );
            }

            /*
             * Find and lock our local payment.
             */
            $payment = Payment::where(
                'razorpay_order_id',
                $razorpayOrderId
            )
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                throw new RuntimeException(
                    'Local payment not found.'
                );
            }

            /*
             * Make sure Razorpay amount matches
             * our local order amount.
             */
            $expectedAmount = (int) round(
                $payment->amount * 100
            );

            if ((int) $razorpayAmount !== $expectedAmount) {
                throw new RuntimeException(
                    'Razorpay payment amount does not match local payment.'
                );
            }

            /*
             * If payment is already paid,
             * don't process it again.
             */
            if ($payment->status === 'paid') {
                return;
            }

            /*
             * Mark payment as paid.
             */
            $payment->update([
                'razorpay_payment_id' => $razorpayPaymentId,
                'status' => 'paid',
            ]);

            /*
             * Mark order as paid.
             */
            $order = Order::where('id', $payment->order_id)
                ->lockForUpdate()
                ->firstOrFail();

            $order->update([
                'status' => 'paid',
            ]);

            /*
             * Consume active reservations.
             */
            $reservations = InventoryReservation::where(
                'order_id',
                $order->id
            )
                ->where('status', 'active')
                ->lockForUpdate()
                ->get();

            foreach ($reservations as $reservation) {
                $this->inventoryService->consumeReservation(
                    $reservation->id
                );
            }
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