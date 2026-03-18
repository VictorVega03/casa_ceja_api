<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

   public function index(Request $request)
{
    $query = Product::with(['category', 'unit']);

    if ($search = $request->query('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('barcode', 'LIKE', "%{$search}%");
        });
    }

    if ($categoryId = $request->query('category_id')) {
        $query->where('category_id', $categoryId);
    }

    if ($request->has('active')) {
        $query->where('active', $request->boolean('active'));
    }

    $perPage = min((int) $request->query('per_page', 50), 200);

    return $this->success($query->orderBy('name')->paginate($perPage));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode'            => 'nullable|string|max:50|unique:products',
            'name'               => 'required|string|max:200',
            'category_id'        => 'nullable|integer|exists:categories,id',
            'unit_id'            => 'nullable|integer|exists:units,id',
            'presentation'       => 'nullable|string|max:100',
            'iva'                => 'nullable|numeric|min:0',
            'price_retail'       => 'nullable|numeric|min:0',
            'price_wholesale'    => 'nullable|numeric|min:0',
            'wholesale_quantity' => 'nullable|integer|min:0',
            'price_special'      => 'nullable|numeric|min:0',
            'price_dealer'       => 'nullable|numeric|min:0',
            'active'             => 'nullable|boolean',
        ]);

        $product = Product::create($validated);
        return $this->success($product, 'Producto creado correctamente', 201);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit']);
        return $this->success($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'barcode'            => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'name'               => 'sometimes|string|max:200',
            'category_id'        => 'nullable|integer|exists:categories,id',
            'unit_id'            => 'nullable|integer|exists:units,id',
            'presentation'       => 'nullable|string|max:100',
            'iva'                => 'nullable|numeric|min:0',
            'price_retail'       => 'nullable|numeric|min:0',
            'price_wholesale'    => 'nullable|numeric|min:0',
            'wholesale_quantity' => 'nullable|integer|min:0',
            'price_special'      => 'nullable|numeric|min:0',
            'price_dealer'       => 'nullable|numeric|min:0',
            'active'             => 'nullable|boolean',
        ]);

        $product->update($validated);
        return $this->success($product, 'Producto actualizado correctamente');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return $this->success(null, 'Producto eliminado correctamente');
    }
}