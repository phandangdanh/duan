<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\StatisticsController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\UserCatalogueController;
use App\Http\Controllers\Backend\DanhMucController;
use App\Http\Controllers\Backend\SanPhamController;
use App\Http\Controllers\Backend\SanPhamColorController;
use App\Http\Controllers\Backend\DonHangController;
use App\Http\Controllers\Backend\StoreSettingsController;
use App\Http\Controllers\Backend\VoucherController;
use App\Http\Controllers\VNPayController;

// Ajax Controllers
use App\Http\Controllers\Ajax\LocationController;
use App\Http\Controllers\Ajax\UserAjaxController;
use App\Http\Controllers\Ajax\DanhMucAjaxController;
use App\Http\Controllers\Ajax\SanPhamAjaxController;
use App\Http\Controllers\Ajax\DonHangAjaxController;

// ========================================
// DASHBOARD ROUTES (Protected - Chỉ admin)
// ========================================
Route::middleware(['auth', 'role:admin', 'adminlock'])->group(function () {
    Route::get('/dashboard/index', [DashboardController::class, 'index'])->name('dashboard.index');
});

// ========================================
// STATISTICS ROUTES (Protected - Chỉ admin)
// ========================================
Route::middleware(['auth', 'role:admin', 'adminlock'])->group(function () {
    Route::get('/admin/', [StatisticsController::class, 'index'])->name('admin.statistics');
    Route::get('/admin/statistics', [StatisticsController::class, 'index'])->name('admin.statistics');
    Route::get('/admin/dashboard', [StatisticsController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/statistics/chart-data', [StatisticsController::class, 'getChartData'])->name('admin.statistics.chart');
    Route::get('/admin/statistics/filtered-stats', [StatisticsController::class, 'getFilteredStats'])->name('admin.statistics.filtered');
    Route::get('/admin/statistics/top-products', [StatisticsController::class, 'getTopProducts'])->name('admin.statistics.products');
    Route::get('/admin/statistics/top-customers', [StatisticsController::class, 'getTopCustomers'])->name('admin.statistics.customers');
});

// ========================================
// STORE SETTINGS ROUTES (Protected - Chỉ admin)
// ========================================
Route::middleware(['auth', 'role:admin', 'adminlock'])->prefix('admin/store')->name('admin.store.')->group(function () {
    Route::get('/settings', [StoreSettingsController::class, 'get'])->name('get');
    Route::post('/settings', [StoreSettingsController::class, 'update'])->name('update');
});

// ========================================
// AJAX ROUTES (Protected - Chỉ admin)
// ========================================
Route::middleware(['auth', 'role:admin', 'adminlock'])->group(function () {
    // Location
    Route::get('ajax/location/getLocation', [LocationController::class, 'getLocation'])
        ->name('ajax.location.getLocation');

// User Ajax
Route::post('ajax/user/toggle-status', [UserAjaxController::class, 'toggleStatus'])
    ->name('ajax.user.toggleStatus');

// DanhMuc Ajax
Route::post('/ajax/danhmuc/toggle-status-danhmuc', [DanhMucAjaxController::class, 'toggleStatus'])
    ->name('ajax.danhmuc.toggle-status');

// SanPham Ajax
Route::post('/ajax/sanpham/toggle-status/{id}', [SanPhamAjaxController::class, 'toggleStatus'])
        ->name('ajax.sanpham.toggleStatus')
        ->where('id', '[0-9]+');

Route::delete('/ajax/sanpham/delete/{id}', [SanPhamAjaxController::class, 'deleteProduct'])
        ->name('ajax.sanpham.delete')
        ->where('id', '[0-9]+');

Route::post('/ajax/sanpham/bulk-action', [SanPhamAjaxController::class, 'bulkAction'])
        ->name('ajax.sanpham.bulkAction');

Route::get('/ajax/sanpham/stats', [SanPhamAjaxController::class, 'getStats'])
        ->name('ajax.sanpham.stats');

Route::post('/ajax/sanpham/upload-temp', [SanPhamAjaxController::class, 'uploadTemp'])
    ->name('ajax.sanpham.uploadTemp');

Route::get('/ajax/sanpham/check-impact/{id}', [SanPhamAjaxController::class, 'checkDeleteImpact'])
        ->name('ajax.sanpham.checkImpact')
        ->where('id', '[0-9]+');

Route::get('/ajax/sanpham/test-delete', [SanPhamAjaxController::class, 'testDeleteFunctionality'])
        ->name('ajax.sanpham.testDelete');

// DonHang Ajax
Route::prefix('ajax/donhang')->name('ajax.donhang.')->group(function () {
    Route::post('/update-trangthai', [DonHangAjaxController::class, 'updateTrangThai'])
        ->name('update.trangthai');
    
    Route::post('/update-trangthai-bulk', [DonHangAjaxController::class, 'updateTrangThaiBulk'])
        ->name('update.trangthai.bulk');
    
    Route::post('/destroy', [DonHangAjaxController::class, 'destroy'])
        ->name('destroy');
    
    Route::get('/get-info', [DonHangAjaxController::class, 'getDonHangInfo'])
        ->name('get.info');
    
    Route::get('/get-stats', [DonHangAjaxController::class, 'getStats'])
        ->name('get.stats');
    
    Route::get('/get-chart-data', [DonHangAjaxController::class, 'getChartData'])
        ->name('get.chart.data');
    
    Route::get('/get-top-customers', [DonHangAjaxController::class, 'getTopCustomers'])
        ->name('get.top.customers');
    
    Route::get('/get-top-products', [DonHangAjaxController::class, 'getTopProducts'])
        ->name('get.top.products');
    
    // Route kiểm tra session cho admin
    Route::get('/admin/check-session', function() {
        if (auth()->check() && auth()->user()->user_catalogue_id == 1) {
            return response()->json(['status' => 'ok']);
        }
        return response()->json(['status' => 'unauthorized'], 401);
    })->name('admin.check.session');
    
});
}); // Đóng group middleware cho AJAX routes

// ========================================
// ADMIN ROUTES (Protected - Chỉ admin)
// ========================================
Route::middleware(['auth', 'role:admin', 'adminlock'])->group(function () {
    // User Management
    Route::group(['prefix' => 'admin/user'], function () {
    Route::get('index', [UserController::class, 'index'])
        ->name('user.index');
    Route::get('statistics', [UserController::class, 'statistics'])
        ->name('user.statistics');
    
    Route::get('create', [UserController::class, 'create'])
        ->name('user.create');
    
    Route::post('store', [UserController::class, 'store'])
        ->name('user.store');
    
    Route::get('edit/{id}', [UserController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('user.edit');
    
    Route::put('update/{id}', [UserController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('user.update');
    
    Route::delete('destroy/{id}', [UserController::class, 'destroy'])
        ->name('user.destroy');
    
    Route::post('bulk-action', [UserController::class, 'bulkAction'])
        ->name('user.bulkAction');
});

// DanhMuc Management
Route::group(['prefix' => 'admin/danhmuc'], function () {
    Route::get('index', [DanhMucController::class, 'index'])
        ->name('danhmuc.index');
    Route::get('statistics', [DanhMucController::class, 'statistics'])
        ->name('danhmuc.statistics');
    
    Route::get('create', [DanhMucController::class, 'create'])
        ->name('danhmuc.create');
    
    Route::post('store', [DanhMucController::class, 'store'])
        ->name('danhmuc.store');
    
    Route::get('edit/{id}', [DanhMucController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('danhmuc.edit');
    
    Route::put('update/{id}', [DanhMucController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('danhmuc.update');
    
    Route::delete('destroy/{id}', [DanhMucController::class, 'destroy'])
        ->name('danhmuc.destroy');
    
    Route::post('toggle-status/{id}', [DanhMucController::class, 'toggleStatus'])
        ->name('danhmuc.toggleStatus');
    
    Route::post('search', [DanhMucController::class, 'search'])
        ->name('danhmuc.search');
    
    Route::post('bulk-action', [DanhMucController::class, 'bulkAction'])
        ->name('danhmuc.bulkAction');
});

// SanPham Management
Route::group(['prefix' => 'admin/sanpham'], function () {
    // Basic CRUD
    Route::get('index', [SanPhamController::class, 'index'])
        ->name('sanpham.index');
    Route::get('statistics', [SanPhamController::class, 'statisticsPage'])
        ->name('sanpham.statistics.page');
    
    Route::get('create', [SanPhamController::class, 'create'])
        ->name('sanpham.create');
    
    Route::post('store', [SanPhamController::class, 'store'])
        ->name('sanpham.store');
    
    Route::get('show/{id}', [SanPhamController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('sanpham.show');
    
    Route::get('edit/{id}', [SanPhamController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('sanpham.edit');
    
    Route::put('update/{id}', [SanPhamController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('sanpham.update');
    
    Route::delete('destroy/{id}', [SanPhamController::class, 'destroy'])
        ->name('sanpham.destroy');
    
    // Delete routes (GET & POST for compatibility)
    Route::get('delete/{id}', [SanPhamController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('sanpham.delete');
    
    Route::post('destroy/{id}', [SanPhamController::class, 'destroyPost'])
        ->where('id', '[0-9]+')
        ->name('sanpham.destroy.post');
    
    // Additional features
    Route::post('toggle-status/{id}', [SanPhamController::class, 'toggleStatus'])
        ->name('sanpham.toggleStatus');
    
    Route::post('search', [SanPhamController::class, 'search'])
        ->name('sanpham.search');
    
    Route::post('bulk-action', [SanPhamController::class, 'bulkAction'])
        ->name('sanpham.bulkAction');
    
    Route::post('bulk-delete', [SanPhamController::class, 'bulkDelete'])
        ->name('sanpham.bulkDelete');
    
    Route::post('bulk-status', [SanPhamController::class, 'bulkStatus'])
        ->name('sanpham.bulkStatus');
    
    // Soft delete & restore
    Route::get('trashed', [SanPhamController::class, 'trashed'])
        ->name('sanpham.trashed');
    
    Route::delete('force-delete/{id}', [SanPhamController::class, 'forceDelete'])
        ->name('sanpham.forceDelete');
    
    Route::post('force-delete/{id}', [SanPhamController::class, 'forceDelete'])
        ->name('sanpham.forceDelete.post');
    
    Route::post('restore/{id}', [SanPhamController::class, 'restore'])
        ->name('sanpham.restore');
    
    Route::post('restore-all', [SanPhamController::class, 'restoreAll'])
        ->name('sanpham.restoreAll');
    
    Route::post('force-delete-all', [SanPhamController::class, 'forceDeleteAll'])
        ->name('sanpham.forceDeleteAll');
    
    // API endpoints
    Route::get('api/list', [SanPhamController::class, 'apiIndex'])
        ->name('sanpham.api.index');
    
    Route::get('api/show/{id}', [SanPhamController::class, 'apiShow'])
        ->where('id', '[0-9]+')
        ->name('sanpham.api.show');
    
    Route::get('api/statistics', [SanPhamController::class, 'statistics'])
        ->name('sanpham.api.statistics');
});

// SanPham Color Management (New Structure)
Route::group(['prefix' => 'admin/sanpham-color'], function () {
    Route::get('create', [SanPhamColorController::class, 'create'])
        ->name('sanpham.color.create');
    
    Route::post('store', [SanPhamColorController::class, 'store'])
        ->name('sanpham.color.store');
    
    Route::get('show/{id}', [SanPhamColorController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('sanpham.color.show');
    
    Route::get('edit/{id}', [SanPhamColorController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('sanpham.color.edit');
    
    Route::put('update/{id}', [SanPhamColorController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('sanpham.color.update');
});

// DonHang Management
Route::group(['prefix' => 'admin/donhang'], function () {
    Route::get('/', [DonHangController::class, 'index'])
        ->name('admin.donhang.index');
    
    Route::get('/statistics', [DonHangController::class, 'statistics'])
        ->name('admin.donhang.statistics');
    
    Route::get('/{id}', [DonHangController::class, 'show'])
        ->name('admin.donhang.show');
    
    Route::get('/{id}/print', [DonHangController::class, 'print'])
        ->name('admin.donhang.print');
    
    Route::put('/{id}/trangthai', [DonHangController::class, 'updateTrangThai'])
        ->name('admin.donhang.update.trangthai');
    
    Route::delete('/{id}', [DonHangController::class, 'destroy'])
        ->name('admin.donhang.destroy');
    
    Route::get('/export/excel', [DonHangController::class, 'export'])
        ->name('admin.donhang.export.excel');
    
    Route::get('/api/chart-data', [DonHangController::class, 'getChartData'])
        ->name('admin.donhang.api.chart.data');
});

// Voucher Management
Route::group(['prefix' => 'admin/vouchers'], function () {
    Route::get('/', [VoucherController::class, 'index'])
        ->name('admin.vouchers.index');
    
    Route::get('/create', [VoucherController::class, 'create'])
        ->name('admin.vouchers.create');
    
    Route::post('/', [VoucherController::class, 'store'])
        ->name('admin.vouchers.store');
    
    Route::get('/{id}', [VoucherController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('admin.vouchers.show');
    
    Route::get('/{id}/edit', [VoucherController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('admin.vouchers.edit');
    
    Route::put('/{id}', [VoucherController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('admin.vouchers.update');
    
    Route::delete('/{id}', [VoucherController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('admin.vouchers.destroy');
    
    Route::post('/{id}/toggle-status', [VoucherController::class, 'toggleStatus'])
        ->where('id', '[0-9]+')
        ->name('admin.vouchers.toggle-status');
    
    Route::get('/expiring-soon', [VoucherController::class, 'expiringSoon'])
        ->name('admin.vouchers.expiring-soon');
    
    Route::get('/low-stock', [VoucherController::class, 'lowStock'])
        ->name('admin.vouchers.low-stock');
    
    Route::get('/search', [VoucherController::class, 'search'])
        ->name('admin.vouchers.search');
    
                Route::get('/statistics', [VoucherController::class, 'statistics'])
                    ->name('admin.vouchers.statistics');
                Route::get('/refresh-csrf', [VoucherController::class, 'refreshCsrf'])
                    ->name('admin.vouchers.refresh-csrf');
    
    Route::get('/export', [VoucherController::class, 'export'])
        ->name('admin.vouchers.export');
    
    // AJAX routes
    Route::post('/generate-code', [VoucherController::class, 'generateCode'])
        ->name('admin.vouchers.generate-code');
    
    Route::post('/check-code', [VoucherController::class, 'checkCode'])
        ->name('admin.vouchers.check-code');
});

// ========================================
// FRONTEND ROUTES
// ========================================

}); // Đóng group middleware cho ADMIN ROUTES

// ========================================
// PUBLIC ROUTES
// ========================================

// Home
Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('home');
Route::get('/trangchu', [\App\Http\Controllers\Frontend\HomeController::class, 'index']);
Route::get('/trangchu-api', [\App\Http\Controllers\Frontend\HomeController::class, 'indexApi'])->name('home.api');

// Products
Route::get('/sanpham', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('products');
Route::get('/sanpham/{id}', [\App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('product.detail');
Route::get('/sanpham-api/{id}', [\App\Http\Controllers\Frontend\ProductController::class, 'showApi'])->name('product.detail.api');

// Categories
Route::get('/danh-muc/{slug}', [\App\Http\Controllers\Frontend\CategoryController::class, 'show'])->name('category.show');

// Cart routes
Route::get('/giohang', [\App\Http\Controllers\Frontend\CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [\App\Http\Controllers\Frontend\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [\App\Http\Controllers\Frontend\CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [\App\Http\Controllers\Frontend\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [\App\Http\Controllers\Frontend\CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/info', [\App\Http\Controllers\Frontend\CartController::class, 'getCartInfo'])->name('cart.info');
Route::get('/api/cart/display-stock/{productId}', [\App\Http\Controllers\Frontend\CartController::class, 'getDisplayStock'])->name('cart.display.stock');

// Checkout routes - yêu cầu đăng nhập
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [\App\Http\Controllers\Frontend\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [\App\Http\Controllers\Frontend\CheckoutController::class, 'processOrder'])->name('checkout.process');
    Route::get('/checkout/success/{orderId}', [\App\Http\Controllers\Frontend\CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure/{orderId}', [\App\Http\Controllers\Frontend\CheckoutController::class, 'failure'])->name('checkout.failure');
    Route::post('/checkout/check-voucher', [\App\Http\Controllers\Frontend\CheckoutController::class, 'checkVoucher'])->name('checkout.check.voucher');
});

// Order routes - yêu cầu đăng nhập
Route::middleware(['auth'])->group(function () {
    Route::get('/don-hang', [\App\Http\Controllers\Frontend\OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{id}', [\App\Http\Controllers\Frontend\OrderController::class, 'show'])->name('orders.detail');
    Route::post('/don-hang/{id}/cancel', [\App\Http\Controllers\Frontend\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/don-hang/{id}/reorder', [\App\Http\Controllers\Frontend\OrderController::class, 'reorder'])->name('orders.reorder');
    
    // Bank payment routes
    Route::get('/thanh-toan-ngan-hang/{id}', [\App\Http\Controllers\Frontend\BankPaymentController::class, 'showBankInfo'])->name('bank.payment.info');
    Route::post('/thanh-toan-ngan-hang/{id}/confirm', [\App\Http\Controllers\Frontend\BankPaymentController::class, 'confirmPayment'])->name('bank.payment.confirm');
});

// Static pages
Route::view('/gioithieu', 'fontend.about.gioithieu')->name('about');
Route::view('/contact', 'fontend.contact.contact')->name('contact');

// Authentication
Route::view('/dangnhap', 'fontend.auth.dangnhap')->name('login');
Route::view('/dangki', 'fontend.auth.dangki')->name('register');
Route::post('/dangki', [\App\Http\Controllers\Auth\AuthController::class, 'register'])->name('register.post');
Route::post('/dangnhap', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

// Google Login
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);
Route::get('/auth/google/logout', [\App\Http\Controllers\Auth\GoogleController::class, 'logoutGoogle'])->name('google.logout');

// Password Reset Routes
Route::get('/quen-mat-khau', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/quen-mat-khau', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/dat-lai-mat-khau/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/dat-lai-mat-khau', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Product listing pages
Route::get('/sanpham-noi-bat', [\App\Http\Controllers\Frontend\ProductController::class, 'featured'])->name('products.featured');
Route::get('/sanpham-khuyen-mai', [\App\Http\Controllers\Frontend\ProductController::class, 'sale'])->name('products.sale');
Route::get('/sanpham-ban-chay', [\App\Http\Controllers\Frontend\ProductController::class, 'bestselling'])->name('products.bestselling');

// Payment check route
Route::get('/checkout/check-payment', function () {
    return view('fontend.checkout.check-payment');
})->name('checkout.check-payment');

// Payment success route
Route::get('/checkout/payment-success', function () {
    return view('fontend.checkout.payment-success');
})->name('checkout.payment-success');

// Test payment API route
Route::get('/test-payment-api', function () {
    $totalOrders = \App\Models\DonHang::count();
    $firstOrder = $totalOrders > 0 ? \App\Models\DonHang::first() : null;
    
    return response()->json([
        'total_orders' => $totalOrders,
        'first_order' => $firstOrder ? [
            'id' => $firstOrder->id,
            'phone' => $firstOrder->sodienthoai,
            'payment_status' => $firstOrder->trangthaithanhtoan ?? 0,
            'total_amount' => $firstOrder->tongtien
        ] : null
    ]);
});


