@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        <div class="mb-4">
            <h2>Welcome, {{ auth()->user()->name }} 👋</h2>
            <p class="text-muted">
                Welcome to your customer dashboard.
            </p>
        </div>

        <div class="row g-4">

            {{-- Browse Products --}}
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h4>🛍️</h4>
                        <h5 class="card-title">Browse Products</h5>
                        <p class="card-text">
                            View available products and add them to your cart.
                        </p>

                        <a href="{{ route('customer.products') }}" class="btn btn-primary">
                            Browse Products
                        </a>
                    </div>
                </div>
            </div>

            {{-- Cart --}}
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h4>🛒</h4>
                        <h5 class="card-title">My Cart</h5>
                        <p class="card-text">
                            View and manage the products in your cart.
                        </p>

                        <a href="{{ route('customer.cart') }}" class="btn btn-success">
                            View Cart
                        </a>
                    </div>
                </div>
            </div>

            {{-- Orders --}}
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h4>📦</h4>
                        <h5 class="card-title">My Orders</h5>
                        <p class="card-text">
                            View your orders and their status.
                        </p>

                        <a href="{{ route('customer.orders') }}" class="btn btn-warning">
                            View Orders
                        </a>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h4>🔔</h4>
                            <h5 class="card-title">Notifications</h5>
                            <p class="card-text">
                                View your payment and order notifications.
                            </p>

                            <a href="{{ route('customer.notifications') }}" class="btn btn-secondary">
                                Notifications
                            </a>
                        </div>
                    </div>
                </div>

        </div>

    </div>

@endsection