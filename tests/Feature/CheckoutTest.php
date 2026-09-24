<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
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

        // Create the user's active cart.
        $cart = Cart::create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        // Add the product to the cart.
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Sanctum::actingAs($user);

        // Checkout uses the authenticated user's cart.
        $response = $this->postJson(
            '/api/v1/checkout',
            [],
            [
                'Idempotency-Key' => 'test-checkout-001',
            ]
        );

        $response->assertStatus(201);

        // Verify that the order was created.
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
        ]);

        // Verify that the idempotency record belongs to this user.
        $this->assertDatabaseHas('idempotency_keys', [
            'user_id' => $user->id,
            'key' => 'test-checkout-001',
            'status' => 'completed',
        ]);

        // Verify that a request fingerprint was stored.
        $this->assertDatabaseMissing('idempotency_keys', [
            'user_id' => $user->id,
            'key' => 'test-checkout-001',
            'request_fingerprint' => null,
        ]);
    }

    public function test_same_idempotency_key_does_not_create_duplicate_order(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct();

        // Create the user's active cart.
        $cart = Cart::create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        // Add the product to the cart.
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Sanctum::actingAs($user);

        $headers = [
            'Idempotency-Key' => 'duplicate-test-001',
        ];

        // First checkout.
        $firstResponse = $this->postJson(
            '/api/v1/checkout',
            [],
            $headers
        );

        $firstResponse->assertStatus(201);

        // Verify the idempotency record was created.
        $this->assertDatabaseHas('idempotency_keys', [
            'user_id' => $user->id,
            'key' => 'duplicate-test-001',
            'status' => 'completed',
        ]);

        // Verify the fingerprint is not null.
        $this->assertDatabaseMissing('idempotency_keys', [
            'user_id' => $user->id,
            'key' => 'duplicate-test-001',
            'request_fingerprint' => null,
        ]);

        // Second checkout with the SAME idempotency key.
        $secondResponse = $this->postJson(
            '/api/v1/checkout',
            [],
            $headers
        );

        $secondResponse->assertStatus(201);

        // Only one order should exist.
        $this->assertDatabaseCount('orders', 1);

        // Only one idempotency record should exist.
        $this->assertDatabaseCount('idempotency_keys', 1);
    }
    public function test_same_idempotency_key_can_be_used_by_different_users(): void
    {
        $userOne = User::factory()->create([
            'role' => 'customer',
        ]);

        $userTwo = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct(quantity: 10);

        // User 1 cart.
        $cartOne = Cart::create([
            'user_id' => $userOne->id,
            'status' => 'active',
        ]);

        CartItem::create([
            'cart_id' => $cartOne->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // User 2 cart.
        $cartTwo = Cart::create([
            'user_id' => $userTwo->id,
            'status' => 'active',
        ]);

        CartItem::create([
            'cart_id' => $cartTwo->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $headers = [
            'Idempotency-Key' => 'same-key-for-different-users',
        ];

        // User 1 checkout.
        Sanctum::actingAs($userOne);

        $firstResponse = $this->postJson(
            '/api/v1/checkout',
            [],
            $headers
        );

        $firstResponse->assertStatus(201);

        $firstOrderId = $firstResponse->json('data.id');

        // User 2 uses the SAME idempotency key.
        Sanctum::actingAs($userTwo);

        $secondResponse = $this->postJson(
            '/api/v1/checkout',
            [],
            $headers
        );

        $secondResponse->assertStatus(201);

        $secondOrderId = $secondResponse->json('data.id');

        // Each user must receive a different order.
        $this->assertNotSame(
            $firstOrderId,
            $secondOrderId
        );

        // User 1 owns the first order.
        $this->assertDatabaseHas('orders', [
            'id' => $firstOrderId,
            'user_id' => $userOne->id,
        ]);

        // User 2 owns the second order.
        $this->assertDatabaseHas('orders', [
            'id' => $secondOrderId,
            'user_id' => $userTwo->id,
        ]);

        // Two separate idempotency records exist.
        $this->assertDatabaseHas('idempotency_keys', [
            'user_id' => $userOne->id,
            'key' => 'same-key-for-different-users',
        ]);

        $this->assertDatabaseHas('idempotency_keys', [
            'user_id' => $userTwo->id,
            'key' => 'same-key-for-different-users',
        ]);
    }
    public function test_user_can_only_checkout_their_own_cart(): void
    {
        $userOne = User::factory()->create([
            'role' => 'customer',
        ]);

        $userTwo = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct(quantity: 10);

        /*
         * Create a cart belonging to User 1.
         */
        $cart = Cart::create([
            'user_id' => $userOne->id,
            'status' => 'active',
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        /*
         * Authenticate as User 2.
         */
        Sanctum::actingAs($userTwo);

        /*
         * User 2 tries to checkout.
         *
         * User 2 does not have an active cart.
         */
        $response = $this->postJson(
            '/api/v1/checkout',
            [],
            [
                'Idempotency-Key' => 'user-two-checkout-001',
            ]
        );

        /*
         * Checkout should fail because User 2
         * cannot use User 1's cart.
         */
        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'Active cart not found.',
        ]);

        /*
         * No order should have been created.
         */
        $this->assertDatabaseCount('orders', 0);

        /*
         * User 1's cart must still exist.
         */
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'user_id' => $userOne->id,
            'status' => 'active',
        ]);

        /*
         * User 1's cart item must still exist.
         */
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }
    public function test_checkout_fails_when_inventory_is_insufficient(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        // Only 1 item is available.
        $product = $this->createProduct(quantity: 1);

        $cart = Cart::create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        // Customer requests 2 items.
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/v1/checkout',
            [],
            [
                'Idempotency-Key' => 'insufficient-stock-001',
            ]
        );

        $response->assertStatus(422);

        // No order should be created.
        $this->assertDatabaseCount('orders', 0);

        // Inventory should remain unchanged.
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }
    public function test_unauthenticated_user_cannot_checkout(): void
    {
        $response = $this->postJson(
            '/api/v1/checkout',
            [],
            [
                'Idempotency-Key' => 'unauthenticated-checkout-001',
            ]
        );

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);

        $this->assertDatabaseCount('orders', 0);
    }
}
