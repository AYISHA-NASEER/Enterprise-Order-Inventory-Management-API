@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2>Inventory</h2>

                <p class="text-muted mb-0">
                    View and manage product inventory
                </p>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark">
                ← Dashboard
            </a>

        </div>


        {{-- Inventory Table --}}
        <div class="card shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-dark">

                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th>Updated</th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse($inventories as $inventory)

                                <tr>

                                    {{-- Inventory ID --}}
                                    <td>
                                        {{ $inventory->id }}
                                    </td>


                                    {{-- Product --}}
                                    <td>

                                        @if($inventory->product)

                                            <strong>
                                                {{ $inventory->product->name }}
                                            </strong>

                                        @else

                                            <span class="text-muted">
                                                Product not found
                                            </span>

                                        @endif

                                    </td>


                                    {{-- SKU --}}
                                    <td>

                                        @if($inventory->product)

                                            {{ $inventory->product->sku }}

                                        @else

                                            —

                                        @endif

                                    </td>


                                    {{-- Quantity --}}
                                    <td>

                                        @if($inventory->quantity > 10)

                                            <span class="badge bg-success fs-6">
                                                {{ $inventory->quantity }}
                                            </span>

                                        @elseif($inventory->quantity > 0)

                                            <span class="badge bg-warning text-dark fs-6">
                                                {{ $inventory->quantity }}
                                            </span>

                                        @else

                                            <span class="badge bg-danger fs-6">
                                                0
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Source --}}
                                    <td>
                                        {{ $inventory->source ?? 'local' }}
                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if($inventory->quantity > 0)

                                            <span class="badge bg-success">
                                                In Stock
                                            </span>

                                        @else

                                            <span class="badge bg-danger">
                                                Out of Stock
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Updated --}}
                                    <td>

                                        @if($inventory->updated_at)

                                            {{ $inventory->updated_at->format('d M Y, h:i A') }}

                                        @else

                                            —

                                        @endif

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="7" class="text-center py-4">

                                        <span class="text-muted">
                                            No inventory found.
                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- Pagination --}}
        @if($inventories->hasPages())

            <div class="mt-4">

                {{-- Previous / Page Numbers / Next --}}
                <nav aria-label="Inventory pagination">

                    <ul class="pagination justify-content-center mb-2">

                        {{-- Previous --}}
                        @if($inventories->onFirstPage())

                            <li class="page-item disabled">

                                <span class="page-link">
                                    « Previous
                                </span>

                            </li>

                        @else

                            <li class="page-item">

                                <a class="page-link" href="{{ $inventories->previousPageUrl() }}">
                                    « Previous
                                </a>

                            </li>

                        @endif


                        {{-- Page Numbers --}}
                        @foreach($inventories->getUrlRange(1, $inventories->lastPage()) as $page => $url)

                            @if($page == $inventories->currentPage())

                                <li class="page-item active">

                                    <span class="page-link">
                                        {{ $page }}
                                    </span>

                                </li>

                            @else

                                <li class="page-item">

                                    <a class="page-link" href="{{ $url }}">
                                        {{ $page }}
                                    </a>

                                </li>

                            @endif

                        @endforeach


                        {{-- Next --}}
                        @if($inventories->hasMorePages())

                            <li class="page-item">

                                <a class="page-link" href="{{ $inventories->nextPageUrl() }}">
                                    Next »
                                </a>

                            </li>

                        @else

                            <li class="page-item disabled">

                                <span class="page-link">
                                    Next »
                                </span>

                            </li>

                        @endif

                    </ul>

                </nav>


                {{-- Result Information --}}
                <div class="text-center text-muted">

                    Showing
                    {{ $inventories->firstItem() ?? 0 }}
                    -
                    {{ $inventories->lastItem() ?? 0 }}
                    of
                    {{ $inventories->total() }}
                    inventory records

                </div>

            </div>

        @endif

    </div>

@endsection