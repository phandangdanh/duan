<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trangthai' => 'nullable|string|in:cho_xac_nhan,da_xac_nhan,dang_giao,da_giao,da_huy,hoan_tra',
            'hoten' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'sodienthoai' => 'nullable|string|max:20',
            'diachigiaohang' => 'nullable|string|max:500',
            'phuongthucthanhtoan' => 'nullable|string|in:cod,banking,momo,zalopay',
            'trangthaithanhtoan' => 'nullable|string|in:chua_thanh_toan,da_thanh_toan,hoan_tien',
            'ghichu' => 'nullable|string|max:1000',
            'nhanvien' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'trangthai.in' => 'Trạng thái đơn hàng không hợp lệ',
            
            'hoten.string' => 'Họ tên người nhận phải là chuỗi ký tự',
            'hoten.max' => 'Họ tên người nhận không được vượt quá 255 ký tự',
            
            'email.email' => 'Email người nhận không hợp lệ',
            'email.max' => 'Email người nhận không được vượt quá 255 ký tự',
            
            'sodienthoai.string' => 'Số điện thoại người nhận phải là chuỗi ký tự',
            'sodienthoai.max' => 'Số điện thoại người nhận không được vượt quá 20 ký tự',
            
            'diachigiaohang.string' => 'Địa chỉ giao hàng phải là chuỗi ký tự',
            'diachigiaohang.max' => 'Địa chỉ giao hàng không được vượt quá 500 ký tự',
            
            'phuongthucthanhtoan.in' => 'Phương thức thanh toán không hợp lệ',
            'trangthaithanhtoan.in' => 'Trạng thái thanh toán không hợp lệ',
            
            'ghichu.string' => 'Ghi chú phải là chuỗi ký tự',
            'ghichu.max' => 'Ghi chú không được vượt quá 1000 ký tự',
            
            'nhanvien.string' => 'Tên nhân viên phải là chuỗi ký tự',
            'nhanvien.max' => 'Tên nhân viên không được vượt quá 255 ký tự',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'trangthai' => 'trạng thái đơn hàng',
            'hoten' => 'họ tên người nhận',
            'email' => 'email người nhận',
            'sodienthoai' => 'số điện thoại người nhận',
            'diachigiaohang' => 'địa chỉ giao hàng',
            'phuongthucthanhtoan' => 'phương thức thanh toán',
            'trangthaithanhtoan' => 'trạng thái thanh toán',
            'ghichu' => 'ghi chú',
            'nhanvien' => 'tên nhân viên',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $orderId = $this->route('id');
            
            if ($orderId) {
                $order = \App\Models\DonHang::find($orderId);
                
                if (!$order) {
                    $validator->errors()->add('id', 'Đơn hàng không tồn tại');
                    return;
                }
                
                // Validate status transitions
                if ($this->has('trangthai')) {
                    $newStatus = $this->input('trangthai');
                    $currentStatus = $order->trangthai;
                    
                    // Define allowed status transitions
                    $allowedTransitions = [
                        'cho_xac_nhan' => ['da_xac_nhan', 'da_huy'],
                        'da_xac_nhan' => ['dang_giao', 'da_huy', 'cho_xac_nhan'], // Allow back to pending
                        'dang_giao' => ['da_giao', 'hoan_tra', 'da_xac_nhan'], // Allow back to confirmed
                        'da_giao' => ['hoan_tra', 'dang_giao'], // Allow back to shipping
                        'da_huy' => ['cho_xac_nhan'], // Allow reactivate
                        'hoan_tra' => ['da_giao'], // Allow back to delivered
                    ];
                    
                    // Allow same status (no change)
                    if ($newStatus === $currentStatus) {
                        // No error, allow same status
                    } elseif (isset($allowedTransitions[$currentStatus]) && 
                        !in_array($newStatus, $allowedTransitions[$currentStatus])) {
                        $validator->errors()->add(
                            'trangthai',
                            "Không thể chuyển từ trạng thái '{$this->getStatusText($currentStatus)}' sang '{$this->getStatusText($newStatus)}'"
                        );
                    }
                }
                
                // Validate payment status
                if ($this->has('trangthaithanhtoan')) {
                    $newPaymentStatus = $this->input('trangthaithanhtoan');
                    $currentPaymentStatus = $order->trangthaithanhtoan;
                    
                    // Define allowed payment status transitions
                    $allowedPaymentTransitions = [
                        'chua_thanh_toan' => ['da_thanh_toan', 'chua_thanh_toan'], // Allow same status
                        'da_thanh_toan' => ['hoan_tien', 'chua_thanh_toan'], // Allow back to unpaid
                        'hoan_tien' => ['da_thanh_toan'], // Allow back to paid
                    ];
                    
                    // Allow same status (no change)
                    if ($newPaymentStatus === $currentPaymentStatus) {
                        // No error, allow same status
                    } elseif (isset($allowedPaymentTransitions[$currentPaymentStatus]) && 
                        !in_array($newPaymentStatus, $allowedPaymentTransitions[$currentPaymentStatus])) {
                        $validator->errors()->add(
                            'trangthaithanhtoan',
                            "Không thể chuyển từ trạng thái thanh toán '{$this->getPaymentStatusText($currentPaymentStatus)}' sang '{$this->getPaymentStatusText($newPaymentStatus)}'"
                        );
                    }
                }
            }
        });
    }

    /**
     * Get status text
     */
    private function getStatusText(string $status): string
    {
        $statuses = [
            'cho_xac_nhan' => 'Chờ xác nhận',
            'da_xac_nhan' => 'Đã xác nhận',
            'dang_giao' => 'Đang giao',
            'da_giao' => 'Đã giao',
            'da_huy' => 'Đã hủy',
            'hoan_tra' => 'Hoàn trả',
        ];
        
        return $statuses[$status] ?? 'Không xác định';
    }

    /**
     * Get payment status text
     */
    private function getPaymentStatusText(string $status): string
    {
        $statuses = [
            'chua_thanh_toan' => 'Chưa thanh toán',
            'da_thanh_toan' => 'Đã thanh toán',
            'hoan_tien' => 'Hoàn tiền',
        ];
        
        return $statuses[$status] ?? 'Không xác định';
    }
}
