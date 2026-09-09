<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
    }

    /**
     * List products with:
     * - search
     * - category filter
     * - status filter
     * - sorting
     * - pagination
     * - Redis caching
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->list([
            'search' => $request->input('search'),
            'category' => $request->input('category'),
            'status' => $request->input('status'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_dir' => $request->input('sort_dir', 'desc'),
            'per_page' => min(
                max((int) $request->input('per_page', 15), 1),
                50
            ),
            'page' => max((int) $request->input('page', 1), 1),
        ]);

        return ProductResource::collection($products);
    }

    /**
     * Get single product.
     */
    public function show(int $id)
    {
        $product = $this->productService->find($id);

        $this->authorize('view', $product);

        return new ProductResource(
            $product->load(['category', 'inventory'])
        );
    }

    /**
     * Create product.
     */
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $product = $this->productService->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Product created successfully',
            'data' => new ProductResource(
                $product->load(['category', 'inventory'])
            ),
        ], 201);
    }

    /**
     * Update product.
     */
    public function update(
        UpdateProductRequest $request,
        int $id
    ) {
        $product = $this->productService->find($id);

        $this->authorize('update', $product);

        $product = $this->productService->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new ProductResource(
                $product->load(['category', 'inventory'])
            ),
        ]);
    }

    /**
     * Delete product.
     */
    public function destroy(int $id)
    {
        $product = $this->productService->find($id);

        $this->authorize('delete', $product);

        $this->productService->delete($id);

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}