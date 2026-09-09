<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class DummyJsonService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.dummyjson.base_url');
    }

    public function getProducts(): array
    {
        $response = Http::timeout(10)
            ->get($this->baseUrl . '/products');

        if ($response->failed()) {
            throw new RuntimeException(
                'DummyJSON API request failed.'
            );
        }

        return $response->json('products', []);
    }
}