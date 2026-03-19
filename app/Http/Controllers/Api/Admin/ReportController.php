<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\CashClose;
use App\Models\Credit;
use App\Models\Layaway;
use App\Models\StockEntry;
use App\Models\StockOutput;
use App\Models\ProductStock;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponse;

    public function sales(Request $request)
    {
        $query = Sale::query();

        if ($branchId = $request->query('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        if ($from = $request->query('from')) {
            $query->where('sale_date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->where('sale_date', '<=', $to);
        }

        $perPage = min((int) $request->query('per_page', 50), 200);
        $page    = (int) $request->query('page', 1);

        $results = $query->orderBy('sale_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'data'         => $results->items(),
            'count'        => $results->count(),
            'total'        => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
        ]);
    }

    public function cashCloses(Request $request)
    {
        $query = CashClose::query();

        if ($branchId = $request->query('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        if ($from = $request->query('from')) {
            $query->where('close_date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->where('close_date', '<=', $to);
        }

        $perPage = min((int) $request->query('per_page', 50), 200);
        $page    = (int) $request->query('page', 1);

        $results = $query->orderBy('close_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'data'         => $results->items(),
            'count'        => $results->count(),
            'total'        => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
        ]);
    }

    public function credits(Request $request)
    {
        $query = Credit::with(['customer', 'branch']);

        if ($branchId = $request->query('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->query('from')) {
            $query->where('credit_date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->where('credit_date', '<=', $to);
        }

        $perPage = min((int) $request->query('per_page', 50), 200);
        $page    = (int) $request->query('page', 1);

        $results = $query->orderBy('credit_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'data'         => $results->items(),
            'count'        => $results->count(),
            'total'        => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
        ]);
    }

    public function layaways(Request $request)
    {
        $query = Layaway::with(['customer', 'branch']);

        if ($branchId = $request->query('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->query('from')) {
            $query->where('layaway_date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->where('layaway_date', '<=', $to);
        }

        $perPage = min((int) $request->query('per_page', 50), 200);
        $page    = (int) $request->query('page', 1);

        $results = $query->orderBy('layaway_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'data'         => $results->items(),
            'count'        => $results->count(),
            'total'        => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
        ]);
    }

    public function inventory(Request $request)
    {
        $type     = $request->query('type', 'entries');
        $branchId = $request->query('branch_id');
        $from     = $request->query('from');
        $to       = $request->query('to');
        $perPage  = min((int) $request->query('per_page', 50), 200);
        $page     = (int) $request->query('page', 1);

        if ($type === 'entries') {
            $query = StockEntry::with(['branch', 'supplier']);

            if ($branchId) $query->where('branch_id', $branchId);
            if ($from) $query->where('entry_date', '>=', $from);
            if ($to) $query->where('entry_date', '<=', $to);

            $results = $query->orderBy('entry_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $query = StockOutput::with(['originBranch', 'destinationBranch']);

            if ($branchId) $query->where('origin_branch_id', $branchId);
            if ($from) $query->where('output_date', '>=', $from);
            if ($to) $query->where('output_date', '<=', $to);

            $results = $query->orderBy('output_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return $this->success([
            'data'         => $results->items(),
            'count'        => $results->count(),
            'total'        => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
        ]);
    }

    public function productStock(Request $request)
    {
        $query = ProductStock::with(['product', 'branch']);

        if ($branchId = $request->query('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        if ($productId = $request->query('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($search = $request->query('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        }

        $perPage = min((int) $request->query('per_page', 100), 500);
        $page    = (int) $request->query('page', 1);

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'data'         => $results->items(),
            'count'        => $results->count(),
            'total'        => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
        ]);
    }
}