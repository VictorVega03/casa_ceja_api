<?php

namespace App\Http\Controllers\Api\Sync;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SyncLog;
use App\Services\SyncPullService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PullController extends Controller
{
    use ApiResponse;

    private SyncPullService $pullService;

    public function __construct(SyncPullService $pullService)
    {
        $this->pullService = $pullService;
    }

    public function products(Request $request)
    {
        return $this->handlePull($request, 'products', function ($since, $page) {
            return $this->pullService->pullProducts($since, $page);
        });
    }

    public function categories(Request $request)
    {
        return $this->handlePull($request, 'categories', function ($since, $page) {
            return $this->pullService->pull(Category::class, $since, $page);
        });
    }

    public function units(Request $request)
    {
        return $this->handlePull($request, 'units', function ($since, $page) {
            return $this->pullService->pull(Unit::class, $since, $page);
        });
    }

    public function branches(Request $request)
    {
        return $this->handlePull($request, 'branches', function ($since, $page) {
            return $this->pullService->pull(Branch::class, $since, $page);
        });
    }

    public function users(Request $request)
    {
        return $this->handlePull($request, 'users', function ($since, $page) {
            return $this->pullService->pullUsers($since, $page);
        });
    }

    public function customers(Request $request)
    {
        return $this->handlePull($request, 'customers', function ($since, $page) {
            return $this->pullService->pull(Customer::class, $since, $page);
        });
    }

    public function suppliers(Request $request)
    {
        return $this->handlePull($request, 'suppliers', function ($since, $page) {
            return $this->pullService->pull(Supplier::class, $since, $page);
        });
    }

    public function stockEntries(Request $request)
    {
        $branchId = $request->input('branch_id');
        return $this->handlePull($request, 'stock_entries', function ($since, $page) use ($branchId) {
            return $this->pullService->pullStockEntries($branchId, $since, $page);
        });
    }

    public function stockOutputs(Request $request)
    {
        $branchId = $request->input('branch_id');
        return $this->handlePull($request, 'stock_outputs', function ($since, $page) use ($branchId) {
            return $this->pullService->pullStockOutputs($branchId, $since, $page);
        });
    }

    public function cashCloses(Request $request)
    {
        $branchId = $request->input('branch_id');
        return $this->handlePull($request, 'cash_closes', function ($since, $page) use ($branchId) {
            return $this->pullService->pullCashCloses($branchId, $since, $page);
        });
    }

    public function sales(Request $request)
    {
        $branchId = $request->input('branch_id');
        return $this->handlePull($request, 'sales', function ($since, $page) use ($branchId) {
            return $this->pullService->pullSales($branchId, $since, $page);
        });
    }

    public function credits(Request $request)
    {
        $branchId = $request->input('branch_id');
        return $this->handlePull($request, 'credits', function ($since, $page) use ($branchId) {
            return $this->pullService->pullCredits($branchId, $since, $page);
        });
    }

    public function creditPayments(Request $request)
    {
        $branchId = $request->input('branch_id');
        return $this->handlePull($request, 'credit_payments', function ($since, $page) use ($branchId) {
            return $this->pullService->pullCreditPayments($branchId, $since, $page);
        });
    }

    public function layaways(Request $request)
    {
        $branchId = $request->input('branch_id');
        return $this->handlePull($request, 'layaways', function ($since, $page) use ($branchId) {
            return $this->pullService->pullLayaways($branchId, $since, $page);
        });
    }

    public function layawayPayments(Request $request)
    {
        $branchId = $request->input('branch_id');
        return $this->handlePull($request, 'layaway_payments', function ($since, $page) use ($branchId) {
            return $this->pullService->pullLayawayPayments($branchId, $since, $page);
        });
    }

    private function handlePull(Request $request, string $entity, callable $fetcher)
    {
        $startTime = microtime(true);
        $authUser  = $request->input('auth_user');
        $branchId  = $authUser?->branch_id ?? 0;
        $since     = (int) $request->query('since', 0);
        $page      = (int) $request->query('page', 1);

        try {
            $data       = $fetcher($since, $page);
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            SyncLog::logPull($branchId, $entity, $data['count'], $durationMs);

            return $this->success($data);
        } catch (\Exception $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            SyncLog::logPull($branchId, $entity, 0, $durationMs);

            return $this->error('Error al obtener datos: ' . $e->getMessage(), 500);
        }
    }
}