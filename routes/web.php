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

// Ajax Controllers
use App\Http\Controllers\Ajax\LocationController;
use App\Http\Controllers\Ajax\UserAjaxController;
use App\Http\Controllers\Ajax\DanhMucAjaxController;
use App\Http\Controllers\Ajax\SanPhamAjaxController;
use App\Http\Controllers\Ajax\DonHangAjaxController;

// ========================================
// DASHBOARD ROUTES
// ========================================
Route::get('/dashboard/index', [DashboardController::class, 'index'])->name('dashboard.index');

// ========================================
// STATISTICS ROUTES
// ========================================
Route::get('/admin/statistics', [StatisticsController::class, 'index'])->name('admin.statistics');
Route::get('/admin/dashboard', [StatisticsController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/statistics/chart-data', [StatisticsController::class, 'getChartData'])->name('admin.statistics.chart');
Route::get('/admin/statistics/filtered-stats', [StatisticsController::class, 'getFilteredStats'])->name('admin.statistics.filtered');
Route::get('/admin/statistics/top-products', [StatisticsController::class, 'getTopProducts'])->name('admin.statistics.products');
Route::get('/admin/statistics/top-customers', [StatisticsController::class, 'getTopCustomers'])->name('admin.statistics.customers');

// ========================================
// STORE SETTINGS ROUTES
// ========================================
Route::prefix('admin/store')->name('admin.store.')->group(function () {
    Route::get('/settings', [StoreSettingsController::class, 'get'])->name('get');
    Route::post('/settings', [StoreSettingsController::class, 'update'])->name('update');
});

// ========================================
// AJAX ROUTES
// ========================================

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
});

// ========================================
// ADMIN ROUTES
// ========================================

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

// Home
Route::view('/', 'fontend.home.trangchu')->name('home');
Route::view('/trangchu', 'fontend.home.trangchu');

// Static pages
Route::view('/gioithieu', 'fontend.about.gioithieu')->name('about');
Route::view('/sanpham', 'fontend.products.sanpham')->name('products');
Route::view('/trangchitiet', 'fontend.product.trangchitiet')->name('product.detail');
Route::view('/giohang', 'fontend.cart.giohang')->name('cart');
Route::view('/contact', 'fontend.contact.contact')->name('contact');

// Authentication
Route::view('/dangnhap', 'fontend.auth.dangnhap')->name('login');
Route::view('/dangki', 'fontend.auth.dangki')->name('register');
