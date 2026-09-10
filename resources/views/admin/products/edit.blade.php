@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Edit Product</h2>
            <p class="text-muted mb-0">
                Update product information
            </p>
        </div>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            Back to Products
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Product Name</label>

                    <input type="text" name="name" class="form-control" value="{{ $product->name }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">SKU</label>

                    <input type="text" name="sku" class="form-control" value="{{ $product->sku }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>

                    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>

                    <select name="category_id" class="form-select">

                        <option value="">
                            Select Category
                        </option>

                        @foreach($categories as $category)

                            <option value="{{ $category->id }}" @selected($product->category_id == $category->id)>
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>

                    <input type="number" name="price" step="0.01" class="form-control" value="{{ $product->price }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>

                    <select name="status" class="form-select">

                        <option value="active" @selected($product->status === 'active')>
                            Active
                        </option>

                        <option value="inactive" @selected($product->status === 'inactive')>
                            Inactive
                        </option>

                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Product
                </button>

            </form>

        </div>
    </div>

@endsection