<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    /**
     * Display all orders.
     *
     * Admin and Manager can view all orders.
     */
    public function index()
    {
        // dd(auth()->user());
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->all();

        return response()->json([
            'data' => $orders,
        ]);
    }

    /**
     * Display a specific order.
     *
     * Admin/Manager can view any order.
     * Customer can view only their own order.
     */
    public function show(int $id)
    {
        $order = $this->orderService->find($id);

        $this->authorize('view', $order);

        return response()->json([
            'data' => $order,
        ]);
    }

    /**
     * Create an order.
     *
     * Direct order creation is restricted
     * to Admin and Manager.
     *
     * Customer checkout should normally use
     * CheckoutController instead.
     */
    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);

        $validated = $request->validated();

        $order = $this->orderService->create($validated);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }
}