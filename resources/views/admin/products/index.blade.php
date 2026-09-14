@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2>Products</h2>

                <p class="text-muted mb-0">
                    Manage your products
                </p>
            </div>

            <a href="{{ route('products.create') }}" class="btn btn-primary">
                + Add Product
            </a>

        </div>




        {{-- Products --}}
        @if($products->count() > 0)

            <div class="card shadow-sm">

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover mb-0">

                            <thead class="table-dark">

                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th width="120">Action</th>
                                </tr>

                            </thead>


                            <tbody>

                                @foreach($products as $product)

                                    <tr>

                                        {{-- ID --}}
                                        <td>
                                            {{ $product->id }}
                                        </td>


                                        {{-- Name --}}
                                        <td>
                                            {{ $product->name }}
                                        </td>


                                        {{-- SKU --}}
                                        <td>
                                            {{ $product->sku }}
                                        </td>


                                        {{-- Category --}}
                                        <td>
                                            {{ $product->category?->name ?? 'No Category' }}
                                        </td>


                                        {{-- Price --}}
                                        <td>
                                            ₹{{ number_format((float) $product->price, 2) }}
                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @if($product->status === 'active')

                                                <span class="badge bg-success">
                                                    Active
                                                </span>

                                            @else

                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($product->status) }}
                                                </span>

                                            @endif

                                        </td>


                                        {{-- Action --}}
                                        <td>

                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>



            {{-- Pagination --}}
            {{-- Pagination --}}
            @if($products->hasPages())

                <div class="mt-4 mb-4">

                    <nav aria-label="Products pagination">

                        <div class="d-flex justify-content-center align-items-center gap-3">

                            {{-- Previous --}}
                            @if($products->onFirstPage())

                                <button class="btn btn-outline-secondary" disabled>
                                    ← Previous
                                </button>

                            @else

                                <a href="{{ $products->previousPageUrl() }}" class="btn btn-outline-primary">
                                    ← Previous
                                </a>

                            @endif


                            {{-- Page Information --}}
                            <span class="text-muted">
                                Page
                                <strong>{{ $products->currentPage() }}</strong>
                                of
                                <strong>{{ $products->lastPage() }}</strong>
                            </span>


                            {{-- Next --}}
                            @if($products->hasMorePages())

                                <a href="{{ $products->nextPageUrl() }}" class="btn btn-primary">
                                    Next →
                                </a>

                            @else

                                <button class="btn btn-outline-secondary" disabled>
                                    Next →
                                </button>

                            @endif

                        </div>

                    </nav>

                </div>

            @endif

        @else

            {{-- No Products --}}
            <div class="alert alert-info">
                No products found.
            </div>

        @endif

    </div>

@endsection