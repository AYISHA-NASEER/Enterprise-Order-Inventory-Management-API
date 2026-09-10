@extends('layouts.app')

@section('content')

    <div class="container mt-4">

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


        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif


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

                                        <td>
                                            {{ $product->id }}
                                        </td>

                                        <td>
                                            {{ $product->name }}
                                        </td>

                                        <td>
                                            {{ $product->sku }}
                                        </td>

                                        <td>
                                            {{ $product->category?->name ?? 'No Category' }}
                                        </td>

                                        <td>
                                            ₹{{ number_format($product->price, 2) }}
                                        </td>

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

        @else

            <div class="alert alert-info">
                No products found.
            </div>

        @endif

    </div>

@endsection