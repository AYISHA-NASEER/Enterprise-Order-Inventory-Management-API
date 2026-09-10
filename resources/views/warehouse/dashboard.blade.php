@extends('layouts.app')

@section('content')

    <div class="container py-4">

        {{-- Header --}}
        <div class="mb-4">

            <h2 class="fw-bold">
                📦 Warehouse Staff Dashboard
            </h2>

            <p class="text-muted">
                Welcome, {{ auth()->user()->name }}.
                Manage fulfillment and inventory from here.
            </p>

        </div>


        {{-- Dashboard Cards --}}
        <div class="row g-4">

            {{-- Paid Orders --}}
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="fs-1 mb-3">📋</div>
                        <h4 class="card-title">Paid Orders</h4>
                        <p class="text-muted">
                            View paid orders that are waiting
                            for fulfillment.
                        </p>
                        <a href="{{ route('warehouse.orders') }}" class="btn btn-primary">
                            View Orders
                        </a>
                    </div>
                </div>
            </div>


            {{-- Fulfillment --}}
            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body text-center p-4">

                        <div class="fs-1 mb-3">
                            🚚
                        </div>

                        <h4 class="card-title">
                            Fulfillment
                        </h4>

                        <p class="text-muted">
                            Update shipment and fulfillment
                            status of orders.
                        </p>

                        <a href="{{ route('warehouse.shipments') }}" class="btn btn-success">
                            Manage Shipments
                        </a>

                    </div>

                </div>

            </div>


            {{-- Inventory --}}
            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body text-center p-4">

                        <div class="fs-1 mb-3">
                            📦
                        </div>

                        <h4 class="card-title">
                            Inventory
                        </h4>

                        <p class="text-muted">
                            View current stock and manage
                            product inventory.
                        </p>

                        <a href="{{ route('warehouse.inventory') }}" class="btn btn-warning">
                            View Inventory
                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- Quick Information --}}
        <div class="card shadow-sm mt-4">

            <div class="card-body">

                <h5 class="mb-3">
                    🏭 Warehouse Responsibilities
                </h5>

                <div class="row">

                    <div class="col-md-4">
                        <strong>📋 Orders</strong>
                        <p class="text-muted">
                            View paid orders requiring fulfillment.
                        </p>
                    </div>

                    <div class="col-md-4">
                        <strong>🚚 Shipments</strong>
                        <p class="text-muted">
                            Update shipment status.
                        </p>
                    </div>

                    <div class="col-md-4">
                        <strong>📦 Stock</strong>
                        <p class="text-muted">
                            Manage available inventory.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection