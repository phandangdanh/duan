<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankPaymentController extends Controller
{
    /**
     * Hiển thị thông tin chuyển khoản ngân hàng
     */
    public function showBankInfo($orderId)
    {
        try {
            $user = Auth::user();
            
            \Log::info('BankPaymentController showBankInfo called:', [
                'order_id' => $orderId,
                'user_id' => $user ? $user->id : 'not_logged_in'
            ]);
            
            // Lấy đơn hàng của user
            $order = DonHang::where('id', $orderId)
                ->where('id_user', $user->id)
                ->first();
            
            \Log::info('Order found:', [
                'order_id' => $orderId,
                'order_exists' => $order ? 'yes' : 'no',
                'payment_method' => $order ? $order->phuongthucthanhtoan : 'N/A'
            ]);
            
            if (!$order) {
                \Log::error('Order not found:', ['order_id' => $orderId, 'user_id' => $user->id]);
                return redirect()->route('orders.index')->with('error', 'Không tìm thấy đơn hàng!');
            }
            
            // Kiểm tra phương thức thanh toán
            if ($order->phuongthucthanhtoan !== 'banking') {
                \Log::error('Wrong payment method:', [
                    'order_id' => $orderId,
                    'expected' => 'banking',
                    'actual' => $order->phuongthucthanhtoan
                ]);
                return redirect()->route('orders.detail', $orderId)->with('error', 'Đơn hàng này không sử dụng chuyển khoản ngân hàng!');
            }
            
            return view('fontend.checkout.bank-info', compact('order'));
            
        } catch (\Exception $e) {
            \Log::error('BankPaymentController error:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải thông tin thanh toán!');
        }
    }
    
    
    /**
     * Xác nhận đã chuyển khoản
     */
    public function confirmPayment(Request $request, $orderId)
    {
        try {
            $user = Auth::user();
            
            // Lấy đơn hàng của user
            $order = DonHang::where('id', $orderId)
                ->where('id_user', $user->id)
                ->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng!'
                ]);
            }
            
            // Kiểm tra phương thức thanh toán
            if ($order->phuongthucthanhtoan !== 'chuyen_khoan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng này không sử dụng chuyển khoản ngân hàng!'
                ]);
            }
            
            // Cập nhật trạng thái đơn hàng
            $order->update([
                'trangthai' => 'cho_xac_nhan',
                'ghichu' => $order->ghichu . "\n[Đã chuyển khoản - Chờ xác nhận]"
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã gửi thông tin thanh toán! Shop sẽ xác nhận trong thời gian sớm nhất.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận thanh toán!'
            ]);
        }
    }
}
