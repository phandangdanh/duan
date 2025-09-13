<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\ApiLocationController;
use App\Http\Controllers\Api\ApiCategoryController;
use App\Http\Controllers\Api\ApiProductController;
use App\Http\Controllers\Api\ApiOrderController;
use App\Http\Controllers\Api\ApiVoucherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Location API (public)
Route::prefix('locations')->group(function () {
    Route::get('/provinces', [ApiLocationController::class, 'getProvinces']);
    Route::get('/districts', [ApiLocationController::class, 'getDistricts']);
    Route::get('/wards', [ApiLocationController::class, 'getWards']);
    Route::get('/full-address', [ApiLocationController::class, 'getFullAddress']);
});


    

    // Users API (admin only)
    Route::prefix('users')->group(function () {
        Route::get('/', [ApiUserController::class, 'index']);
        Route::post('/', [ApiUserController::class, 'store']);
        Route::get('{id}', [ApiUserController::class, 'show'])->where('id', '[0-9]+');
        Route::put('{id}', [ApiUserController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [ApiUserController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // Categories API
    Route::prefix('categories')->group(function () {
        Route::get('/', [ApiCategoryController::class, 'index']);
        Route::post('/', [ApiCategoryController::class, 'store']);
        Route::get('{id}', [ApiCategoryController::class, 'show'])->where('id', '[0-9]+');
        Route::put('{id}', [ApiCategoryController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [ApiCategoryController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // Products API
    Route::prefix('products')->group(function () {
        Route::get('/', [ApiProductController::class, 'index']);
        Route::post('/', [ApiProductController::class, 'store']);
        Route::get('{id}', [ApiProductController::class, 'show'])->where('id', '[0-9]+');
        Route::put('{id}', [ApiProductController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [ApiProductController::class, 'destroy'])->where('id', '[0-9]+');
        Route::post('{id}/restore', [ApiProductController::class, 'restore'])->where('id', '[0-9]+');
        Route::delete('{id}/force', [ApiProductController::class, 'forceDestroy'])->where('id', '[0-9]+');
    });

    // Orders API
    Route::prefix('orders')->group(function () {
        Route::get('/', [ApiOrderController::class, 'index']);
        Route::post('/', [ApiOrderController::class, 'store']);
        Route::get('{id}', [ApiOrderController::class, 'show'])->where('id', '[0-9]+');
        Route::put('{id}', [ApiOrderController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [ApiOrderController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // Vouchers API
    Route::prefix('vouchers')->group(function () {
        Route::get('/', [ApiVoucherController::class, 'index']);
        Route::post('/', [ApiVoucherController::class, 'store']);
        Route::get('{id}', [ApiVoucherController::class, 'show'])->where('id', '[0-9]+');
        Route::put('{id}', [ApiVoucherController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [ApiVoucherController::class, 'destroy'])->where('id', '[0-9]+');
    });


