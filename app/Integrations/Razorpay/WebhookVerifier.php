<?php

namespace App\Integrations\Razorpay;

use RuntimeException;

class WebhookVerifier
{
    public function verify(
        string $payload,
        string $signature
    ): void {
        $secret = config(
            'services.razorpay.webhook_secret'
        );

        if (!$secret) {
            throw new RuntimeException(
                'Razorpay webhook secret is not configured.'
            );
        }

        $expectedSignature = hash_hmac(
            'sha256',
            $payload,
            $secret
        );

        if (
            !hash_equals(
                $expectedSignature,
                $signature
            )
        ) {
            throw new RuntimeException(
                'Invalid Razorpay webhook signature.'
            );
        }
    }
}