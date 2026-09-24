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

    public function index(Request $request)
    {
        $items = $this->orderItemService->allForUser(
            $request->user()->id
        );

        return response()->json([
            'data' => $items,
        ]);
    }

    public function show(int $id)
    {
        $item = $this->orderItemService->find($id);

        $this->authorize('view', $item->order);

        return response()->json([
            'data' => $item,
        ]);
    }

}