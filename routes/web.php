<?php



use App\Models\Payment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\CustomerWebController;
use App\Http\Controllers\WarehouseWebController;
use App\Http\Controllers\AdminWebController;

Route::get('/payment-test/{paymentId}', function (int $paymentId) {

    $payment = Payment::findOrFail($paymentId);

    return view('payment', compact('payment'));

});




Route::get('/', function () {
    return view('auth.register');
});




Route::get('/register', [WebAuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [WebAuthController::class, 'register'])
    ->name('register.submit');


Route::get('/login', [WebAuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [WebAuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->name('logout');


Route::get('/dashboard', function () {
    return view('admin.dashboard');
})
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard');



Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/orders', [AdminWebController::class, 'index'])
        ->name('admin.orders.index');

    Route::get('/admin/payments', [AdminWebController::class, 'payments'])
        ->name('admin.payments.index');

    Route::get('/admin/payments/{payment}', [AdminWebController::class, 'paymentShow'])
        ->name('admin.payments.show');

    Route::get('/admin/inventory', [AdminWebController::class, 'inventory'])
        ->name('admin.inventory.index');

});


Route::middleware('auth')->group(function () {

    Route::get('/products', [ProductWebController::class, 'index'])
        ->name('products.index');

    Route::get('/products/create', [ProductWebController::class, 'create'])
        ->name('products.create');

    Route::post('/products', [ProductWebController::class, 'store'])
        ->name('products.store');

    Route::get('/products/{product}/edit', [ProductWebController::class, 'edit'])
        ->name('products.edit');

    Route::put('/products/{product}', [ProductWebController::class, 'update'])
        ->name('products.update');

});

Route::middleware(['auth', 'role:customer'])->group(function () {

    Route::get('/customer/dashboard', [CustomerWebController::class, 'dashboard'])
        ->name('customer.dashboard');

    Route::get('/customer/products', [CustomerWebController::class, 'products'])
        ->name('customer.products');
    Route::post(
        '/customer/cart/{productId}',
        [CustomerWebController::class, 'addToCart']
    )->name('customer.cart.add');

    Route::get('/customer/cart', [CustomerWebController::class, 'cart'])
        ->name('customer.cart');

    Route::put('/customer/cart/{cartItemId}', [CustomerWebController::class, 'updateCart'])
        ->name('customer.cart.update');

    Route::delete('/customer/cart/{cartItemId}', [CustomerWebController::class, 'removeFromCart'])
        ->name('customer.cart.remove');



    Route::post('/customer/checkout', [CustomerWebController::class, 'checkout'])
        ->name('customer.checkout');

    Route::get('/customer/order/{order}/success', [CustomerWebController::class, 'orderSuccess'])
        ->name('customer.order.success');

    Route::post('/customer/orders/{order}/pay', [CustomerWebController::class, 'pay'])->name('customer.orders.pay');


    Route::get('/customer/orders', [CustomerWebController::class, 'orders'])
        ->name('customer.orders');

    Route::get(
        '/customer/notifications',
        [CustomerWebController::class, 'notifications']
    )->name('customer.notifications');
});





Route::middleware(['auth', 'role:warehouse'])->group(function () {

    Route::get(
        '/warehouse/dashboard',
        [WarehouseWebController::class, 'index']
    )->name('warehouse.dashboard');

    Route::get(
        '/warehouse/inventory',
        [WarehouseWebController::class, 'inventory']
    )->name('warehouse.inventory');

    Route::post(
        '/warehouse/inventory/{inventory}/add-stock',
        [WarehouseWebController::class, 'addStock']
    )->name('warehouse.inventory.add-stock');

    Route::put(
        '/warehouse/inventory/{inventory}/update-stock',
        [WarehouseWebController::class, 'updateStock']
    )->name('warehouse.inventory.update-stock');

    Route::get(
        '/warehouse/orders',
        [WarehouseWebController::class, 'orders']
    )->name('warehouse.orders');

    Route::get(
        '/warehouse/shipments',
        [WarehouseWebController::class, 'shipments']
    )->name('warehouse.shipments');
});

Route::middleware('auth')->group(function () {

    Route::get('/admin/orders', [AdminWebController::class, 'index'])
        ->name('admin.orders.index');

});




