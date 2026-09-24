<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Events\PaymentCaptured;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use App\Enums\InventoryReservationStatus;

class PaymentWebhookService
{
    public function __construct(
        private InventoryService $inventoryService
    ) {
    }

    public function process(array $data, string $eventId): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate webhook event ID
        |--------------------------------------------------------------------------
        */

        if ($eventId === '') {
            throw new RuntimeException(
                'Webhook event ID is missing.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Validate event type
        |--------------------------------------------------------------------------
        */

        $eventType = $data['event'] ?? null;

        if (!$eventType) {
            throw new RuntimeException(
                'Webhook event type is missing.'
            );
        }

        if (
            !in_array(
                $eventType,
                [
                    'payment.captured',
                    'payment.failed',
                ],
                true
            )
        ) {
            throw new RuntimeException(
                'Unsupported Razorpay webhook event.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Extract Razorpay payment information
        |--------------------------------------------------------------------------
        */

        $paymentEntity = $data['payload']['payment']['entity'] ?? [];

        $razorpayPaymentId = $paymentEntity['id'] ?? null;
        $razorpayOrderId = $paymentEntity['order_id'] ?? null;
        $razorpayAmount = $paymentEntity['amount'] ?? null;
        $razorpayCurrency = $paymentEntity['currency'] ?? null;
        $razorpayStatus = $paymentEntity['status'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | 4. Validate required Razorpay payment fields
        |--------------------------------------------------------------------------
        */

        if (
            !$razorpayPaymentId ||
            !$razorpayOrderId ||
            $razorpayAmount === null ||
            !$razorpayCurrency ||
            !$razorpayStatus
        ) {
            throw new RuntimeException(
                'Invalid Razorpay payment payload.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Validate currency
        |--------------------------------------------------------------------------
        */

        if ($razorpayCurrency !== 'INR') {
            throw new RuntimeException(
                'Invalid Razorpay payment currency.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Validate Razorpay payment status
        |--------------------------------------------------------------------------
        */

        if (
            $eventType === 'payment.captured' &&
            $razorpayStatus !== 'captured'
        ) {
            throw new RuntimeException(
                'Razorpay payment is not captured.'
            );
        }

        if (
            $eventType === 'payment.failed' &&
            $razorpayStatus !== 'failed'
        ) {
            throw new RuntimeException(
                'Razorpay payment is not failed.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Process webhook inside database transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($data, $eventId, $eventType, $razorpayPaymentId, $razorpayOrderId, $razorpayAmount) {
            /*
            |--------------------------------------------------------------------------
            | 8. Ignore duplicate webhook
            |--------------------------------------------------------------------------
            */

            if (
                PaymentWebhookEvent::where(
                    'event_id',
                    $eventId
                )->exists()
            ) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | 9. Find and lock local payment
            |--------------------------------------------------------------------------
            */

            $payment = Payment::where(
                'razorpay_order_id',
                $razorpayOrderId
            )
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                throw new RuntimeException(
                    'Local payment was not found.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 10. Lock related order
            |--------------------------------------------------------------------------
            */

            $order = Order::lockForUpdate()
                ->findOrFail($payment->order_id);

            /*
            |--------------------------------------------------------------------------
            | 11. Verify Razorpay amount
            |--------------------------------------------------------------------------
            */

            $expectedAmountInPaise = (int) round(
                $payment->amount * 100
            );

            if (
                (int) $razorpayAmount !== $expectedAmountInPaise
            ) {
                throw new RuntimeException(
                    'Razorpay payment amount does not match the order amount.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 12. Verify Razorpay payment ID consistency
            |--------------------------------------------------------------------------
            */

            if (
                $payment->razorpay_payment_id !== null &&
                $payment->razorpay_payment_id !== $razorpayPaymentId
            ) {
                throw new RuntimeException(
                    'Razorpay payment ID does not match the existing payment.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 13. Record webhook event
            |--------------------------------------------------------------------------
            */

            PaymentWebhookEvent::create([
                'event_id' => $eventId,
                'event_type' => $eventType,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_order_id' => $razorpayOrderId,
                'payload' => $data,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 14. Payment captured
            |--------------------------------------------------------------------------
            */

            if ($eventType === 'payment.captured') {

                /*
                | Already paid.
                |
                | Do not consume inventory again.
                */

                if ($payment->status === PaymentStatus::PAID) {
                    return;
                }

                /*
                | Only a created payment can become paid.
                */

                if ($payment->status !== PaymentStatus::CREATED) {
                    throw new RuntimeException(
                        'Payment cannot be marked as paid from its current state.'
                    );
                }

                /*
                | Save Razorpay payment ID first.
                */

                $payment->razorpay_payment_id = $razorpayPaymentId;
                $payment->save();

                /*
                | Use the central payment state transition.
                */

                $payment->markAsPaid();

                /*
                |--------------------------------------------------------------------------
                | Update order
                |--------------------------------------------------------------------------
                |
                | Order currently supports:
                | pending -> paid
                |
                */

                $order->markAsPaid();

                /*
                |--------------------------------------------------------------------------
                | Find active reservations
                |--------------------------------------------------------------------------
                */

                $reservations = InventoryReservation::where(
                    'order_id',
                    $order->id
                )
                    ->where(
                        'status',
                        InventoryReservationStatus::ACTIVE->value
                    )
                    ->get();

                /*
                | Consume reservations.
                */

                foreach ($reservations as $reservation) {
                    $this->inventoryService->consumeReservation(
                        $reservation->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Dispatch payment captured event
                |--------------------------------------------------------------------------
                */

                PaymentCaptured::dispatch(
                    $payment->fresh('order')
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | 15. Payment failed
            |--------------------------------------------------------------------------
            */

            if ($eventType === 'payment.failed') {

                /*
                | Never change an already-paid payment to failed.
                */

                if ($payment->status === PaymentStatus::PAID) {
                    return;
                }

                /*
                | Only a created payment can become failed.
                */

                if ($payment->status !== PaymentStatus::CREATED) {
                    throw new RuntimeException(
                        'Payment cannot be marked as failed from its current state.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Find active reservations
                |--------------------------------------------------------------------------
                */

                $reservations = InventoryReservation::where(
                    'order_id',
                    $order->id
                )
                    ->where(
                        'status',
                        InventoryReservationStatus::ACTIVE->value
                    )
                    ->get();

                /*
                | Release reservations.
                */

                foreach ($reservations as $reservation) {
                    $this->inventoryService->releaseReservation(
                        $reservation->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Update Razorpay payment ID
                |--------------------------------------------------------------------------
                */

                $payment->razorpay_payment_id = $razorpayPaymentId;
                $payment->save();

                /*
                | Use the central payment state transition.
                */

                $payment->markAsFailed();

                /*
                |--------------------------------------------------------------------------
                | Update order
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                | Your current OrderStatus enum does not yet contain
                | PAYMENT_FAILED.
                |
                | Therefore this line will be handled in the next step.
                |
                */

                $order->markAsPaymentFailed();
            }
        });
    }
}
