<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_requires_signature(): void
    {
        $response = $this->postJson('/api/v1/payments/webhook', [
            'event' => 'payment.captured',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'message' => 'Missing Razorpay signature.',
        ]);
    }
}