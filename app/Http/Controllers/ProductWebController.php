<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductWebController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
    }

    public function index(): View
    {
        $products = Product::with('category')
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $this->productService->create(
            $request->validated()
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ): RedirectResponse {
        $this->authorize('update', $product);

        $this->productService->update(
            $product->id,
            $request->validated()
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }
}