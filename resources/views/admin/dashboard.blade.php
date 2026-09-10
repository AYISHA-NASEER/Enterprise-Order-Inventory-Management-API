@extends('layouts.app')

@section('content')

    <!-- <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h1>Dashboard</h1>
                                                        <p class="text-muted">
                                                            Enterprise Order & Inventory Management
                                                        </p>
                                                    </div>
                                                </div> -->
    <div class="mb-4">
        <h2>Welcome, {{ auth()->user()->name }} 👋</h2>
        <p class="text-muted">
            Welcome to your admin dashboard.
        </p>
    </div>
    <div class="row g-4">

        <div class="col-md-3">

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Products</h5>
                    <p>Manage products and catalogue.</p>

                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        View Products
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Inventory</h5>
                    <p>Manage product stock.</p>

                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary">
                        View Inventory
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Orders</h5>
                    <p>View customer orders.</p>

                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
                        View Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Payments</h5>
                    <p>View payment information.</p>

                    <a href="{{ route('admin.payments.index') }}" class="btn btn-primary">
                        View Payments
                    </a>
                </div>
            </div>
        </div>

    </div>

@endsection