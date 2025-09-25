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
            // Debug: Log request data
            Log::info('Checkout processOrder request:', [
                'all_data' => $request->all(),
                'cart_data' => $request->input('cart_data'),
                'phuongthucthanhtoan' => $request->input('phuongthucthanhtoan')
            ]);
            
            // Validate dữ liệu
            try {
                $request->validate([
                    'hoten' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'sodienthoai' => 'required|string|max:20',
                    'diachigiaohang' => 'required|string|max:500',
                    'phuongthucthanhtoan' => 'required|string|in:cod,banking,momo,zalopay',
                    'ghichu' => 'nullable|string|max:1000',
                    'voucher_code' => 'nullable|string|max:50',
                    'cart_data' => 'required|string', // Cart data từ localStorage
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation failed:', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', array_flatten($e->errors())),
                    'errors' => $e->errors()
                ], 422);
            }

            // Lấy cart data từ localStorage
            $cartData = $request->input('cart_data');
            $cartItems = json_decode($cartData, true);
            
            if (!$cartItems || empty($cartItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng trống!'
                ], 400);
            }

            // Tính tổng tiền từ cart data
            $total = 0;
            foreach ($cartItems as $item) {
                $price = floatval($item['price'] ?? 0);
                $quantity = intval($item['quantity'] ?? 0);
                $total += $price * $quantity;
            }
            
            // Log để debug
            Log::info('Cart total calculation:', [
                'cart_items' => $cartItems,
                'calculated_total' => $total
            ]);

            // Chuẩn bị dữ liệu đơn hàng
            $orderData = [
                'id_user' => Auth::id() ?? 1, // Guest user ID = 1
                'hoten' => $request->hoten,
                'email' => $request->email,
                'sodienthoai' => $request->sodienthoai,
                'diachigiaohang' => $request->diachigiaohang,
                'phuongthucthanhtoan' => $request->phuongthucthanhtoan,
                'ghichu' => $request->ghichu,
                'chi_tiet_don_hang' => $this->prepareOrderDetailsFromLocalStorage($cartItems),
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
                'chi_tiet_don_hang' => $this->prepareOrderDetailsFromLocalStorageForService($cartItems),
                'vouchers' => $orderData['vouchers'],
            ];

            // Kiểm tra tồn kho trước khi tạo đơn hàng (tạm thời bỏ qua vì đã check ở frontend)
            $inventoryWarnings = [];

            // Tạo đơn hàng
            Log::info('Creating order with data:', $orderDataForService);
            $order = $this->orderService->createOrder($orderDataForService);
            Log::info('Order created:', ['order_id' => $order ? $order->id : 'null', 'order_type' => get_class($order)]);

            if ($order) {
                // Cart sẽ được xóa ở frontend sau khi đơn hàng thành công
                Log::info('Order created successfully:', [
                    'order_id' => $order->id,
                    'payment_method' => $request->phuongthucthanhtoan,
                    'order_status' => $order->trangthai
                ]);

                // Xử lý thanh toán
                if ($request->phuongthucthanhtoan === 'cod') {
                    // COD - tạm thời bỏ qua kiểm tra inventory vì đã check ở frontend
                    
                    $response = [
                        'success' => true,
                        'message' => 'Đặt hàng thành công! Bạn sẽ thanh toán khi nhận hàng.',
                        'order_id' => $order->id,
                        'redirect_url' => route('checkout.success', $order->id)
                    ];
                    
                    // Thêm cảnh báo tồn kho nếu có
                    if (!empty($inventoryWarnings)) {
                        $response['warnings'] = $inventoryWarnings;
                        $response['message'] .= ' Lưu ý: ' . implode('; ', $inventoryWarnings);
                    }
                    
                    return response()->json($response);
                } else {
                    // Thanh toán online - KHÔNG trừ tồn kho, chờ thanh toán thành công
                    Log::info('Processing online payment:', [
                        'order_id' => $order->id,
                        'payment_method' => $request->phuongthucthanhtoan
                    ]);
                    
                    $redirectUrl = $this->processOnlinePayment($order, $request->phuongthucthanhtoan);
                    
                    Log::info('Payment redirect URL:', [
                        'order_id' => $order->id,
                        'redirect_url' => $redirectUrl
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Đang chuyển hướng đến trang thanh toán...',
                        'order_id' => $order->id,
                        'redirect_url' => $redirectUrl
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
                'voucher_code' => 'required|string|max:50',
                'total_amount' => 'required|numeric|min:0' // Total amount từ localStorage
            ]);

            $voucher = $this->voucherService->getVoucherByCode($request->voucher_code);
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã voucher không hợp lệ!'
                ]);
            }

            $cartTotal = $request->input('total_amount');
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
     * Chuẩn bị chi tiết đơn hàng từ localStorage
     */
    private function prepareOrderDetailsFromLocalStorage($cartItems)
    {
        $details = [];
        foreach ($cartItems as $item) {
            $details[] = [
                'id_chitietsanpham' => $item['variant_id'] ?? $item['product_id'],
                'soluong' => $item['quantity'],
                'dongia' => $item['price'],
                'ghichu' => $item['variant_name'] ?? null,
            ];
        }
        return $details;
    }

    /**
     * Chuẩn bị chi tiết đơn hàng cho DonHangService từ localStorage
     */
    private function prepareOrderDetailsFromLocalStorageForService($cartItems)
    {
        $details = [];
        Log::info('Preparing order details for service from localStorage:', ['cart_items_count' => count($cartItems)]);
        
        foreach ($cartItems as $index => $item) {
            Log::info("Processing cart item {$index}:", [
                'item' => $item,
                'product_id' => $item['product_id'] ?? 'null',
                'variant_id' => $item['variant_id'] ?? 'null',
                'name' => $item['name'] ?? 'null'
            ]);
            
            // Nếu là sản phẩm chính (variant_id = 0), tìm hoặc tạo ChiTietSanPham
            // Nếu là variant, sử dụng variant_id
            if ($item['variant_id'] && $item['variant_id'] != 0) {
                $id_chitietsanpham = $item['variant_id'];
            } else {
                // Tìm ChiTietSanPham cho sản phẩm chính
                $chiTietSanPham = \App\Models\ChiTietSanPham::where('id_sp', $item['product_id'])
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($chiTietSanPham) {
                    $id_chitietsanpham = $chiTietSanPham->id;
                } else {
                    // Tạo ChiTietSanPham mới cho sản phẩm chính
                    $sanPham = \App\Models\SanPham::find($item['product_id']);
                    if ($sanPham) {
                        $id_chitietsanpham = \App\Models\ChiTietSanPham::create([
                            'id_sp' => $item['product_id'],
                            'id_mausac' => null,
                            'id_size' => null,
                            'soLuong' => $sanPham->soLuong ?? 0,
                            'tenSp' => $sanPham->tenSP,
                            'gia' => $sanPham->base_price ?? 0,
                            'gia_khuyenmai' => $sanPham->base_sale_price ?? 0,
                        ])->id;
                    } else {
                        throw new \Exception("Không tìm thấy sản phẩm với ID: " . $item['product_id']);
                    }
                }
            }
            
            Log::info('Processing cart item:', [
                'variant_id' => $item['variant_id'] ?? 'null',
                'product_id' => $item['product_id'] ?? 'null',
                'final_id_chitietsanpham' => $id_chitietsanpham,
                'name' => $item['name'] ?? 'null'
            ]);
            
            $details[] = [
                'id_chitietsanpham' => $id_chitietsanpham,
                'tensanpham' => $item['name'] ?? 'Sản phẩm',
                'dongia' => $item['price'],
                'soluong' => $item['quantity'],
                'thanhtien' => $item['price'] * $item['quantity'],
                'ghichu' => $item['variant_name'] ?? null,
            ];
        }
        
        Log::info('Order details prepared from localStorage:', ['details_count' => count($details)]);
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
        if ($paymentMethod === 'banking') {
            return route('bank.payment.info', $order->id);
        }
        
        // Cho các phương thức khác, redirect đến trang success
        return route('checkout.success', $order->id);
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
