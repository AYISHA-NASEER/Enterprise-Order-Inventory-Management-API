@extends('layouts.app')

@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>🛒 My Cart</h2>

            <p class="text-muted mb-0">
                Review and manage the products in your cart.
            </p>
        </div>

        <a href="{{ route('customer.products') }}" class="btn btn-primary">
            🛍 Continue Shopping
        </a>

    </div>


    {{-- Cart Has Items --}}
    @if($cart->items->count() > 0)

        @php
            $cartTotal = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
        @endphp


        {{-- Cart Items --}}
        <div class="card shadow-sm">

            <div class="card-header">
                <h5 class="mb-0">
                    Cart Items
                </h5>
            </div>


            <div class="card-body">

                @foreach($cart->items as $item)

                    <div class="row align-items-center border-bottom py-3">

                        {{-- Product --}}
                        <div class="col-md-3">

                            <h5 class="mb-1">
                                {{ $item->product->name }}
                            </h5>

                            <small class="text-muted">
                                SKU: {{ $item->product->sku }}
                            </small>

                        </div>


                        {{-- Price --}}
                        <div class="col-md-2">

                            <small class="text-muted d-block">
                                Price
                            </small>

                            <strong>
                                ₹{{ number_format($item->product->price, 2) }}
                            </strong>

                        </div>


                        {{-- Quantity --}}
                        <div class="col-md-3">

                            <small class="text-muted d-block mb-1">
                                Quantity
                            </small>

                            <form action="{{ route('customer.cart.update', $item->id) }}" method="POST">

                                @csrf
                                @method('PUT')

                                <div class="input-group">

                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control">

                                    <button type="submit" class="btn btn-primary">
                                        Update
                                    </button>

                                </div>

                            </form>

                        </div>


                        {{-- Item Total --}}
                        <div class="col-md-2">

                            <small class="text-muted d-block">
                                Total
                            </small>

                            <strong class="text-success">

                                ₹{{ number_format(
                        $item->product->price * $item->quantity,
                        2
                    ) }}

                            </strong>

                        </div>


                        {{-- Remove --}}
                        <div class="col-md-2 text-end">

                            <form action="{{ route('customer.cart.remove', $item->id) }}" method="POST">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Remove this item from your cart?')">
                                    🗑 Remove
                                </button>

                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        <div class="row justify-content-center mt-4">
            <div class="col-md-6 col-lg-5">

                <div class="card shadow-sm">
                    <div class="card-body">

                        <h3 class="text-center mb-4">
                            Order Summary
                        </h3>

                        @php
                            $cartTotal = $cart->items->sum(
                                fn($item) => $item->product->price * $item->quantity
                            );

                            $totalItems = $cart->items->sum('quantity');
                        @endphp

                        <div class="d-flex justify-content-between mb-3">
                            <span>Items</span>
                            <strong>{{ $totalItems }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Total</span>
                            <strong class="text-success fs-4">
                                ₹{{ number_format($cartTotal, 2) }}
                            </strong>
                        </div>

                        <hr>

                        <div class="text-center mt-4">
                            <form action="{{ route('customer.checkout') }}" method="POST" id="checkoutForm">
                                @csrf

                                <input type="hidden" name="idempotency_key" value="{{ $checkoutKey }}">
                                <button type="submit" class="btn btn-success btn-lg px-5" id="checkoutButton">
                                    💳 Checkout
                                </button>
                            </form>

                            <script>
                                document.getElementById('checkoutForm').addEventListener('submit', function () {
                                    const button = document.getElementById('checkoutButton');

                                    button.disabled = true;
                                    button.innerHTML = '⏳ Processing...';
                                });
                            </script>
                        </div>

                    </div>
                </div>

            </div>
        </div>


    @else

        {{-- Empty Cart --}}
        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <div class="display-1 mb-3">
                    🛒
                </div>

                <h3>
                    Your cart is empty
                </h3>

                <p class="text-muted">
                    You haven't added any products yet.
                </p>

                <a href="{{ route('customer.products') }}" class="btn btn-primary">
                    🛍 Browse Products
                </a>

            </div>

        </div>

    @endif

@endsection