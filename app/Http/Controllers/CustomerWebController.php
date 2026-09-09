<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Order;
use Illuminate\Support\Str;


class CustomerWebController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CheckoutService $checkoutService
    ) {
    }

    public function dashboard(): View
    {
        return view('customer.dashboard');
    }

    public function products(): View
    {
        $products = Product::where('status', 'active')
            ->with('category')
            ->latest()
            ->get();

        return view('customer.products.index', compact('products'));
    }

    public function addToCart(
        Request $request,
        int $productId
    ): RedirectResponse {

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {

            $this->cartService->addItem(
                auth()->id(),
                $productId,
                $validated['quantity']
            );

            return redirect()
                ->route('customer.products')
                ->with('success', 'Product added to cart successfully.');

        } catch (\RuntimeException $e) {

            return back()
                ->with('error', $e->getMessage());

        }

    }
    public function cart(): View
    {
        $cart = $this->cartService->getCart(auth()->id());

        $checkoutKey = session('checkout_idempotency_key');

        if (!$checkoutKey) {
            $checkoutKey = (string) Str::uuid();

            session()->put(
                'checkout_idempotency_key',
                $checkoutKey
            );
        }

        return view('customer.cart.index', compact(
            'cart',
            'checkoutKey'
        ));
    }

    public function updateCart(
        Request $request,
        int $cartItemId
    ): RedirectResponse {

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {

            $this->cartService->updateItem(
                auth()->id(),
                $cartItemId,
                $validated['quantity']
            );

            return redirect()
                ->route('customer.cart')
                ->with('success', 'Cart quantity updated successfully.');

        } catch (\RuntimeException $e) {

            return back()
                ->with('error', $e->getMessage());

        }
    }
    public function removeFromCart(int $cartItemId): RedirectResponse
    {
        try {
            $this->cartService->removeItem(
                auth()->id(),
                $cartItemId
            );

            return redirect()
                ->route('customer.cart')
                ->with('success', 'Item removed from cart successfully.');

        } catch (\RuntimeException $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'idempotency_key' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        try {

            $cart = $this->cartService->getCart(auth()->id());

            if (!$cart || $cart->items->isEmpty()) {
                return back()->with(
                    'error',
                    'Your cart is empty.'
                );
            }

            $items = $cart->items->map(function ($item) {
                return [
                    'product_id' => (int) $item->product_id,
                    'quantity' => (int) $item->quantity,
                ];
            })->values()->toArray();

            $order = $this->checkoutService->checkout(
                auth()->id(),
                // $items,
                $validated['idempotency_key']
            );

            session()->forget('checkout_idempotency_key');

            return redirect()
                ->route('customer.orders')
                ->with(
                    'success',
                    'Order created successfully. Order #' . $order->id
                );

        } catch (\RuntimeException $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
    public function orderSuccess(int $orderId): View
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->with('items.product')
            ->firstOrFail();

        return view('customer.orders.success', compact('order'));
    }

    public function pay(int $orderId): RedirectResponse
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status !== 'pending') {
            return back()->with(
                'error',
                'This order is not available for payment.'
            );
        }

        if ($order->payment) {
            $payment = $order->payment;
        } else {
            $payment = app(PaymentService::class)
                ->createPayment($order->id);
        }

        return redirect('/payment-test/' . $payment->id);
    }
    public function orders(): View
    {
        $orders = Order::with([
            'items.product',
            'payment',
            'shipment',
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('customer.orders.index', compact('orders'));
    }
    public function notifications(): View
    {
        

        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->get();

        return view(
            'customer.notifications.index',
            compact('notifications')
        );
    }
}