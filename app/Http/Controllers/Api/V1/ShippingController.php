<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;

class ShippingController extends Controller
{
    public function __construct(
        private ShippingService $shippingService
    ) {
    }

    public function status(int $shipmentId): JsonResponse
    {
        $shipment = $this->shippingService->getStatus($shipmentId);

        return response()->json([
            'message' => 'Shipment status retrieved successfully.',
            'data' => $shipment,
        ]);
    }
}