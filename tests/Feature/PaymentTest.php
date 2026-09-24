<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_have_only_one_payment(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'TEST-ORDER-001',
            'total_amount' => 50000,
            'status' => 'pending',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 50000,
            'status' => 'created',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 50000,
            'status' => 'created',
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
}