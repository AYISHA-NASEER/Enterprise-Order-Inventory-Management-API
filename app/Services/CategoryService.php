<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Get all categories.
     */
    public function all()
    {
        return Category::orderBy('name')->get();
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Find a category.
     */
    public function find(int $id): Category
    {
        return Category::findOrFail($id);
    }

    /**
     * Update a category.
     */
    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);

        $category->update($data);

        return $category->fresh();
    }

    /**
     * Delete a category.
     */
    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);

        $category->delete();
    }
}