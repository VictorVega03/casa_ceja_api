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
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
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
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, [
            'id', 'barcode', 'name', 'presentation', 'iva',
            'price_retail', 'price_wholesale', 'wholesale_quantity',
            'price_special', 'price_dealer', 'category_id', 'unit_id',
            'active', 'updated_at',
        ], 'page', $page);

        return [
            'data'         => $paginated->items(),
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
            'password', 'user_type', 'branch_id', 'active', 'updated_at'
        ]);

        if ($since > 0) {
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
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
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
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
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
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
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
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
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullCredits(int $branchId, int $since, int $page): array
    {
        // Actualizar vencidos antes de responder
        Credit::where('branch_id', $branchId)
            ->where('status', 1)
            ->where('due_date', '<', now())
            ->update(['status' => 3, 'updated_at' => now()]);

        $cutoff = now()->subDays(90);

        $query = Credit::with(['products', 'payments', 'customer'])
            ->where('branch_id', $branchId);

        if ($since > 0) {
            $sinceDate = Carbon::createFromTimestamp($since - 60);
            $query->where(function ($q) use ($sinceDate, $cutoff) {
                // Pendientes: delta sync desde último corte
                $q->where(function ($q1) use ($sinceDate) {
                    $q1->where('status', 1)
                       ->where('updated_at', '>', $sinceDate);
                })
                // Historial 90 días: siempre completo, sin importar since
                ->orWhere(function ($q2) use ($cutoff) {
                    $q2->whereIn('status', [2, 3, 4])
                       ->where('updated_at', '>=', $cutoff);
                });
            });
        } else {
            // Pull inicial: todos los pendientes + historial 90 días
            $query->where(function ($q) use ($cutoff) {
                $q->where('status', 1)
                  ->orWhere(function ($q2) use ($cutoff) {
                      $q2->whereIn('status', [2, 3, 4])
                         ->where('updated_at', '>=', $cutoff);
                  });
            });
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
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
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }

    public function pullLayaways(int $branchId, int $since, int $page): array
    {
        // Actualizar vencidos antes de responder
        Layaway::where('branch_id', $branchId)
            ->where('status', 1)
            ->where('pickup_date', '<', now())
            ->update(['status' => 3, 'updated_at' => now()]);

        $cutoff = now()->subDays(90);

        $query = Layaway::with(['products', 'payments', 'customer'])
            ->where('branch_id', $branchId);

        if ($since > 0) {
            $sinceDate = Carbon::createFromTimestamp($since - 60);
            $query->where(function ($q) use ($sinceDate, $cutoff) {
                $q->where(function ($q1) use ($sinceDate) {
                    $q1->where('status', 1)
                       ->where('updated_at', '>', $sinceDate);
                })
                ->orWhere(function ($q2) use ($cutoff) {
                    $q2->whereIn('status', [2, 3, 4])
                       ->where('updated_at', '>=', $cutoff);
                });
            });
        } else {
            $query->where(function ($q) use ($cutoff) {
                $q->where('status', 1)
                  ->orWhere(function ($q2) use ($cutoff) {
                      $q2->whereIn('status', [2, 3, 4])
                         ->where('updated_at', '>=', $cutoff);
                  });
            });
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
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
            $query->where('updated_at', '>', Carbon::createFromTimestamp($since - 60));
        }

        $paginated = $query->orderBy('updated_at')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return [
            'data'         => $paginated->items(),
            'count'        => $paginated->count(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ];
    }
}