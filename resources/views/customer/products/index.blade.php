@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>🛍 Browse Products</h2>

            <p class="text-muted mb-0">
                Browse our available products.
            </p>
        </div>

        <a href="{{ route('customer.cart') }}" class="btn btn-success">
            🛒 My Cart
        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Error Message --}}
    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    @if($products->count() > 0)

        <div class="row g-4">

            @foreach($products as $product)

                <div class="col-md-6 col-lg-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body d-flex flex-column">

                            {{-- Product Name --}}
                            <h5 class="card-title">
                                {{ $product->name }}
                            </h5>


                            {{-- SKU --}}
                            <p class="text-muted mb-2">
                                SKU: {{ $product->sku }}
                            </p>


                            {{-- Category --}}
                            @if($product->category)

                                <div>
                                    <span class="badge bg-secondary mb-3">
                                        {{ $product->category->name }}
                                    </span>
                                </div>

                            @endif


                            {{-- Description --}}
                            @if($product->description)

                                <p class="card-text">
                                    {{ $product->description }}
                                </p>

                            @endif


                            {{-- Price --}}
                            <h4 class="text-success mb-3">
                                ₹{{ number_format((float) $product->price, 2) }}
                            </h4>


                            {{-- Inventory --}}
                            @if($product->inventory)

                                @if($product->inventory->quantity > 0)

                                    {{-- In Stock --}}
                                    <div class="mb-3">

                                        <span class="badge bg-success">
                                            ✅ In Stock
                                        </span>

                                        <small class="text-muted ms-2">
                                            {{ $product->inventory->quantity }} available
                                        </small>

                                    </div>


                                    {{-- Add To Cart --}}
                                    <form action="{{ route('customer.cart.add', $product->id) }}" method="POST" class="mt-auto">

                                        @csrf

                                        <div class="input-group mb-2">

                                            <label class="input-group-text">
                                                Qty
                                            </label>

                                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->inventory->quantity }}"
                                                class="form-control" required>

                                        </div>


                                        <button type="submit" class="btn btn-primary w-100">
                                            🛒 Add to Cart
                                        </button>

                                    </form>


                                @else

                                    {{-- Out Of Stock --}}
                                    <div class="mb-3">

                                        <span class="badge bg-danger">
                                            ❌ Out of Stock
                                        </span>

                                    </div>


                                    {{-- Disabled Button --}}
                                    <button type="button" class="btn btn-secondary w-100 mt-auto" disabled>
                                        ❌ Out of Stock
                                    </button>

                                @endif


                            @else

                                {{-- Inventory Record Missing --}}
                                <div class="mb-3">

                                    <span class="badge bg-danger">
                                        ❌ Out of Stock
                                    </span>

                                </div>


                                <button type="button" class="btn btn-secondary w-100 mt-auto" disabled>
                                    ❌ Out of Stock
                                </button>

                            @endif

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


    @else

        <div class="alert alert-info">
            No active products available.
        </div>

    @endif

@endsection