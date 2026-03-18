<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return $this->success($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:categories',
            'discount'     => 'nullable|numeric|min:0|max:100',
            'has_discount' => 'nullable|boolean',
            'active'       => 'nullable|boolean',
        ]);

        $category = Category::create($validated);
        return $this->success($category, 'Categoría creada correctamente', 201);
    }

    public function show(Category $category)
    {
        return $this->success($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'         => 'sometimes|string|max:100|unique:categories,name,' . $category->id,
            'discount'     => 'nullable|numeric|min:0|max:100',
            'has_discount' => 'nullable|boolean',
            'active'       => 'nullable|boolean',
        ]);

        $category->update($validated);
        return $this->success($category, 'Categoría actualizada correctamente');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return $this->success(null, 'Categoría eliminada correctamente');
    }
}