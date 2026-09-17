@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1>Manager Inventory</h1>

                <p class="text-muted mb-0">
                    View product stock levels
                </p>
            </div>

            <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary">
                ← Dashboard
            </a>

        </div>


        @if($inventories->count())

            <div class="card shadow-sm">

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead class="table-dark">

                                <tr>
                                    <th>Inventory ID</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($inventories as $inventory)

                                    <tr>

                                        <td>
                                            #{{ $inventory->id }}
                                        </td>

                                        <td>
                                            {{ $inventory->product?->name ?? 'Unknown Product' }}
                                        </td>

                                        <td>
                                            {{ $inventory->product?->sku ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $inventory->quantity }}
                                        </td>

                                        <td>

                                            @if($inventory->quantity <= 10)

                                                <span class="badge bg-danger">
                                                    Low Stock
                                                </span>

                                            @else

                                                <span class="badge bg-success">
                                                    In Stock
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- Pagination --}}

            @if($inventories->hasPages())

                <div class="mt-4">

                    <div class="d-flex justify-content-center align-items-center gap-3">

                        @if($inventories->onFirstPage())

                            <button class="btn btn-outline-secondary" disabled>
                                Previous
                            </button>

                        @else

                            <a href="{{ $inventories->previousPageUrl() }}" class="btn btn-outline-primary">
                                Previous
                            </a>

                        @endif


                        <span>
                            Page {{ $inventories->currentPage() }}
                            of {{ $inventories->lastPage() }}
                        </span>


                        @if($inventories->hasMorePages())

                            <a href="{{ $inventories->nextPageUrl() }}" class="btn btn-outline-primary">
                                Next
                            </a>

                        @else

                            <button class="btn btn-outline-secondary" disabled>
                                Next
                            </button>

                        @endif

                    </div>

                </div>

            @endif

        @else

            <div class="alert alert-info">
                No inventory records found.
            </div>

        @endif

    </div>

@endsection