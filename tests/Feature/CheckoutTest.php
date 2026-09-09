<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(int $quantity = 10): Product
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Laptop',
            'sku' => 'TEST-LAPTOP-001',
            'description' => 'Test laptop',
            'price' => 50000,
            'status' => 'active',
            'source' => 'local',
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);

        return $product;
    }

    public function test_user_can_checkout_product(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ], [
            'Idempotency-Key' => 'test-checkout-001',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
        ]);
    }

    public function test_same_idempotency_key_does_not_create_duplicate_order(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct();

        Sanctum::actingAs($user);

        $headers = [
            'Idempotency-Key' => 'duplicate-test-001',
        ];

        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        // First checkout
        $firstResponse = $this->postJson(
            '/api/v1/checkout',
            $payload,
            $headers
        );

        $firstResponse->assertStatus(201);

        // Second checkout with the SAME idempotency key
        $secondResponse = $this->postJson(
            '/api/v1/checkout',
            $payload,
            $headers
        );

        $secondResponse->assertStatus(200);

        // Only one order should exist
        $this->assertDatabaseCount('orders', 1);
    }
}