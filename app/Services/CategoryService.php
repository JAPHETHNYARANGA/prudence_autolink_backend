<?php

namespace App\Services;

use App\Models\category;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        // Validate and sanitize input data here if necessary

        // Create and return the new category
        return category::create([
            'name' => $data['name'],
        ]);
    }

    public function getAllCarCategories()
    {
        return category::all(); // Retrieves all car categories from the database
    }
}
