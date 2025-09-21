<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\ApiLocationController;
use App\Http\Controllers\Api\ApiCategoryController;
use App\Http\Controllers\Api\ApiProductController;
use App\Http\Controllers\Api\ApiOrderController;
use App\Http\Controllers\Api\ApiVoucherController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SimpleAuthController;
use App\Http\Controllers\Api\TestApiController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\TestAuthController;

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

// Simple Auth routes (public) - Không cần middleware phức tạp
Route::post('/auth/login', [SimpleAuthController::class, 'login']);
Route::post('/auth/register', [SimpleAuthController::class, 'register']);

// Simple Auth routes (protected) - Chỉ cần auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [SimpleAuthController::class, 'me']);
    Route::post('/auth/logout', [SimpleAuthController::class, 'logout']);
    
    // Test auth
    Route::get('/test-auth', [TestAuthController::class, 'test']);
    
    // User profile routes - User có thể xem/sửa profile của mình
    Route::get('/user/profile', [UserProfileController::class, 'show']);
    Route::put('/user/profile', [UserProfileController::class, 'update']);
});

// Legacy Auth routes (giữ lại để tương thích)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});


// Location API (public)
Route::prefix('locations')->group(function () {
    Route::get('/provinces', [ApiLocationController::class, 'getProvinces']);
    Route::get('/districts', [ApiLocationController::class, 'getDistricts']);
    Route::get('/wards', [ApiLocationController::class, 'getWards']);
    Route::get('/full-address', [ApiLocationController::class, 'getFullAddress']);
});

// Users API (admin only)
Route::prefix('users')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [ApiUserController::class, 'index']);
    Route::post('/', [ApiUserController::class, 'store']);
    Route::get('{id}', [ApiUserController::class, 'show'])->where('id', '[0-9]+');
    Route::put('{id}', [ApiUserController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('{id}', [ApiUserController::class, 'destroy'])->where('id', '[0-9]+');
});

// Categories API
Route::prefix('categories')->group(function () {
    // Public routes
    Route::get('/', [ApiCategoryController::class, 'index']);
    Route::get('{id}', [ApiCategoryController::class, 'show'])->where('id', '[0-9]+');
    
    // Protected routes (admin only)
    Route::middleware(['auth:sanctum', 'permission:manage_categories'])->group(function () {
        Route::post('/', [ApiCategoryController::class, 'store']);
        Route::put('{id}', [ApiCategoryController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [ApiCategoryController::class, 'destroy'])->where('id', '[0-9]+');
    });
});

// Products API
Route::prefix('products')->group(function () {
    // Public routes
    Route::get('/', [ApiProductController::class, 'index']);
    Route::get('{id}', [ApiProductController::class, 'show'])->where('id', '[0-9]+');
    
    // Protected routes (require product management permission)
    Route::middleware(['auth:sanctum', 'permission:manage_products'])->group(function () {
        Route::post('/', [ApiProductController::class, 'store']);
        Route::put('{id}', [ApiProductController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [ApiProductController::class, 'destroy'])->where('id', '[0-9]+');
        Route::post('{id}/restore', [ApiProductController::class, 'restore'])->where('id', '[0-9]+');
        Route::delete('{id}/force', [ApiProductController::class, 'forceDestroy'])->where('id', '[0-9]+');
    });
});

// Orders API
Route::prefix('orders')->group(function () {
    // Người dùng đã đăng nhập có thể xem đơn hàng của họ
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [ApiOrderController::class, 'index']);
        Route::post('/', [ApiOrderController::class, 'store']);
        Route::get('{id}', [ApiOrderController::class, 'show'])->where('id', '[0-9]+');
    });
    
    // Chỉ admin và manager có thể cập nhật và xóa đơn hàng
    Route::middleware(['auth:sanctum', 'permission:update_orders'])->group(function () {
        Route::put('{id}', [ApiOrderController::class, 'update'])->where('id', '[0-9]+');
    });
    
    Route::middleware(['auth:sanctum', 'permission:delete_orders'])->group(function () {
        Route::delete('{id}', [ApiOrderController::class, 'destroy'])->where('id', '[0-9]+');
    });
});

// Vouchers API
Route::prefix('vouchers')->group(function () {
    // Public routes
    Route::get('/', [ApiVoucherController::class, 'index']);
    Route::get('{id}', [ApiVoucherController::class, 'show'])->where('id', '[0-9]+');
    Route::post('check', [ApiVoucherController::class, 'checkVoucher']); // Check voucher by code
    
    // Protected routes (require voucher management permission)
    Route::middleware(['auth:sanctum', 'permission:manage_vouchers'])->group(function () {
        Route::post('/', [ApiVoucherController::class, 'store']);
        Route::put('{id}', [ApiVoucherController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [ApiVoucherController::class, 'destroy'])->where('id', '[0-9]+');
    });
});