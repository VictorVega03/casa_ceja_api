<?php

use App\Http\Controllers\Api\Admin\BranchController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\SupplierController;
use App\Http\Controllers\Api\Admin\TokenController;
use App\Http\Controllers\Api\Admin\UnitController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

// ─── Admin Routes ───────────────────────────────────────────
// apiResource se encarga de crear en automatico las rutas para index, store, show, update y destroy, etc.
// en pocas palabras crea todas las rutas para cada CRUD, lo que hace que el código sea mas limpio y fácil de mantener, ademas de seguir las convenciones RESTful.
Route::prefix('admin')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('units', UnitController::class);
    Route::apiResource('branches', BranchController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/password', [UserController::class, 'updatePassword']);

    Route::get('tokens', [TokenController::class, 'index']);
    Route::post('tokens', [TokenController::class, 'store']);
    Route::patch('tokens/{branchToken}/toggle', [TokenController::class, 'toggle']);
    Route::delete('tokens/{branchToken}', [TokenController::class, 'destroy']);
});