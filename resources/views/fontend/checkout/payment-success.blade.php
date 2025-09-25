@extends('fontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Chuyển khoản thành công!
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Success Message -->
                    <div class="text-center mb-4">
                        <div class="success-icon mb-3">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        <h3 class="text-success">🎉 Chuyển khoản đã được xác nhận!</h3>
                        <p class="text-muted">Cảm ơn bạn đã thanh toán. Đơn hàng của bạn sẽ được xử lý ngay.</p>
                    </div>

                    <!-- Order Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-receipt me-2"></i>Thông tin đơn hàng</h6>
                                    <p><strong>Mã đơn hàng:</strong> {{ $order->id ?? 'DH' . time() }}</p>
                                    <p><strong>Tổng tiền:</strong> <span class="text-danger fw-bold">{{ number_format($order->tongtien ?? 500000, 0, ',', '.') }}₫</span></p>
                                    <p><strong>Phương thức:</strong> Chuyển khoản ngân hàng</p>
                                    <p><strong>Thời gian:</strong> {{ date('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-truck me-2"></i>Thông tin giao hàng</h6>
                                    <p><strong>Khách hàng:</strong> {{ auth()->user()->ten ?? 'Khách hàng' }}</p>
                                    <p><strong>SĐT:</strong> {{ auth()->user()->sdt ?? '0123456789' }}</p>
                                    <p><strong>Địa chỉ:</strong> {{ auth()->user()->diachi ?? '123 Đường ABC, Quận 1, TP.HCM' }}</p>
                                    <p><strong>Dự kiến giao:</strong> 2-3 ngày làm việc</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Những bước tiếp theo:</h6>
                        <ol class="mb-0">
                            <li><strong>Xác nhận đơn hàng:</strong> Chúng tôi sẽ gọi điện xác nhận trong vòng 30 phút</li>
                            <li><strong>Chuẩn bị hàng:</strong> Đơn hàng sẽ được chuẩn bị và đóng gói</li>
                            <li><strong>Giao hàng:</strong> Nhân viên giao hàng sẽ liên hệ trước khi đến</li>
                            <li><strong>Nhận hàng:</strong> Kiểm tra hàng hóa trước khi thanh toán (nếu COD)</li>
                        </ol>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        <a href="{{ route('orders.detail', $order->id ?? 1) }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-eye me-2"></i>Xem chi tiết đơn hàng
                        </a>
                        <a href="{{ route('checkout.check-payment') }}" class="btn btn-info btn-lg me-3">
                            <i class="fas fa-search me-2"></i>Kiểm tra trạng thái
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home me-2"></i>Về trang chủ
                        </a>
                    </div>

                    <!-- Contact Support -->
                    <div class="mt-4">
                        <h6><i class="fas fa-headset me-2"></i>Cần hỗ trợ?</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                                    <p><strong>Hotline</strong></p>
                                    <a href="tel:0379559690" class="btn btn-outline-primary btn-sm">0379559690</a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fab fa-facebook fa-2x text-primary mb-2"></i>
                                    <p><strong>Facebook</strong></p>
                                    <a href="https://facebook.com" class="btn btn-outline-primary btn-sm">Nhắn tin</a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fab fa-telegram fa-2x text-primary mb-2"></i>
                                    <p><strong>Zalo</strong></p>
                                    <a href="https://zalo.me/0379559690" class="btn btn-outline-primary btn-sm">0379559690</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Payment Notice -->
                    <div class="mt-4">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-flask me-2"></i>Đây là trang test</h6>
                            <p class="mb-0">Trang này mô phỏng thông báo chuyển khoản thành công. Trong thực tế, bạn sẽ nhận được thông báo này sau khi chuyển khoản thật.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.btn-success-page {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success-page:hover {
    background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40,167,69,0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto redirect after 10 seconds (optional)
    // setTimeout(() => {
    //     window.location.href = '/orders/detail/{{ $order->id ?? 1 }}';
    // }, 10000);

    // Show success animation
    const successIcon = document.querySelector('.success-icon');
    if (successIcon) {
        successIcon.style.opacity = '0';
        successIcon.style.transform = 'scale(0.5)';
        
        setTimeout(() => {
            successIcon.style.transition = 'all 0.5s ease';
            successIcon.style.opacity = '1';
            successIcon.style.transform = 'scale(1)';
        }, 500);
    }

    // Add confetti effect (optional)
    // You can add confetti library here if needed
});
</script>
@endsection
