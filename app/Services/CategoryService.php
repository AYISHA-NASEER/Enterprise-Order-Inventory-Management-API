<?php

namespace App\Services;
use App\Models\Category;
class CategoryService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function create(array $data): Category
    {
        return Category::create($data);
    }
}