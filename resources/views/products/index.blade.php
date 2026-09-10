@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Products</h2>
            <p class="text-muted mb-0">Manage your products</p>
        </div>

        <a href="{{ route('products.create') }}" class="btn btn-primary">
            + Create Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            @if($products->count())

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($products as $product)

                                <tr>
                                    <td>{{ $product->id }}</td>

                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                    </td>

                                    <td>
                                        {{ $product->sku }}
                                    </td>

                                    <td>
                                        {{ $product->category?->name ?? 'N/A' }}
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
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>
                </div>

            @else

                <div class="text-center py-5">
                    <h5>No products found</h5>
                    <p class="text-muted">
                        Create your first product.
                    </p>

                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        Create Product
                    </a>
                </div>

            @endif

        </div>
    </div>

@endsection