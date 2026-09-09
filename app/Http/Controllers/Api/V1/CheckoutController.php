<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService
    ) {
    }

    public function checkout(Request $request): JsonResponse
    {
        /*
         * Get the idempotency key from the request header.
         */
        $idempotencyKey = $request->header('Idempotency-Key');

        if (!$idempotencyKey) {
            return response()->json([
                'message' => 'Idempotency-Key header is required.',
            ], 400);
        }

        /*
         * Get authenticated user.
         */
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        try {

            /*
             * CheckoutService gets the active cart
             * from the database.
             *
             * Therefore we only pass:
             * 1. User ID
             * 2. Idempotency Key
             */
            $order = $this->checkoutService->checkout(
                $user->id,
                $idempotencyKey
            );

            return response()->json([
                'message' => 'Checkout successful.',
                'data' => $order,
            ], 201);

        } catch (\RuntimeException $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}