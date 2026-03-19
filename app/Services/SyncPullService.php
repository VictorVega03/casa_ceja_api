<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\StockEntry;
use App\Models\StockOutput;
use App\Models\CashClose;
use App\Models\Sale;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\Layaway;
use App\Models\LayawayPayment;
use Carbon\Carbon;

class SyncPullService
{
    const PER_PAGE = 200;

    public function pull(string $modelClass, int $since, int $page): array
    {
        $query = $modelClass::query();

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullProducts(int $since, int $page): array
    {
        $query = Product::query();

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, [
            'id', 'barcode', 'name', 'presentation', 'iva',
            'price_retail', 'price_wholesale', 'wholesale_quantity',
            'price_special', 'price_dealer', 'category_id', 'unit_id',
            'active', 'updated_at',
        ], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullUsers(int $since, int $page): array
    {
        $query = User::select([
            'id', 'name', 'email', 'phone', 'username',
            'user_type', 'branch_id', 'active', 'updated_at'
        ]);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullStockEntries(int $branchId, int $since, int $page): array
    {
        $query = StockEntry::with(['products'])
            ->where('branch_id', $branchId);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullStockOutputs(int $branchId, int $since, int $page): array
    {
        $query = StockOutput::with(['products'])
            ->where('origin_branch_id', $branchId);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullCashCloses(int $branchId, int $since, int $page): array
    {
        $query = CashClose::where('branch_id', $branchId);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullSales(int $branchId, int $since, int $page): array
    {
        $query = Sale::with(['products'])
            ->where('branch_id', $branchId);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullCredits(int $branchId, int $since, int $page): array
    {
        $query = Credit::with(['products', 'payments', 'customer'])
            ->where('branch_id', $branchId);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullCreditPayments(int $branchId, int $since, int $page): array
    {
        $query = CreditPayment::where('branch_id', $branchId);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullLayaways(int $branchId, int $since, int $page): array
    {
        $query = Layaway::with(['products', 'payments', 'customer'])
            ->where('branch_id', $branchId);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullLayawayPayments(int $branchId, int $since, int $page): array
    {
        $query = LayawayPayment::where('branch_id', $branchId);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'records'      => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }
}