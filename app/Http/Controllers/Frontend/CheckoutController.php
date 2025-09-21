<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\DonHangService;
use App\Services\VoucherService;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $orderService;
    protected $voucherService;
    protected $locationService;

    public function __construct(
        CartService $cartService,
        DonHangService $orderService,
        VoucherService $voucherService,
        LocationService $locationService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->voucherService = $voucherService;
        $this->locationService = $locationService;
    }

    /**
     * Hiển thị trang checkout
     */
    public function index()
    {
        try {
            // Kiểm tra giỏ hàng có rỗng không
            if ($this->cartService->isEmpty()) {
                return redirect()->route('cart')->with('error', 'Giỏ hàng trống!');
            }

            $cartItems = $this->cartService->getCartWithDetails();
            $total = $this->cartService->getTotal();
            $cartCount = $this->cartService->getCartCount();

            // Lấy thông tin địa chỉ
            $provincesData = $this->locationService->getProvinces();
            $provinces = $provincesData['data'] ?? collect([]);
            $districts = collect([]);
            $wards = collect([]);

            // Lấy thông tin user nếu đã đăng nhập
            $user = Auth::user();
            $userInfo = null;
            if ($user) {
                $userInfo = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->sodienthoai ?? '',
                    'address' => $user->diachi ?? '',
                ];
            }

            return view('fontend.checkout.index', compact(
                'cartItems',
                'total',
                'cartCount',
                'provinces',
                'districts',
                'wards',
                'userInfo'
            ));
        } catch (\Exception $e) {
            Log::error('CheckoutController index error: ' . $e->getMessage());
            return redirect()->route('cart')->with('error', 'Có lỗi xảy ra khi tải trang thanh toán');
        }
    }

    /**
     * Xử lý đơn hàng
     */
    public function processOrder(Request $request)
    {
        try {
            // Validate dữ liệu
            $request->validate([
                'hoten' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'sodienthoai' => 'required|string|max:20',
                'diachigiaohang' => 'required|string|max:500',
                'phuongthucthanhtoan' => 'required|string|in:cod,banking,momo,zalopay',
                'ghichu' => 'nullable|string|max:1000',
                'voucher_code' => 'nullable|string|max:50',
            ]);

            // Kiểm tra giỏ hàng
            if ($this->cartService->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng trống!'
                ], 400);
            }

            $cartItems = $this->cartService->getCartWithDetails();
            $total = $this->cartService->getTotal();

            // Chuẩn bị dữ liệu đơn hàng
            $orderData = [
                'id_user' => Auth::id() ?? 1, // Guest user ID = 1
                'hoten' => $request->hoten,
                'email' => $request->email,
                'sodienthoai' => $request->sodienthoai,
                'diachigiaohang' => $request->diachigiaohang,
                'phuongthucthanhtoan' => $request->phuongthucthanhtoan,
                'ghichu' => $request->ghichu,
                'chi_tiet_don_hang' => $this->prepareOrderDetails($cartItems),
                'vouchers' => $this->prepareVouchers($request->voucher_code),
            ];

            // Chuẩn bị dữ liệu cho DonHangService
            $orderDataForService = [
                'id_user' => $orderData['id_user'],
                'tongtien' => $total,
                'hoten' => $orderData['hoten'],
                'email' => $orderData['email'],
                'sodienthoai' => $orderData['sodienthoai'],
                'diachigiaohang' => $orderData['diachigiaohang'],
                'phuongthucthanhtoan' => $orderData['phuongthucthanhtoan'],
                'ghichu' => $orderData['ghichu'],
                'chi_tiet_don_hang' => $this->prepareOrderDetailsForService($cartItems),
            ];

            // Kiểm tra tồn kho trước khi tạo đơn hàng
            $inventoryCheck = $this->cartService->checkInventory();
            if (!$inventoryCheck['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $inventoryCheck['message']
                ], 400);
            }
            
            // Lưu cảnh báo tồn kho nếu có
            $inventoryWarnings = $inventoryCheck['warnings'] ?? [];

            // Tạo đơn hàng
            Log::info('Creating order with data:', $orderDataForService);
            $order = $this->orderService->createOrder($orderDataForService);
            Log::info('Order created:', ['order_id' => $order ? $order->id : 'null', 'order_type' => get_class($order)]);

            if ($order) {
                // Xóa giỏ hàng sau khi tạo đơn hàng thành công
                $this->cartService->clearCart();

                // Xử lý thanh toán
                if ($request->phuongthucthanhtoan === 'cod') {
                    // COD - trừ tồn kho ngay vì đã thanh toán
                    $inventoryResult = $this->cartService->deductInventory();
                    if (!$inventoryResult['success']) {
                        // Nếu không đủ tồn kho, xóa đơn hàng và trả về lỗi
                        $order->delete();
                        return response()->json([
                            'success' => false,
                            'message' => $inventoryResult['message']
                        ], 400);
                    }
                    
                    $response = [
                        'success' => true,
                        'message' => 'Đặt hàng thành công! Bạn sẽ thanh toán khi nhận hàng.',
                        'order_id' => $order->id,
                        'redirect_url' => route('order.success', $order->id)
                    ];
                    
                    // Thêm cảnh báo tồn kho nếu có
                    if (!empty($inventoryWarnings)) {
                        $response['warnings'] = $inventoryWarnings;
                        $response['message'] .= ' Lưu ý: ' . implode('; ', $inventoryWarnings);
                    }
                    
                    return response()->json($response);
                } else {
                    // Thanh toán online - KHÔNG trừ tồn kho, chờ thanh toán thành công
                    $paymentUrl = $this->processOnlinePayment($order, $request->phuongthucthanhtoan);
                    return response()->json([
                        'success' => true,
                        'message' => 'Đang chuyển hướng đến cổng thanh toán...',
                        'order_id' => $order->id,
                        'payment_url' => $paymentUrl
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tạo đơn hàng!'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('CheckoutController processOrder error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trang thành công
     */
    public function success($orderId)
    {
        try {
            $order = $this->orderService->getOrderById($orderId);
            
            if (!$order) {
                return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng!');
            }

            return view('fontend.checkout.success', compact('order'));
        } catch (\Exception $e) {
            Log::error('CheckoutController success error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Trang thất bại
     */
    public function failure($orderId)
    {
        try {
            $order = $this->orderService->getOrderById($orderId);
            
            return view('fontend.checkout.failure', compact('order'));
        } catch (\Exception $e) {
            Log::error('CheckoutController failure error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Kiểm tra voucher
     */
    public function checkVoucher(Request $request)
    {
        try {
            $request->validate([
                'voucher_code' => 'required|string|max:50'
            ]);

            $voucher = $this->voucherService->getVoucherByCode($request->voucher_code);
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã voucher không hợp lệ!'
                ]);
            }

            $cartTotal = $this->cartService->getTotal();
            $discount = $this->calculateVoucherDiscount($voucher, $cartTotal);

            return response()->json([
                'success' => true,
                'voucher' => $voucher,
                'discount' => $discount,
                'final_total' => $cartTotal - $discount
            ]);

        } catch (\Exception $e) {
            Log::error('CheckoutController checkVoucher error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra voucher!'
            ], 500);
        }
    }

    /**
     * Chuẩn bị chi tiết đơn hàng
     */
    private function prepareOrderDetails($cartItems)
    {
        $details = [];
        foreach ($cartItems as $item) {
            // Kiểm tra product có tồn tại không
            if (!isset($item['product']) || !$item['product']) {
                continue;
            }
            
            $details[] = [
                'id_chitietsanpham' => $item['variant_id'] ?? $item['product']->id,
                'soluong' => $item['quantity'],
                'dongia' => $item['price'],
                'ghichu' => $item['variant_name'] ?? null,
            ];
        }
        return $details;
    }

    /**
     * Chuẩn bị chi tiết đơn hàng cho DonHangService
     */
    private function prepareOrderDetailsForService($cartItems)
    {
        $details = [];
        Log::info('Preparing order details for service:', ['cart_items_count' => count($cartItems)]);
        
        foreach ($cartItems as $index => $item) {
            Log::info("Processing cart item {$index}:", [
                'item' => $item,
                'has_product' => isset($item['product']),
                'product_type' => isset($item['product']) ? get_class($item['product']) : 'null'
            ]);
            
            // Kiểm tra product có tồn tại không
            if (!isset($item['product']) || !$item['product']) {
                Log::warning("Skipping item {$index} - no product found");
                continue;
            }
            
            $details[] = [
                'id_chitietsanpham' => $item['variant_id'] ?? $item['product']->id,
                'tensanpham' => $item['product']->tenSP ?? $item['product_name'],
                'dongia' => $item['price'],
                'soluong' => $item['quantity'],
                'thanhtien' => $item['price'] * $item['quantity'],
                'ghichu' => $item['variant_name'] ?? null,
            ];
        }
        
        Log::info('Order details prepared:', ['details_count' => count($details)]);
        return $details;
    }

    /**
     * Chuẩn bị voucher
     */
    private function prepareVouchers($voucherCode)
    {
        if (!$voucherCode) {
            return [];
        }

        $voucher = $this->voucherService->getVoucherByCode($voucherCode);
        if ($voucher) {
            return [['id_voucher' => $voucher->id]];
        }

        return [];
    }

    /**
     * Xử lý thanh toán online
     */
    private function processOnlinePayment($order, $paymentMethod)
    {
        // TODO: Implement payment gateway integration
        // Tạm thời return URL giả
        return route('payment.process', [
            'order_id' => $order->id,
            'method' => $paymentMethod
        ]);
    }

    /**
     * Tính toán giảm giá voucher
     */
    private function calculateVoucherDiscount($voucher, $cartTotal)
    {
        if ($voucher->loai_giam_gia === 'percent') {
            $discount = ($cartTotal * $voucher->gia_tri) / 100;
            if ($voucher->gia_tri_toi_da && $discount > $voucher->gia_tri_toi_da) {
                $discount = $voucher->gia_tri_toi_da;
            }
        } else {
            $discount = $voucher->gia_tri;
        }

        return min($discount, $cartTotal);
    }
}
