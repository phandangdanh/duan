<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoucherStoreRequest;
use App\Http\Requests\VoucherUpdateRequest;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VoucherController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Hiển thị danh sách voucher
     */
    public function index(Request $request): View
    {
        $perPageParam = $request->get('per_page', 10);
        // Hỗ trợ "Tất cả" khi per_page = 'all'
        $perPage = (string)$perPageParam === 'all' ? 1000000 : (int)$perPageParam;
        $filters = $request->only(['search', 'trang_thai', 'loai_giam_gia', 'trang_thai_hoat_dong', 'ngay_bat_dau', 'ngay_ket_thuc']);
        
        $vouchers = $this->voucherService->getPaginatedVouchers($perPage, $filters);
        $statistics = $this->voucherService->getVoucherStatistics();
        
        return view('backend.vouchers.index', compact('vouchers', 'statistics', 'filters'));
    }

    /**
     * Hiển thị form tạo voucher
     */
    public function create(): View
    {
        return view('backend.vouchers.create');
    }

    /**
     * Lưu voucher mới
     */
    public function store(VoucherStoreRequest $request): RedirectResponse
    {
        $result = $this->voucherService->createVoucher($request->validated());
        
        if ($result['success']) {
            return redirect()->route('admin.vouchers.index')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Hiển thị chi tiết voucher
     */
    public function show(int $id): View
    {
        $voucher = $this->voucherService->getVoucherById($id);
        
        if (!$voucher) {
            abort(404, 'Voucher không tồn tại');
        }

        return view('backend.vouchers.show', compact('voucher'));
    }

    /**
     * Hiển thị form chỉnh sửa voucher
     */
    public function edit(int $id): View
    {
        $voucher = $this->voucherService->getVoucherById($id);
        
        if (!$voucher) {
            abort(404, 'Voucher không tồn tại');
        }

        return view('backend.vouchers.edit', compact('voucher'));
    }

    /**
     * Cập nhật voucher
     */
    public function update(VoucherUpdateRequest $request, int $id): RedirectResponse
    {
        $result = $this->voucherService->updateVoucher($id, $request->validated());
        
        if ($result['success']) {
            return redirect()->route('admin.vouchers.index')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Xóa voucher
     */
    public function destroy(int $id)
    {
        $result = $this->voucherService->deleteVoucher($id);
        
        if (request()->ajax()) {
            return response()->json($result);
        }
        
        if ($result['success']) {
            return redirect()->route('admin.vouchers.index')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->with('error', $result['message']);
    }

    /**
     * Kích hoạt/tạm dừng voucher
     */
    public function toggleStatus(int $id)
    {
        $result = $this->voucherService->toggleVoucherStatus($id);

        // Trả về JSON nếu là AJAX để frontend xử lý, ngược lại redirect bình thường
        if (request()->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Lấy voucher sắp hết hạn
     */
    public function expiringSoon(Request $request): View
    {
        $days = $request->get('days', 7);
        $vouchers = $this->voucherService->getExpiringSoonVouchers($days);
        
        return view('backend.vouchers.expiring-soon', compact('vouchers', 'days'));
    }

    /**
     * Lấy voucher có số lượng thấp
     */
    public function lowStock(Request $request): View
    {
        $threshold = $request->get('threshold', 10);
        $vouchers = $this->voucherService->getLowStockVouchers($threshold);
        
        return view('backend.vouchers.low-stock', compact('vouchers', 'threshold'));
    }

    /**
     * Tìm kiếm voucher
     */
    public function search(Request $request): View
    {
        $keyword = $request->get('keyword');
        $vouchers = $this->voucherService->searchVouchers($keyword);
        
        return view('backend.vouchers.search', compact('vouchers', 'keyword'));
    }

    /**
     * Lấy thống kê voucher
     */
    public function statistics(Request $request): View
    {
        $statistics = $this->voucherService->getVoucherStatistics();
        $mostUsed = $this->voucherService->getMostUsedVouchers(10);
        $expiringSoon = $this->voucherService->getExpiringSoonVouchers(7);
        $lowStock = $this->voucherService->getLowStockVouchers(10);

        // Dữ liệu biểu đồ theo ngày, hỗ trợ ?range=7|30|90
        $range = (int) $request->get('range', 30);
        if (!in_array($range, [7,30,90])) { $range = 30; }
        $labels = [];
        $data = [];
        for ($i=$range-1; $i>=0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $labels[] = $date->format('d/m');
            $data[] = \App\Models\Voucher::whereDate('created_at', $date)->count();
        }
        
        return view('backend.vouchers.statistics', compact('statistics', 'mostUsed', 'expiringSoon', 'lowStock','labels','data'));
    }

    /**
     * Refresh CSRF token (AJAX)
     */
    public function refreshCsrf()
    {
        return response()->json([
            'csrf_token' => csrf_token()
        ]);
    }

    /**
     * Tạo mã voucher tự động (AJAX)
     */
    public function generateCode(Request $request)
    {
        $prefix = $request->get('prefix', 'VOUCHER');
        $code = $this->voucherService->generateVoucherCode($prefix);
        
        return response()->json([
            'success' => true,
            'code' => $code
        ]);
    }

    /**
     * Kiểm tra mã voucher (AJAX)
     */
    public function checkCode(Request $request)
    {
        $code = $request->get('code');
        $voucher = $this->voucherService->getVoucherByCode($code);
        
        return response()->json([
            'exists' => $voucher !== null,
            'message' => $voucher ? 'Mã voucher đã tồn tại' : 'Mã voucher có thể sử dụng'
        ]);
    }

    /**
     * Xuất danh sách voucher (Excel/CSV)
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'trang_thai', 'loai_giam_gia', 'trang_thai_hoat_dong']);
        $vouchers = $this->voucherService->getAllVouchers($filters);
        
        // TODO: Implement export functionality
        return response()->json([
            'message' => 'Chức năng xuất file sẽ được triển khai'
        ]);
    }
}
