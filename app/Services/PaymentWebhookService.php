<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use App\Events\PaymentCaptured;
use App\Models\InventoryReservation;


class PaymentWebhookService
{
    public function process(array $data, string $eventId): void
    {
        if ($eventId === '') {
            throw new RuntimeException(
                'Webhook event ID is missing.'
            );
        }

        $eventType = $data['event'] ?? null;

        if (!$eventType) {
            throw new RuntimeException(
                'Webhook event type is missing.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Extract Razorpay payment information
        |--------------------------------------------------------------------------
        */

        $paymentEntity = $data['payload']['payment']['entity'] ?? [];

        $razorpayPaymentId = $paymentEntity['id'] ?? null;
        $razorpayOrderId = $paymentEntity['order_id'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Process webhook inside a database transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($data, $eventId, $eventType, $razorpayPaymentId, $razorpayOrderId) {

            /*
            |--------------------------------------------------------------------------
            | Duplicate webhook check
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
            | Save webhook event
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
            | Find local payment
            |--------------------------------------------------------------------------
            */

            if (!$razorpayOrderId) {
                return;
            }

            $payment = Payment::where(
                'razorpay_order_id',
                $razorpayOrderId
            )->first();

            if (!$payment) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Payment captured
            |--------------------------------------------------------------------------
            */

            if ($eventType === 'payment.captured') {

                $payment->update([
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'status' => 'paid',
                ]);

                Order::where(
                    'id',
                    $payment->order_id
                )->update([
                            'status' => 'paid',
                        ]);

                InventoryReservation::where('order_id', $payment->order_id)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'consumed',
                    ]);

                PaymentCaptured::dispatch($payment->fresh());
            }

            /*
            |--------------------------------------------------------------------------
            | Payment failed
            |--------------------------------------------------------------------------
            */

            if ($eventType === 'payment.failed') {

                // Never change an already-paid payment back to failed.
                if ($payment->status !== 'paid') {

                    $payment->update([
                        'razorpay_payment_id' => $razorpayPaymentId,
                        'status' => 'failed',
                    ]);

                    Order::where(
                        'id',
                        $payment->order_id
                    )->update([
                                'status' => 'payment_failed',
                            ]);
                }
            }
        });
    }
}