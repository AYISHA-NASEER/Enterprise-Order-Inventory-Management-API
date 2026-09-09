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
     * Find one product.
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

        // Product list cache is now outdated.
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

        // Product list cache is now outdated.
        $this->clearProductListCache();
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
    public function list(array $filters = []): LengthAwarePaginator
    {
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
        | Cache Key
        |--------------------------------------------------------------------------
        |
        | Every different combination gets a different cache entry.
        |
        | Example:
        |
        | products:abc123
        | products:def456
        |
        */

        $cacheKey = 'products:' . md5(
            json_encode($filters)
        );

        /*
        |--------------------------------------------------------------------------
        | Redis Cache
        |--------------------------------------------------------------------------
        |
        | If data already exists in Redis:
        |
        |     Redis → return data
        |
        | Otherwise:
        |
        |     MySQL → Redis → return data
        |
        */

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($filters, $page) {

                /*
                |--------------------------------------------------------------------------
                | Base Query
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
                |
                | Search product name OR SKU.
                |
                */

                if (!empty($filters['search'])) {

                    $search = $filters['search'];

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
                | Category Filter
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
                | Status Filter
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

                /*
                | Only allow these columns.
                | This prevents users from passing arbitrary
                | column names.
                */

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
                | Pagination
                |--------------------------------------------------------------------------
                */

                $perPage = min(
                    max(
                        (int) ($filters['per_page'] ?? 15),
                        1
                    ),
                    50
                );

                return $query->paginate(
                    $perPage,
                    ['*'],
                    'page',
                    $page
                );
            }
        );
    }

    /**
     * Clear product list cache.
     */
    private function clearProductListCache(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Remove all product list cache entries.
        |--------------------------------------------------------------------------
        */

        $redis = Cache::getRedis();

        $keys = $redis->keys('products:*');

        if (!empty($keys)) {
            $redis->del($keys);
        }
    }
}