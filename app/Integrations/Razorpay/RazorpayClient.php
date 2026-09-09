<?php

namespace App\Integrations\Razorpay;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RazorpayClient
{
    private function client(): PendingRequest
    {
        return Http::baseUrl(
            config('services.razorpay.base_url') . '/v1'
        )
            ->withBasicAuth(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            )
            ->acceptJson()
            ->timeout(10)
            ->retry(3, 200);
    }

    /**
     * Create an order in Razorpay.
     */
    public function createOrder(
        int $amount,
        string $receipt
    ): array {
        $response = $this->client()->post('/orders', [
            'amount' => $amount,
            'currency' => 'INR',
            'receipt' => $receipt,
        ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Unable to create Razorpay order.'
            );
        }

        return $response->json();
    }

    /**
     * Verify Razorpay payment signature.
     */
    public function verifyPaymentSignature(
        string $razorpayOrderId,
        string $razorpayPaymentId,
        string $razorpaySignature
    ): bool {
        $payload = $razorpayOrderId . '|' . $razorpayPaymentId;

        $expectedSignature = hash_hmac(
            'sha256',
            $payload,
            config('services.razorpay.secret')
        );

        return hash_equals(
            $expectedSignature,
            $razorpaySignature
        );
    }
}