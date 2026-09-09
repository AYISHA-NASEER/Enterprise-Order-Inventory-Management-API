<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\OrderItemController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\ShippingController;
use App\Http\Controllers\Api\V1\HealthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/v1/categories', [CategoryController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/v1/products', [ProductController::class, 'store']);
     Route::get('/v1/products', [ProductController::class, 'index'])->middleware('throttle:products');
    Route::get('/v1/products/{id}', [ProductController::class, 'show']);
    Route::patch('/v1/products/{id}', [ProductController::class, 'update']);
    Route::delete('/v1/products/{id}', [ProductController::class, 'destroy']);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/inventory', [InventoryController::class, 'store']);

    Route::get('/inventory/{productId}', [InventoryController::class, 'show']);

    Route::patch('/inventory/{productId}', [InventoryController::class, 'update']);

    Route::patch('/inventory/{productId}/increase', [InventoryController::class, 'increase']);

    Route::patch('/inventory/{productId}/decrease', [InventoryController::class, 'decrease']);
});



Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    Route::get('/cart', [CartController::class, 'show']);

    Route::post('/cart/items', [CartController::class, 'addItem']);

    Route::patch('/cart/items/{cartItemId}', [CartController::class, 'updateItem']);

    Route::delete('/cart/items/{cartItemId}', [CartController::class, 'removeItem']);

    Route::delete('/cart', [CartController::class, 'clear']);

});

Route::middleware('auth:sanctum')
    ->prefix('v1')
    ->group(function () {

        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);

    });



Route::post('/v1/order-items', [OrderItemController::class, 'store']);
Route::get('/v1/order-items', [OrderItemController::class, 'index']);
Route::get('/v1/order-items/{id}', [OrderItemController::class, 'show']);

Route::post('/v1/register', [AuthController::class, 'register']);


Route::post('/v1/login', [AuthController::class, 'login'])->middleware('throttle:login');


Route::middleware('auth:sanctum')->get('/v1/me', [AuthController::class, 'me']);
Route::middleware('auth:sanctum')->post('/v1/logout', [AuthController::class, 'logout']);


Route::post('/v1/checkout', [CheckoutController::class, 'checkout'])->middleware('auth:sanctum', 'throttle:checkout');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/v1/admin/test', function () {
        return response()->json(['ok' => true, 'message' => 'Admin access confirmed']);
    });
});
Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::get('/v1/manager/test', function () {
        return response()->json(['ok' => true, 'message' => 'Manager access confirmed']);
    });
});


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/v1/payments', [PaymentController::class, 'store']);

    Route::get('/v1/payments/{paymentId}', [PaymentController::class, 'show']);



});
Route::post(
    '/v1/payments/{paymentId}/verify',
    [PaymentController::class, 'verify']
);



Route::post('/v1/payments/webhook', [PaymentController::class, 'webhook']);


Route::get('/v1/shipments/{shipmentId}', [ShippingController::class, 'status']);



Route::get('/v1/health', HealthController::class);