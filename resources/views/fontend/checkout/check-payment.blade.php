@extends('fontend.layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- FontAwesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
.payment-status-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid #007bff;
    margin-bottom: 20px;
}

.status-success {
    border-left-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
}

.status-pending {
    border-left-color: #ffc107;
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
}

.status-failed {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
}

.status-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.btn-check-status {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-check-status:hover {
    background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(23,162,184,0.3);
}

.btn-check-status:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #17a2b8;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23,162,184,0.25);
}

.card {
    border: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border: none;
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border: none;
}

.btn-info:hover {
    background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
}

/* Validation styles */
.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220,53,69,0.25);
}

.form-control.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        Kiểm tra trạng thái chuyển khoản
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="mb-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Nhập thông tin để kiểm tra</h6>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Nếu thấy "Không xác định", vui lòng refresh trang (F5) để cập nhật code mới.
                        </div>
                        <form id="check-payment-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="order_id" class="form-label">Mã đơn hàng</label>
                                        <input type="text" class="form-control" id="order_id" 
                                               placeholder="Nhập mã đơn hàng (VD: DH123456)" 
                                               value="{{ $order->id ?? '' }}" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" id="phone" 
                                               placeholder="Nhập số điện thoại đặt hàng"
                                               value="{{ auth()->user()->sdt ?? '' }}" 
                                               required>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-info btn-lg" id="check-btn">
                                    <i class="fas fa-search me-2"></i>Kiểm tra trạng thái
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Loading State -->
                    <div id="loading-state" class="text-center" style="display: none;">
                        <div class="spinner-border text-info mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Đang kiểm tra trạng thái chuyển khoản...</p>
                    </div>

                    <!-- Payment Status Result -->
                    <div id="payment-result" style="display: none;">
                        <!-- Result will be populated by JavaScript -->
                    </div>


                    <!-- Instructions -->
                    <div class="mt-4">
                        <h6><i class="fas fa-lightbulb me-2"></i>Hướng dẫn</h6>
                        <div class="alert alert-light">
                            <ul class="mb-0">
                                <li><strong>Chưa chuyển khoản:</strong> Vui lòng thực hiện chuyển khoản theo thông tin bên trên</li>
                                <li><strong>Đã chuyển khoản:</strong> Đơn hàng sẽ được xử lý và giao hàng</li>
                                <li><strong>Cần hỗ trợ:</strong> Liên hệ hotline 0379559690</li>
                            </ul>
                        </div>
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
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('check-payment-form');
    const checkBtn = document.getElementById('check-btn');
    const loadingState = document.getElementById('loading-state');
    const paymentResult = document.getElementById('payment-result');

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        checkPaymentStatus();
    });


    // Check payment status function
    function checkPaymentStatus() {
        const orderId = document.getElementById('order_id').value.trim();
        const phone = document.getElementById('phone').value.trim();

        // Clear previous validation states
        document.getElementById('order_id').classList.remove('is-invalid', 'is-valid');
        document.getElementById('phone').classList.remove('is-invalid', 'is-valid');

        if (!orderId) {
            document.getElementById('order_id').classList.add('is-invalid');
            displayError('Vui lòng nhập mã đơn hàng');
            document.getElementById('order_id').focus();
            return;
        }

        if (!phone) {
            document.getElementById('phone').classList.add('is-invalid');
            displayError('Vui lòng nhập số điện thoại');
            document.getElementById('phone').focus();
            return;
        }

        // Mark fields as valid
        document.getElementById('order_id').classList.add('is-valid');
        document.getElementById('phone').classList.add('is-valid');

        // Show loading state
        checkBtn.disabled = true;
        checkBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang kiểm tra...';
        loadingState.style.display = 'block';
        paymentResult.style.display = 'none';

        // Make real API call to check payment status
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        
        fetch('/api/orders/check-payment?_t=' + Date.now(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Authorization': token ? `Bearer ${token}` : ''
            },
            body: JSON.stringify({
                order_id: orderId,
                phone: phone
            })
        })
        .then(response => {
            if (response.status === 401) {
                displayError('Vui lòng đăng nhập để kiểm tra trạng thái thanh toán');
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            
            if (data.success) {
                displayPaymentStatus(data.order);
            } else {
                let errorMessage = data.message || 'Không tìm thấy thông tin đơn hàng';
                
                // Add debug info if available
                if (data.debug) {
                    errorMessage += `<br><br><strong>Thông tin debug:</strong><br>`;
                    errorMessage += `• Mã đơn hàng tìm kiếm: ${data.debug.searched_order_id}<br>`;
                    errorMessage += `• Số điện thoại tìm kiếm: ${data.debug.searched_phone}<br>`;
                    errorMessage += `• Đơn hàng có tồn tại: ${data.debug.order_exists ? 'Có' : 'Không'}<br>`;
                    if (data.debug.actual_phone) {
                        errorMessage += `• Số điện thoại thực tế: ${data.debug.actual_phone}<br>`;
                    }
                    errorMessage += `• Gợi ý: ${data.debug.suggestion}`;
                }
                
                displayError(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            displayError('Có lỗi xảy ra khi kiểm tra trạng thái thanh toán');
        })
        .finally(() => {
            // Hide loading state
            loadingState.style.display = 'none';
            checkBtn.disabled = false;
            checkBtn.innerHTML = '<i class="fas fa-search me-2"></i>Kiểm tra trạng thái';
        });
    }


    // Display error message
    function displayError(message) {
        paymentResult.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Lỗi:</strong> ${message}
            </div>
        `;
        paymentResult.style.display = 'block';
    }

    // Get mock payment status based on order ID (fallback)
    function getMockPaymentStatus(orderId) {
        // Simple logic: if order ID contains 'TEST' or ends with even number, it's successful
        const isSuccess = orderId.includes('TEST') || parseInt(orderId.slice(-1)) % 2 === 0;
        
        return {
            id: orderId,
            tongtien: 500000,
            status: isSuccess ? 1 : 0, // 1 = success, 0 = pending
            ngaytao: new Date().toISOString(),
            tenkhachhang: 'Nguyễn Văn A',
            sdt: '0123456789',
            diachi: '123 Đường ABC, Quận 1, TP.HCM',
            payment_method: 'bank_transfer',
            transaction_id: isSuccess ? 'TXN' + Date.now() : null,
            payment_time: isSuccess ? new Date().toISOString() : null
        };
    }

    // Display payment status
    function displayPaymentStatus(orderData) {
        const status = orderData.status;
        
        // Debug log
        console.log('Order data:', orderData);
        console.log('Status value:', status, 'Type:', typeof status);
        
        const statusText = getStatusText(status);
        const statusClass = getStatusClass(status);
        const statusIcon = getStatusIcon(status);

        paymentResult.innerHTML = `
            <div class="payment-status-card ${statusClass}">
                <div class="text-center">
                    <div class="status-icon">${statusIcon}</div>
                    <h4>${statusText}</h4>
                    <p class="mb-3">Đơn hàng: <strong>${orderData.id}</strong></p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tổng tiền:</strong> ${formatCurrency(orderData.tongtien)}</p>
                        <p><strong>Ngày đặt:</strong> ${formatDate(orderData.ngaytao)}</p>
                        <p><strong>Trạng thái:</strong> ${statusText}</p>
                        ${orderData.transaction_id ? `<p><strong>Mã giao dịch:</strong> ${orderData.transaction_id}</p>` : ''}
                    </div>
                    <div class="col-md-6">
                        <p><strong>Khách hàng:</strong> ${orderData.tenkhachhang}</p>
                        <p><strong>SĐT:</strong> ${orderData.sdt}</p>
                        <p><strong>Địa chỉ:</strong> ${orderData.diachi}</p>
                        ${orderData.payment_time ? `<p><strong>Thời gian thanh toán:</strong> ${formatDate(orderData.payment_time)}</p>` : ''}
                    </div>
                </div>

                ${getStatusActions(status, orderData)}
            </div>
        `;

        paymentResult.style.display = 'block';
    }

    // Helper functions
    function getStatusText(status) {
        // Convert to number if it's a string
        const numStatus = parseInt(status);
        
        const statusMap = {
            0: 'Chưa chuyển khoản',
            1: 'Đã chuyển khoản thành công',
            2: 'Chuyển khoản thất bại'
        };
        
        // Handle string status values
        if (status === 'chua_thanh_toan' || status === '0') {
            return 'Chưa chuyển khoản';
        } else if (status === 'da_thanh_toan' || status === '1') {
            return 'Đã chuyển khoản thành công';
        } else if (status === 'that_bai' || status === '2') {
            return 'Chuyển khoản thất bại';
        }
        
        return statusMap[numStatus] || 'Chưa chuyển khoản'; // Default to pending instead of unknown
    }

    function getStatusClass(status) {
        // Handle string status values
        if (status === 'chua_thanh_toan' || status === '0' || status === 0) {
            return 'status-pending';
        } else if (status === 'da_thanh_toan' || status === '1' || status === 1) {
            return 'status-success';
        } else if (status === 'that_bai' || status === '2' || status === 2) {
            return 'status-failed';
        }
        
        const classMap = {
            0: 'status-pending',
            1: 'status-success',
            2: 'status-failed'
        };
        return classMap[parseInt(status)] || 'status-pending';
    }

    function getStatusIcon(status) {
        // Handle string status values
        if (status === 'chua_thanh_toan' || status === '0' || status === 0) {
            return '<i class="fas fa-clock text-warning"></i>';
        } else if (status === 'da_thanh_toan' || status === '1' || status === 1) {
            return '<i class="fas fa-check-circle text-success"></i>';
        } else if (status === 'that_bai' || status === '2' || status === 2) {
            return '<i class="fas fa-times-circle text-danger"></i>';
        }
        
        const iconMap = {
            0: '<i class="fas fa-clock text-warning"></i>',
            1: '<i class="fas fa-check-circle text-success"></i>',
            2: '<i class="fas fa-times-circle text-danger"></i>'
        };
        return iconMap[parseInt(status)] || '<i class="fas fa-clock text-warning"></i>';
    }

    function getStatusActions(status, orderData) {
        switch(status) {
            case 0: // Chưa chuyển khoản
                return `
                    <div class="text-center mt-3">
                        <a href="/checkout/bank-info/{{ $order->id ?? '' }}" class="btn btn-primary me-2">
                            <i class="fas fa-university me-2"></i>Xem thông tin chuyển khoản
                        </a>
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync me-2"></i>Làm mới
                        </button>
                    </div>
                `;
            case 1: // Đã chuyển khoản thành công
                return `
                    <div class="text-center mt-3">
                        <a href="/orders/detail/${orderData.id}" class="btn btn-success me-2">
                            <i class="fas fa-eye me-2"></i>Xem đơn hàng
                        </a>
                        <button class="btn btn-outline-primary" onclick="contactSupport()">
                            <i class="fas fa-headset me-2"></i>Liên hệ hỗ trợ
                        </button>
                    </div>
                `;
            case 2: // Chuyển khoản thất bại
                return `
                    <div class="text-center mt-3">
                        <a href="/checkout/bank-info/{{ $order->id ?? '' }}" class="btn btn-warning me-2">
                            <i class="fas fa-redo me-2"></i>Thử lại chuyển khoản
                        </a>
                        <button class="btn btn-outline-secondary" onclick="contactSupport()">
                            <i class="fas fa-headset me-2"></i>Liên hệ hỗ trợ
                        </button>
                    </div>
                `;
            default:
                return `
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary" onclick="contactSupport()">
                            <i class="fas fa-headset me-2"></i>Liên hệ hỗ trợ
                        </button>
                    </div>
                `;
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Global functions
    window.contactSupport = function() {
        // Scroll to contact section
        document.querySelector('.mt-4').scrollIntoView({ behavior: 'smooth' });
    };
});
</script>

@push('scripts')
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection
