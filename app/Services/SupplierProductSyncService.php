<?php

namespace App\Services;

use App\Integrations\DummyJson\DummyJsonClient;
use App\Integrations\DummyJson\ProductMapper;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Str;

class SupplierProductSyncService
{
    public function __construct(
        private DummyJsonClient $client,
        private ProductMapper $mapper,
    ) {
    }

    /**
     * Synchronize products from DummyJSON.
     */
    public function sync(): int
    {
        $response = $this->client->getProducts();

        $products = $response['products'] ?? [];

        $mappedProducts = $this->mapper->mapMany($products);

        $count = 0;

        foreach ($mappedProducts as $mappedProduct) {

            /*
             * Find or create the local category.
             */
            $category = Category::firstOrCreate(
                [
                    'slug' => Str::slug($mappedProduct['category_name']),
                ],
                [
                    'name' => $mappedProduct['category_name'],
                ]
            );

            /*
             * Remove temporary mapping field.
             *
             * category_name is not a column
             * in the products table.
             */
            unset($mappedProduct['category_name']);

            /*
             * Add our local category ID.
             */
            $mappedProduct['category_id'] = $category->id;

            /*
             * Create the product if it doesn't exist.
             * Update it if it already exists.
             */
            $product = Product::updateOrCreate(
                [
                    'source' => 'dummyjson',
                    'external_id' => $mappedProduct['external_id'],
                ],
                $mappedProduct
            );

            /*
             * Make sure every synced product
             * has an inventory record.
             *
             * If inventory already exists,
             * do NOT create another one.
             */
            Inventory::firstOrCreate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => 0,
                ]
            );

            $count++;
        }

        return $count;
    }
}