@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="fw-bold">📦 Warehouse Inventory</h2>

                <p class="text-muted mb-0">
                    Manage product stock.
                </p>
            </div>

            <a href="{{ route('warehouse.dashboard') }}" class="btn btn-secondary">
                ← Dashboard
            </a>

        </div>


        {{-- Success --}}
        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        {{-- Error --}}
        @if(session('error'))

            <div class="alert alert-danger">
                {{ session('error') }}
            </div>

        @endif


        {{-- Validation Errors --}}
        @if($errors->any())

            <div class="alert alert-danger">

                <strong>Please fix the following:</strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">

                <h5 class="mb-0">
                    📦 Product Inventory
                </h5>

            </div>


            <div class="card-body">

                @if($inventory->isEmpty())

                    <div class="alert alert-info mb-0">
                        No inventory records found.
                    </div>

                @else

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Current Stock</th>
                                    <th>Add Stock</th>
                                    <th>Update Stock</th>
                                </tr>

                            </thead>


                            <tbody>

                                @foreach($inventory as $item)

                                                    <tr>

                                                        {{-- ID --}}
                                                        <td>
                                                            {{ $item->id }}
                                                        </td>


                                                        {{-- Product --}}
                                                        <td>
                                                            <strong>
                                                                {{ $item->product->name ?? 'Unknown Product' }}
                                                            </strong>
                                                        </td>


                                                        {{-- SKU --}}
                                                        <td>
                                                            {{ $item->product->sku ?? '-' }}
                                                        </td>


                                                        {{-- Category --}}
                                                        <td>
                                                            {{ $item->product->category->name ?? '-' }}
                                                        </td>


                                                        {{-- Price --}}
                                                        <td>
                                                            ₹{{ number_format(
                                        (float) ($item->product->price ?? 0),
                                        2
                                    ) }}
                                                        </td>


                                                        {{-- Current Stock --}}
                                                        <td>

                                                            @if($item->quantity > 0)

                                                                <span class="badge bg-success fs-6">
                                                                    {{ $item->quantity }} units
                                                                </span>

                                                            @else

                                                                <span class="badge bg-danger fs-6">
                                                                    Out of Stock
                                                                </span>

                                                            @endif

                                                        </td>


                                                        {{-- Add Stock --}}
                                                        <td>

                                                            <form action="{{ route(
                                        'warehouse.inventory.add-stock',
                                        $item->id
                                    ) }}" method="POST">

                                                                @csrf

                                                                <div class="input-group">

                                                                    <input type="number" name="quantity" class="form-control" min="1"
                                                                        placeholder="Qty" required>

                                                                    <button type="submit" class="btn btn-success">
                                                                        ➕ Add
                                                                    </button>

                                                                </div>

                                                            </form>

                                                        </td>


                                                        {{-- Update Stock --}}
                                                        <td>

                                                            <form action="{{ route(
                                        'warehouse.inventory.update-stock',
                                        $item->id
                                    ) }}" method="POST">

                                                                @csrf
                                                                @method('PUT')

                                                                <div class="input-group">

                                                                    <input type="number" name="quantity" class="form-control" min="0"
                                                                        value="{{ $item->quantity }}" required>

                                                                    <button type="submit" class="btn btn-warning">
                                                                        ✏️ Update
                                                                    </button>

                                                                </div>

                                                            </form>

                                                        </td>

                                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection