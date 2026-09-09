<?php

namespace App\Integrations\DummyJson;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class DummyJsonClient
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.dummyjson.base_url', 'https://dummyjson.com');
        $this->timeout = config('services.dummyjson.timeout', 10);
    }

    public function getProducts(int $limit = 30, int $skip = 0): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry(3, 200)
                ->get("{$this->baseUrl}/products", [
                    'limit' => $limit,
                    'skip' => $skip,
                ]);

            $response->throw();

            return $response->json();
        } catch (ConnectionException $e) {
            throw new \Exception('DummyJSON connection failed: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('DummyJSON request failed: ' . $e->getMessage());
        }
    }
}