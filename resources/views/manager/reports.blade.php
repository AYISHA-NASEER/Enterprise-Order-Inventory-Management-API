@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1>Operational Reports</h1>

                <p class="text-muted mb-0">
                    Manager operational overview
                </p>
            </div>

            <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary">
                ← Dashboard
            </a>

        </div>


        <div class="row g-4">

            {{-- Total Orders --}}
            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5>Total Orders</h5>

                        <h2>
                            {{ $totalOrders }}
                        </h2>

                    </div>

                </div>

            </div>


            {{-- Pending Orders --}}
            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5>Pending Orders</h5>

                        <h2>
                            {{ $pendingOrders }}
                        </h2>

                    </div>

                </div>

            </div>


            {{-- Total Products --}}
            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5>Total Products</h5>

                        <h2>
                            {{ $totalProducts }}
                        </h2>

                    </div>

                </div>

            </div>


            {{-- Total Inventory --}}
            <div class="col-md-6">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5>Total Inventory Quantity</h5>

                        <h2>
                            {{ $totalInventory }}
                        </h2>

                    </div>

                </div>

            </div>


            {{-- Low Stock --}}
            <div class="col-md-6">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5>Low Stock Products</h5>

                        <h2>
                            {{ $lowStockProducts }}
                        </h2>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection