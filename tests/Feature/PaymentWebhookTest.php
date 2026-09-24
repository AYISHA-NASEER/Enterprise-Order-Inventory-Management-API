<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
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


    public function test_failed_payment_releases_inventory_reservation(): void
    {
        config([
            'services.razorpay.webhook_secret' => 'test-webhook-secret',
        ]);

        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Laptop',
            'sku' => 'TEST-LAPTOP-001',
            'price' => 50000,
        ]);

        /*
         * Start with 9 items in inventory.
         */
        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 9,
        ]);

        /*
         * Create the order.
         */
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-001',
            'total_amount' => 50000,
            'status' => 'pending',
        ]);

        /*
         * Create the local payment.
         */
        Payment::create([
            'order_id' => $order->id,
            'razorpay_order_id' => 'order_test_001',
            'razorpay_payment_id' => null,
            'amount' => 50000,
            'status' => 'created',
        ]);

        /*
         * Reserve 1 item for this order.
         */
        $reservation = InventoryReservation::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'quantity' => 1,
            'status' => 'active',
            'expires_at' => now()->addMinutes(30),
        ]);

        /*
         * Fake Razorpay failed-payment webhook.
         *
         * ₹50,000 = 5,000,000 paise.
         */
        $payload = [
            'event' => 'payment.failed',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_test_001',
                        'order_id' => 'order_test_001',
                        'amount' => 5000000,
                        'currency' => 'INR',
                        'status' => 'failed',
                    ],
                ],
            ],
        ];

        $rawPayload = json_encode($payload);

        /*
         * Create the signature using the same secret
         * configured above.
         */
        $signature = hash_hmac(
            'sha256',
            $rawPayload,
            'test-webhook-secret'
        );

        /*
         * Send the webhook.
         */
        $response = $this->call(
            'POST',
            '/api/v1/payments/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
                'HTTP_X_RAZORPAY_EVENT_ID' => 'event_test_001',
            ],
            $rawPayload
        );

        /*
         * Webhook should succeed.
         */
        $response->assertStatus(200);

        /*
         * Reservation should be released.
         */
        $this->assertDatabaseHas('inventory_reservations', [
            'id' => $reservation->id,
            'status' => 'released',
        ]);

        /*
         * Inventory should be restored:
         *
         * 9 + 1 released item = 10.
         */
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        /*
         * Payment should become failed.
         */
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'razorpay_order_id' => 'order_test_001',
            'razorpay_payment_id' => 'pay_test_001',
            'status' => 'failed',
        ]);

        /*
         * Order should become payment_failed.
         */
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'payment_failed',
        ]);
    }
    public function test_failed_payment_webhook_is_repeat_safe(): void
    {
        config([
            'services.razorpay.webhook_secret' => 'test-webhook-secret',
        ]);

        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Laptop',
            'sku' => 'TEST-LAPTOP-002',
            'price' => 50000,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 9,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-002',
            'total_amount' => 50000,
            'status' => 'pending',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'razorpay_order_id' => 'order_test_002',
            'amount' => 50000,
            'status' => 'created',
        ]);

        InventoryReservation::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'quantity' => 1,
            'status' => 'active',
            'expires_at' => now()->addMinutes(30),
        ]);

        $payload = [
            'event' => 'payment.failed',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_test_002',
                        'order_id' => 'order_test_002',
                        'amount' => 5000000,
                        'currency' => 'INR',
                        'status' => 'failed',
                    ],
                ],
            ],
        ];

        $rawPayload = json_encode($payload);

        $signature = hash_hmac(
            'sha256',
            $rawPayload,
            'test-webhook-secret'
        );

        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            'HTTP_X_RAZORPAY_EVENT_ID' => 'event_test_002',
        ];

        // First webhook.
        $firstResponse = $this->call(
            'POST',
            '/api/v1/payments/webhook',
            [],
            [],
            [],
            $headers,
            $rawPayload
        );

        $firstResponse->assertStatus(200);

        // Second webhook with the same event ID.
        $secondResponse = $this->call(
            'POST',
            '/api/v1/payments/webhook',
            [],
            [],
            [],
            $headers,
            $rawPayload
        );

        $secondResponse->assertStatus(200);

        // Inventory must still be 10, not 11.
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        // Reservation must remain released.
        $this->assertDatabaseHas('inventory_reservations', [
            'order_id' => $order->id,
            'status' => 'released',
        ]);
    }
    public function test_payment_service_rejects_second_payment_for_same_order(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'TEST-ORDER-002',
            'total_amount' => 50000,
            'status' => 'pending',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 50000,
            'status' => 'created',
        ]);

        $paymentService = app(\App\Services\PaymentService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Payment already exists for this order.'
        );

        $paymentService->createPayment($order->id);
    }
    public function test_successful_payment_consumes_inventory_reservation(): void
    {
        config([
            'services.razorpay.webhook_secret' => 'test-webhook-secret',
        ]);

        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Laptop',
            'sku' => 'TEST-LAPTOP-003',
            'price' => 50000,
        ]);

        /*
         * Start with 9 items available.
         */
        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 9,
        ]);

        /*
         * Create the pending order.
         */
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-003',
            'total_amount' => 50000,
            'status' => 'pending',
        ]);

        /*
         * Create the local payment.
         */
        Payment::create([
            'order_id' => $order->id,
            'razorpay_order_id' => 'order_test_003',
            'razorpay_payment_id' => null,
            'amount' => 50000,
            'status' => 'created',
        ]);

        /*
         * Reserve 1 item for this order.
         */
        $reservation = InventoryReservation::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'quantity' => 1,
            'status' => 'active',
            'expires_at' => now()->addMinutes(30),
        ]);

        /*
         * Fake successful Razorpay webhook.
         *
         * ₹50,000 = 5,000,000 paise.
         */
        $payload = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_test_003',
                        'order_id' => 'order_test_003',
                        'amount' => 5000000,
                        'currency' => 'INR',
                        'status' => 'captured',
                    ],
                ],
            ],
        ];

        $rawPayload = json_encode($payload);

        /*
         * Generate the Razorpay webhook signature.
         */
        $signature = hash_hmac(
            'sha256',
            $rawPayload,
            'test-webhook-secret'
        );

        /*
         * Send the webhook.
         */
        $response = $this->call(
            'POST',
            '/api/v1/payments/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
                'HTTP_X_RAZORPAY_EVENT_ID' => 'event_test_003',
            ],
            $rawPayload
        );

        $response->assertStatus(200);

        /*
         * Reservation should be consumed.
         */
        $this->assertDatabaseHas('inventory_reservations', [
            'id' => $reservation->id,
            'status' => 'consumed',
        ]);

        /*
         * Inventory should remain at 9.
         *
         * The item was already reserved, so successful payment
         * consumes the reservation rather than adding stock back.
         */
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 9,
        ]);

        /*
         * Payment should become paid.
         */
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'razorpay_order_id' => 'order_test_003',
            'razorpay_payment_id' => 'pay_test_003',
            'status' => 'paid',
        ]);

        /*
         * Order should become paid.
         */
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);
    }
}