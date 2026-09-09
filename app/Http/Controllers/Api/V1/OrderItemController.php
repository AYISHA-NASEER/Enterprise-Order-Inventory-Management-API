<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderItemService;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function __construct(
        private OrderItemService $orderItemService
    ) {
    }

    public function index()
    {
        $items = $this->orderItemService->all();

        return response()->json([
            'data' => $items,
        ]);
    }

    public function show(int $id)
    {
        $item = $this->orderItemService->find($id);

        return response()->json([
            'data' => $item,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'total_price' => ['required', 'numeric', 'min:0'],
        ]);

        $item = $this->orderItemService->create($validated);

        return response()->json([
            'message' => 'Order item created successfully',
            'data' => $item,
        ], 201);
    }
}