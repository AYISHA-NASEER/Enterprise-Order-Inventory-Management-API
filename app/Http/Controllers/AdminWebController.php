<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Inventory;


class AdminWebController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }
    public function payments()
{
    $payments = Payment::with([
        'order.user',
    ])
    ->latest()
    ->paginate(10);

    return view('admin.payments.index', compact('payments'));
}
public function paymentShow(Payment $payment)
{
    $payment->load([
        'order.user',
        'order.items.product',
        'order.shipment',
    ]);

    return view('admin.payments.show', compact('payment'));
}
public function inventory()
{
    $inventories = Inventory::with('product')
        ->latest()
        ->paginate(10);

    return view('admin.inventory.index', compact('inventories'));
}
}






