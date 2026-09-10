<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Order;

class WarehouseWebController extends Controller
{
    // Warehouse Dashboard
    public function index(): View
    {
        return view('warehouse.dashboard');
    }


    // View Inventory
    public function inventory(): View
    {
        $inventory = Inventory::with('product.category')
            ->latest()
            ->get();

        return view(
            'warehouse.inventory.index',
            compact('inventory')
        );
    }


    // Add Stock
    public function addStock(
        Request $request,
        Inventory $inventory
    ): RedirectResponse {

        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $inventory->increment(
            'quantity',
            $validated['quantity']
        );

        return back()->with(
            'success',
            'Stock added successfully.'
        );
    }


    // Update Stock
    public function updateStock(
        Request $request,
        Inventory $inventory
    ): RedirectResponse {

        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $inventory->update([
            'quantity' => $validated['quantity'],
        ]);

        return back()->with(
            'success',
            'Stock updated successfully.'
        );
    }
    public function orders(): View
    {
        $orders = Order::with([
            'user',
            'items.product',
            'payment',
            'shipment',
        ])
            ->where('status', 'paid')
            ->latest()
            ->get();

        return view('warehouse.orders', compact('orders'));
    }
    public function shipments(): View
    {
        $shipments = \App\Models\Shipment::with([
            'order.user',
        ])
            ->latest()
            ->get();

        return view('warehouse.shipments', compact('shipments'));
    }
}