<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {
    }

    /**
     * Create inventory.
     *
     * Admin only.
     */
    public function store(StoreInventoryRequest $request)
    {
        $this->authorize('create', Inventory::class);

        $inventory = $this->inventoryService->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Inventory created successfully',
            'data' => $inventory,
        ], 201);
    }

    /**
     * View inventory for a product.
     *
     * Admin, Manager and Warehouse.
     */
    public function show(int $productId)
    {
        $inventory = $this->inventoryService
            ->findByProduct($productId);

        $this->authorize('view', $inventory);

        return response()->json([
            'data' => $inventory,
        ]);
    }

    /**
     * Update inventory quantity.
     *
     * Admin, Manager and Warehouse.
     */
    public function update(
        UpdateInventoryRequest $request,
        int $productId
    ) {
        $inventory = $this->inventoryService
            ->findByProduct($productId);

        $this->authorize('update', $inventory);

        $inventory = $this->inventoryService->updateQuantity(
            $productId,
            $request->validated()['quantity']
        );

        return response()->json([
            'message' => 'Inventory updated successfully',
            'data' => $inventory,
        ]);
    }

    /**
     * Increase inventory stock.
     *
     * Admin, Manager and Warehouse.
     */
    public function increase(Request $request, int $productId)
    {
        $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $inventory = $this->inventoryService
            ->findByProduct($productId);

        $this->authorize('update', $inventory);

        $inventory = $this->inventoryService->increaseStock(
            $productId,
            $request->integer('quantity')
        );

        return response()->json([
            'message' => 'Inventory increased successfully',
            'data' => $inventory,
        ]);
    }

    /**
     * Decrease inventory stock.
     *
     * Admin, Manager and Warehouse.
     */
    public function decrease(Request $request, int $productId)
    {
        $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $inventory = $this->inventoryService
            ->findByProduct($productId);

        $this->authorize('update', $inventory);

        $inventory = $this->inventoryService->decreaseStock(
            $productId,
            $request->integer('quantity')
        );

        return response()->json([
            'message' => 'Inventory decreased successfully',
            'data' => $inventory,
        ]);
    }
}