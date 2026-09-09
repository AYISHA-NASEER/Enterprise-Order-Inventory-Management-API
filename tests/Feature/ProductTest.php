<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_products(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200);
    }
}