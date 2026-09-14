<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Inventory;
use App\Models\Category;


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
    public function categories()
    {
        $categories = Category::latest()->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }
    public function categoryCreate()
    {
        return view('admin.categories.create');
    }

    public function categoryStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
        ]);

        Category::create($validated);

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category created successfully.');
    }

    public function categoryEdit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function categoryUpdate(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:categories,slug,' . $category->id,
            ],
        ]);

        $category->update($validated);

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category updated successfully.');
    }
    public function categoryDestroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category deleted successfully.');
    }
    public function orderShow(Order $order)
    {
        $order->load([
            'user',
            'items.product',
            'payment',
            'shipment',
        ]);

        return view('admin.orders.show', compact('order'));
    }

}



