<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Branch;

class StockService
{
    public function getStockByBarcode(string $barcode): ?array
    {
        $product = Product::where('barcode', $barcode)
            ->where('active', true)
            ->first();

        if (!$product) return null;

        $stocks = ProductStock::where('product_id', $product->id)
            ->with('branch')
            ->get();

        return [
            'product' => [
                'id'           => $product->id,
                'barcode'      => $product->barcode,
                'name'         => $product->name,
                'price_retail' => $product->price_retail,
            ],
            'stock' => $stocks->map(function ($stock) {
                return [
                    'branch_id'   => $stock->branch_id,
                    'branch_name' => $stock->branch->name ?? 'Desconocida',
                    'quantity'    => $stock->quantity,
                ];
            }),
            'total_quantity' => $stocks->sum('quantity'),
        ];
    }

    public function getStockByBranch(int $branchId, int $page = 1, int $perPage = 100): array
    {
        $branch = Branch::find($branchId);

        $paginated = ProductStock::where('branch_id', $branchId)
            ->with('product')
            ->orderBy('product_id')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'branch_id'    => $branchId,
            'branch_name'  => $branch->name ?? 'Desconocida',
            'data'         => $paginated->map(function ($stock) {
                return [
                    'product_id'   => $stock->product_id,
                    'barcode'      => $stock->product->barcode ?? null,
                    'name'         => $stock->product->name ?? null,
                    'quantity'     => $stock->quantity,
                    'price_retail' => $stock->product->price_retail ?? 0,
                ];
            }),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function addStock(int $productId, int $branchId, float $quantity): array
    {
        ProductStock::addStock($productId, $branchId, $quantity);

        $stock = ProductStock::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->first();

        return [
            'product_id' => $productId,
            'branch_id'  => $branchId,
            'quantity'   => $stock->quantity,
        ];
    }

    public function subtractStock(int $productId, int $branchId, float $quantity): array
    {
        ProductStock::subtractStock($productId, $branchId, $quantity);

        $stock = ProductStock::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->first();

        return [
            'product_id' => $productId,
            'branch_id'  => $branchId,
            'quantity'   => $stock->quantity,
        ];
    }
}