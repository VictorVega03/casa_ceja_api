<?php

namespace App\Http\Controllers\Api\OnDemand;

use App\Http\Controllers\Controller;
use App\Services\StockService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    use ApiResponse;

    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function byProduct(string $barcode)
    {
        $data = $this->stockService->getStockByBarcode($barcode);

        if (!$data) {
            return $this->error('Producto no encontrado', 404);
        }

        return $this->success($data);
    }

    public function byBranch(Request $request, int $branchId)
    {
        $page    = (int) $request->query('page', 1);
        $perPage = min((int) $request->query('per_page', 100), 500);

        $data = $this->stockService->getStockByBranch($branchId, $page, $perPage);

        return $this->success($data);
    }

    public function addStock(Request $request, int $branchId, int $productId)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $data = $this->stockService->addStock($productId, $branchId, $request->quantity);

        return $this->success($data, 'Stock actualizado correctamente');
    }

    public function subtractStock(Request $request, int $branchId, int $productId)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $data = $this->stockService->subtractStock($productId, $branchId, $request->quantity);

        return $this->success($data, 'Stock actualizado correctamente');
    }
}