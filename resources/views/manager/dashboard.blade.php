@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="mb-4">
            <h1>Manager Dashboard</h1>

            <p class="text-muted">
                Welcome, {{ auth()->user()->name }}
            </p>
        </div>


        <div class="row g-4">

            {{-- Orders --}}
            <div class="col-md-6">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h4 class="card-title">
                            📦 Orders
                        </h4>

                        <p class="card-text">
                            View all customer orders and their current status.
                        </p>

                        <a href="{{ route('manager.orders') }}" class="btn btn-primary">

                            View Orders

                        </a>

                    </div>

                </div>

            </div>


            {{-- Inventory --}}
            <div class="col-md-6">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h4 class="card-title">
                            📊 Inventory
                        </h4>

                        <p class="card-text">
                            View product inventory and stock quantities.
                        </p>

                        <a href="{{ route('manager.inventory') }}" class="btn btn-primary">

                            View Inventory

                        </a>

                    </div>

                </div>

            </div>


            {{-- Reports --}}
            <div class="col-md-6">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h4 class="card-title">
                            📈 Operational Reports
                        </h4>

                        <p class="card-text">
                            View operational reports and business information.
                        </p>

                        <a href="{{ route('manager.reports') }}" class="btn btn-primary">

                            View Reports

                        </a>

                    </div>

                </div>

            </div>


            {{-- Supplier Sync --}}
            <div class="col-md-6">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h4 class="card-title">
                            🔄 Supplier Sync
                        </h4>

                        <p class="card-text">
                            View supplier synchronization status.
                        </p>

                        <a href="{{ route('manager.supplier-sync') }}" class="btn btn-primary">

                            View Sync Status

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection