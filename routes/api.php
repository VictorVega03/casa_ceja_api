<?php

use App\Http\Controllers\Api\Auth\AuthController;
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
use App\Http\Controllers\Api\Inventory\InventoryController;
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

// ── Login (sin autenticación) ────────────────────────────────
Route::post('/v1/auth/login', [AuthController::class, 'login']);

// ── Rutas protegidas por token de usuario ────────────────────
Route::prefix('v1')->middleware('user.token')->group(function () {

    // ── PULL — cliente jala datos del servidor ───────────────
    Route::prefix('sync/pull')->group(function () {
        Route::get('/products',         [PullController::class, 'products']);
        Route::get('/categories',       [PullController::class, 'categories']);
        Route::get('/units',            [PullController::class, 'units']);
        Route::get('/branches',         [PullController::class, 'branches']);
        Route::get('/roles',            [PullController::class, 'roles']);
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
        Route::post('/customers',        [PushController::class, 'customers']);
        Route::post('/credits',          [PushController::class, 'credits']);
        Route::post('/credit-payments',  [PushController::class, 'creditPayments']);
        Route::post('/layaways',         [PushController::class, 'layaways']);
        Route::post('/layaway-payments', [PushController::class, 'layawayPayments']);
        Route::post('/stock-entries',    [PushController::class, 'stockEntries']);
        Route::post('/stock-outputs',    [PushController::class, 'stockOutputs']);
        Route::post('/users',            [PushController::class, 'users']);
    });

    // ── INVENTARIO — operaciones en tiempo real (requieren conexión) ──
    Route::prefix('inventory')->group(function () {
        Route::post('/outputs',               [InventoryController::class, 'storeOutput']);
        Route::get('/pending-transfers',      [InventoryController::class, 'pendingTransfers']);
        Route::post('/confirm-transfer/{id}', [InventoryController::class, 'confirmTransfer']);
        Route::get('/unconfirmed-alerts',     [InventoryController::class, 'unconfirmedAlerts']);
    });

    // ── ON-DEMAND — consultas en tiempo real ─────────────────
    Route::prefix('stock')->group(function () {
        Route::get('/product/{barcode}',                                [StockController::class, 'byProduct']);
        Route::get('/branch/{branchId}',                                [StockController::class, 'byBranch']);
        Route::put('/branch/{branchId}/product/{productId}/add',        [StockController::class, 'addStock']);
        Route::put('/branch/{branchId}/product/{productId}/subtract',   [StockController::class, 'subtractStock']);
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
        Route::delete('tokens/{userToken}',       [TokenController::class, 'destroy']);

        Route::get('reports/sales',         [ReportController::class, 'sales']);
        Route::get('reports/cash-closes',   [ReportController::class, 'cashCloses']);
        Route::get('reports/credits',       [ReportController::class, 'credits']);
        Route::get('reports/layaways',      [ReportController::class, 'layaways']);
        Route::get('reports/inventory',     [ReportController::class, 'inventory']);
        Route::get('reports/product-stock', [ReportController::class, 'productStock']);
    });
});