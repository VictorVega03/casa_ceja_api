<?php

use App\Http\Controllers\Api\Admin\BranchController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\SupplierController;
use App\Http\Controllers\Api\Admin\TokenController;
use App\Http\Controllers\Api\Admin\UnitController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Sync\PullController;
use App\Http\Controllers\Api\Sync\PushController;
use App\Http\Controllers\Api\OnDemand\StockController;
use Illuminate\Support\Facades\Route;

// ── Health Check (sin autenticación) ────────────────────────
Route::get('/v1/health', function () {
    return response()->json([
        'status'      => 'ok',
        'version'     => '1.0.0',
        'server_time' => now()->timestamp,
        'laravel'     => app()->version(),
    ]);
});

// ── Rutas protegidas por token de sucursal ───────────────────
Route::prefix('v1')->middleware('branch.token')->group(function () {

    // ── PULL — cliente jala datos del servidor ───────────────
    Route::prefix('sync/pull')->group(function () {
        Route::get('/products',         [PullController::class, 'products']);
        Route::get('/categories',       [PullController::class, 'categories']);
        Route::get('/units',            [PullController::class, 'units']);
        Route::get('/branches',         [PullController::class, 'branches']);
        Route::get('/users',            [PullController::class, 'users']);
        Route::get('/customers',        [PullController::class, 'customers']);
        Route::get('/suppliers',        [PullController::class, 'suppliers']);
        Route::get('/sales',            [PullController::class, 'sales']);
        Route::get('/cash-closes',      [PullController::class, 'cashCloses']);
        Route::get('/credits',          [PullController::class, 'credits']);
        Route::get('/credit-payments',  [PullController::class, 'creditPayments']);
        Route::get('/layaways',         [PullController::class, 'layaways']);
        Route::get('/layaway-payments', [PullController::class, 'layawayPayments']);
        Route::get('/stock-entries',    [PullController::class, 'stockEntries']);
        Route::get('/stock-outputs',    [PullController::class, 'stockOutputs']);
    });

    // ── PUSH — cliente sube datos al servidor ────────────────
    Route::prefix('sync/push')->group(function () {
        Route::post('/sales',            [PushController::class, 'sales']);
        Route::post('/cash-closes',      [PushController::class, 'cashCloses']);
        Route::post('/credits',          [PushController::class, 'credits']);
        Route::post('/credit-payments',  [PushController::class, 'creditPayments']);
        Route::post('/layaways',         [PushController::class, 'layaways']);
        Route::post('/layaway-payments', [PushController::class, 'layawayPayments']);
        Route::post('/stock-entries',    [PushController::class, 'stockEntries']);
        Route::post('/stock-outputs',    [PushController::class, 'stockOutputs']);
    });

    // ── ON-DEMAND — consultas en tiempo real ─────────────────
    Route::prefix('stock')->group(function () {
        Route::get('/product/{barcode}',                                    [StockController::class, 'byProduct']);
        Route::get('/branch/{branchId}',                                    [StockController::class, 'byBranch']);
        Route::put('/branch/{branchId}/product/{productId}/add',            [StockController::class, 'addStock']);
        Route::put('/branch/{branchId}/product/{productId}/subtract',       [StockController::class, 'subtractStock']);
    });

    // ── ADMIN — gestión de catálogos ─────────────────────────
    Route::prefix('admin')->group(function () {
        Route::apiResource('products',   ProductController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('units',      UnitController::class);
        Route::apiResource('branches',   BranchController::class);
        Route::apiResource('users',      UserController::class);
        Route::apiResource('suppliers',  SupplierController::class);
        Route::apiResource('customers',  CustomerController::class);

        Route::patch('users/{user}/password', [UserController::class, 'updatePassword']);

        Route::get('roles', [RoleController::class, 'index']);

        Route::get('tokens',                        [TokenController::class, 'index']);
        Route::post('tokens',                       [TokenController::class, 'store']);
        Route::patch('tokens/{branchToken}/toggle', [TokenController::class, 'toggle']);
        Route::delete('tokens/{branchToken}',       [TokenController::class, 'destroy']);

        Route::get('reports/sales',         [ReportController::class, 'sales']);
        Route::get('reports/cash-closes',   [ReportController::class, 'cashCloses']);
        Route::get('reports/credits',       [ReportController::class, 'credits']);
        Route::get('reports/layaways',      [ReportController::class, 'layaways']);
        Route::get('reports/inventory',     [ReportController::class, 'inventory']);
        Route::get('reports/product-stock', [ReportController::class, 'productStock']);
    });
});