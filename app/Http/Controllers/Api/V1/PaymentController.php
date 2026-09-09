<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Integrations\Razorpay\WebhookVerifier;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Services\PaymentWebhookService;



class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private WebhookVerifier $webhookVerifier,
        private PaymentWebhookService $paymentWebhookService
    ) {
    }


    /**
     * Create Razorpay payment/order.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => [
                'required',
                'integer',
                'exists:orders,id',
            ],
        ]);

        $payment = $this->paymentService->createPayment(
            $validated['order_id']
        );

        return response()->json([
            'message' => 'Razorpay payment initiated successfully.',
            'data' => $payment,
        ], 201);
    }

    /**
     * Show payment.
     */
    public function show(int $paymentId): JsonResponse
    {
        $payment = $this->paymentService->find($paymentId);

        return response()->json([
            'data' => $payment,
        ]);
    }

    /**
     * Verify Razorpay payment.
     */
    public function verify(
        Request $request,
        int $paymentId
    ): JsonResponse {
        $validated = $request->validate([
            'razorpay_order_id' => [
                'required',
                'string',
            ],

            'razorpay_payment_id' => [
                'required',
                'string',
            ],

            'razorpay_signature' => [
                'required',
                'string',
            ],
        ]);

        try {
            $payment = $this->paymentService->verifyPayment(
                $paymentId,
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            return response()->json([
                'message' => 'Payment verified successfully.',
                'data' => $payment,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Razorpay webhook.
     */
    /**
     * Razorpay webhook.
     */
    public function webhook(Request $request): JsonResponse
    {
        // 1. Get the original/raw request body.
        $payload = $request->getContent();

        // 2. Get Razorpay webhook signature.
        $signature = $request->header('X-Razorpay-Signature');

        if (!$signature) {
            return response()->json([
                'message' => 'Missing Razorpay signature.',
            ], 400);
        }

        // 3. Verify Razorpay signature.
        try {
            $this->webhookVerifier->verify(
                $payload,
                $signature
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        // 4. Decode JSON payload.
        $data = json_decode($payload, true);

        if (!is_array($data)) {
            return response()->json([
                'message' => 'Invalid webhook payload.',
            ], 400);
        }

        // 5. Get Razorpay's unique webhook event ID.
        $eventId = $request->header('x-razorpay-event-id');

        if (!$eventId) {
            return response()->json([
                'message' => 'Missing Razorpay event ID.',
            ], 400);
        }

        // 6. Send the webhook to the service.
        $this->paymentWebhookService->process(
            $data,
            $eventId
        );

        // 7. Tell Razorpay we received the webhook.
        return response()->json([
            'message' => 'Webhook processed successfully.',
        ], 200);
    }
}
