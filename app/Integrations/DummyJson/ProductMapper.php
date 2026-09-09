<?php

namespace App\Integrations\DummyJson;

class ProductMapper
{
    /**
     * Convert one DummyJSON product
     * into our application's product format.
     */
    public function map(array $externalProduct): array
    {
        return [
            // Local SKU generated from DummyJSON ID
            'sku' => 'DJ-' . $externalProduct['id'],

            // DummyJSON title → our name
            'name' => $externalProduct['title'] ?? 'Unknown Product',

            // Product description
            'description' => $externalProduct['description'] ?? null,

            // Product price
            'price' => $externalProduct['price'] ?? 0,

            // Our local product status
            'status' => 'active',

            // Supplier information
            'source' => 'dummyjson',

            // DummyJSON's product ID
            'external_id' => $externalProduct['id'],

            // Keep the original DummyJSON response
            'external_data' => $externalProduct,

            // Used later to find/create our local category
            'category_name' => $externalProduct['category'] ?? 'Uncategorized',
        ];
    }

    /**
     * Convert multiple DummyJSON products.
     */
    public function mapMany(array $externalProducts): array
    {
        return array_map(
            fn($product) => $this->map($product),
            $externalProducts
        );
    }
}