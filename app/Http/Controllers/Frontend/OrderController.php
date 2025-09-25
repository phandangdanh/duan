<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\User;
use App\Services\DonHangService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $donHangService;

    public function __construct(DonHangService $donHangService)
    {
        $this->donHangService = $donHangService;
    }
    /**
     * Hiển thị danh sách đơn hàng của user
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Lấy đơn hàng của user
            $orders = DonHang::where('id_user', $user->id)
                ->with(['chiTietDonHang.chiTietSanPham'])
                ->orderBy('ngaytao', 'desc')
                ->paginate(10);
            
            return view('fontend.orders.index', compact('orders'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải danh sách đơn hàng!');
        }
    }
    
    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            // Lấy đơn hàng của user
            $order = DonHang::where('id', $id)
                ->where('id_user', $user->id)
                ->with(['chiTietDonHang.chiTietSanPham', 'user'])
                ->first();
            
            if (!$order) {
                return redirect()->route('orders.index')->with('error', 'Không tìm thấy đơn hàng!');
            }
            
            return view('fontend.orders.detail', compact('order'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải chi tiết đơn hàng!');
        }
    }
    
    /**
     * Hủy đơn hàng
     */
    public function cancel($id)
    {
        try {
            $user = Auth::user();
            
            $order = DonHang::where('id', $id)
                ->where('id_user', $user->id)
                ->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng!'
                ], 404);
            }
            
            // Chỉ cho phép hủy đơn hàng ở trạng thái chờ xác nhận hoặc đã xác nhận
            if (!in_array($order->trangthai, [DonHang::TRANGTHAI_CHO_XAC_NHAN, DonHang::TRANGTHAI_DA_XAC_NHAN])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hủy đơn hàng ở trạng thái này!'
                ], 400);
            }
            
            // Cập nhật trạng thái đơn hàng
            $order->update([
                'trangthai' => DonHang::TRANGTHAI_DA_HUY,
                'ngaycapnhat' => now()
            ]);

            // Hoàn lại tồn kho
            $this->donHangService->restoreInventory($order->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được hủy thành công!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy đơn hàng!'
            ], 500);
        }
    }
    
    /**
     * Mua lại đơn hàng
     */
    public function reorder($id)
    {
        try {
            $user = Auth::user();
            
            $order = DonHang::where('id', $id)
                ->where('id_user', $user->id)
                ->with('chiTietDonHang')
                ->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng!'
                ], 404);
            }
            
            // Thêm sản phẩm vào giỏ hàng
            $cart = session()->get('cart', []);
            
            foreach ($order->chiTietDonHang as $item) {
                $cartKey = $item->id_chitietsanpham;
                
                if (isset($cart[$cartKey])) {
                    $cart[$cartKey]['quantity'] += $item->soluong;
                } else {
                    $cart[$cartKey] = [
                        'id_chitietsanpham' => $item->id_chitietsanpham,
                        'quantity' => $item->soluong,
                        'price' => $item->dongia,
                        'product_name' => $item->tensanpham,
                        'image' => 'backend/img/p1.jpg' // Default image
                    ];
                }
            }
            
            session()->put('cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'redirect_url' => route('cart')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi mua lại đơn hàng!'
            ], 500);
        }
    }
}
