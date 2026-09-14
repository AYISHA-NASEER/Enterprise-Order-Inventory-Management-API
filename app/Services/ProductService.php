<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Create a product.
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {

            $product = Product::create($data);

            Inventory::create([
                'product_id' => $product->id,
                'quantity' => 0,
            ]);

            $this->clearProductListCache();

            return $product;
        });
    }

    /**
     * Find a product.
     */
    public function find(int $id): Product
    {
        return Product::with([
            'category',
            'inventory',
        ])->findOrFail($id);
    }

    /**
     * Update a product.
     */
    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);

        $product->update($data);

        $this->clearProductListCache();

        return $product->fresh([
            'category',
            'inventory',
        ]);
    }

    /**
     * Delete a product.
     */
    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        $product->delete();

        $this->clearProductListCache();
    }

    /**
     * Get products with search, category, status,
     * sorting and pagination.
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        /*
        |--------------------------------------------------------------------------
        | Build the product query
        |--------------------------------------------------------------------------
        */

        $query = Product::with([
            'category',
            'inventory',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {

            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )->orWhere(
                        'sku',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Category filter
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['category'])) {

            $query->where(
                'category_id',
                $filters['category']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['status'])) {

            $query->where(
                'status',
                $filters['status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sortBy = $filters['sort_by'] ?? 'created_at';

        $sortDir = $filters['sort_dir'] ?? 'desc';

        $allowedSorts = [
            'id',
            'name',
            'sku',
            'price',
            'created_at',
            'updated_at',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        $sortDir = $sortDir === 'asc'
            ? 'asc'
            : 'desc';

        $query->orderBy(
            $sortBy,
            $sortDir
        );

        /*
        |--------------------------------------------------------------------------
        | Items per page
        |--------------------------------------------------------------------------
        */

        $perPage = min(
            max(
                (int) ($filters['per_page'] ?? 15),
                1
            ),
            50
        );

        /*
        |--------------------------------------------------------------------------
        | Page
        |--------------------------------------------------------------------------
        */

        $page = max(
            (int) ($filters['page'] ?? 1),
            1
        );

        /*
        |--------------------------------------------------------------------------
        | Database pagination
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | We do NOT cache the paginator.
        | We do NOT cache an Eloquent Collection.
        |
        | MySQL returns only the products needed for this page.
        |
        */

        return $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );
    }

    /**
     * Clear product list cache.
     *
     * Kept here because other parts of the application
     * may use the products:* cache namespace.
     */
    private function clearProductListCache(): void
    {
        $redis = Cache::getRedis();

        $keys = $redis->keys('products:*');

        if (!empty($keys)) {
            $redis->del($keys);
        }
    }
}