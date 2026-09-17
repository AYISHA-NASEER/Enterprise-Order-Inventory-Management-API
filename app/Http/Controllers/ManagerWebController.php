<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\Inventory;
use App\Models\Product;


class ManagerWebController extends Controller
{
    public function orders()
    {
        $orders = Order::with([
            'user',
            'items.product',
            'payment',
            'shipment',
        ])
            ->latest()
            ->paginate(10);

        return view('manager.orders', compact('orders'));
    }
    public function inventory()
    {
        $inventories = Inventory::with('product')
            ->latest()
            ->paginate(10);

        return view('manager.inventory', compact('inventories'));
    }
    public function reports()
    {
        $totalOrders = Order::count();

        $pendingOrders = Order::where('status', 'pending')->count();

        $totalProducts = Product::count();

        $totalInventory = Inventory::sum('quantity');

        $lowStockProducts = Inventory::where('quantity', '<=', 10)->count();

        return view('manager.reports', compact(
            'totalOrders',
            'pendingOrders',
            'totalProducts',
            'totalInventory',
            'lowStockProducts'
        ));
    }
    public function supplierSync()
    {
        return view('manager.supplier-sync', [
            'supplier' => 'DummyJSON',
            'status' => 'Configured',
            'message' => 'Supplier synchronization is handled by the scheduled SyncSupplierProductsJob.',
        ]);
    }
}
