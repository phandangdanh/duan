@extends('fontend.layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('css')
<style>
.order-detail-container {
  max-width: 1000px;
  margin: 0 auto;
}
.order-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px;
  border-radius: 15px;
  margin-bottom: 30px;
}
.status-badge {
  padding: 8px 20px;
  border-radius: 25px;
  font-size: 14px;
  font-weight: bold;
}
.status-pending { background: #fff3cd; color: #856404; }
.status-confirmed { background: #d1ecf1; color: #0c5460; }
.status-shipping { background: #d4edda; color: #155724; }
.status-delivered { background: #d1ecf1; color: #0c5460; }
.status-cancelled { background: #f8d7da; color: #721c24; }

/* Payment Status Styles */
.payment-status-display {
  margin-top: 8px;
}

.payment-status-display .badge {
  font-size: 14px;
  padding: 8px 12px;
  border-radius: 20px;
  font-weight: 600;
}

.payment-status-display .badge.bg-success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
  color: white;
}

.payment-status-display .badge.bg-warning {
  background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
  color: white;
}

.payment-status-display .badge.bg-danger {
  background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
  color: white;
}

.payment-status-display .badge.bg-info {
  background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;
  color: white;
}

.payment-status-display .badge.bg-secondary {
  background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
  color: white;
}

.alert-sm {
  padding: 8px 12px;
  font-size: 13px;
  border-radius: 8px;
  margin-bottom: 0;
}
.order-item {
  display: flex;
  align-items: center;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 10px;
  margin-bottom: 15px;
}
.order-item img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  margin-right: 20px;
}
.timeline {
  position: relative;
  padding-left: 30px;
}
.timeline::before {
  content: '';
  position: absolute;
  left: 15px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e0e0e0;
}
.timeline-item {
  position: relative;
  margin-bottom: 30px;
}
.timeline-item::before {
  content: '';
  position: absolute;
  left: -22px;
  top: 5px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #667eea;
  border: 3px solid white;
  box-shadow: 0 0 0 3px #e0e0e0;
}
.timeline-item.completed::before {
  background: #28a745;
}
.timeline-item.current::before {
  background: #ffc107;
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
  70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
  100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}

/* Enhanced Styles */
.order-detail-container {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.order-header {
  position: relative;
  overflow: hidden;
}

.order-header::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
  animation: shimmer 3s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
  100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.card {
  border: none;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.order-item {
  transition: all 0.3s ease;
  border-radius: 8px;
}

.order-item:hover {
  background: #f8f9fa;
  transform: translateX(5px);
}

.status-badge {
  position: relative;
  z-index: 1;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.timeline-item {
  transition: all 0.3s ease;
}

.timeline-item:hover {
  transform: translateX(5px);
}

.timeline-content {
  transition: all 0.3s ease;
}

.timeline-content:hover {
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

/* Responsive improvements */
@media (max-width: 768px) {
  .order-header .row {
    flex-direction: column;
    text-align: center;
  }
  
  .order-header .text-end {
    text-align: center !important;
    margin-top: 15px;
  }
  
  .timeline {
    padding-left: 20px;
  }
  
  .timeline-item {
    padding-left: 15px;
  }
  
  .timeline-marker {
    left: -17px;
    width: 25px;
    height: 25px;
  }
}
</style>
@endsection

@section('content')
<div class="order-detail-container py-5">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <!-- Order Header -->
        <div class="order-header">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h2 class="mb-2">
                <i class="fas fa-receipt me-2"></i>Đơn hàng #{{ $order->id }}
              </h2>
              <p class="mb-0 opacity-75">
                <i class="fas fa-calendar me-2"></i>Đặt hàng lúc: {{ $order->ngaytao ? $order->ngaytao->format('d/m/Y H:i') : 'N/A' }}
              </p>
            </div>
            <div class="col-md-4 text-end">
              <span class="status-badge status-{{ $order->trangthai }}">{{ $order->trang_thai_text }}</span>
              <div class="mt-3">
                <h3 class="mb-0">{{ number_format($order->tongtien, 0, ',', '.') }}₫</h3>
                <small class="opacity-75">Tổng thanh toán</small>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <!-- Order Items -->
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fas fa-shopping-bag me-2"></i>Sản phẩm đã đặt
                </h5>
              </div>
              <div class="card-body">
                @if($order->chiTietDonHang && $order->chiTietDonHang->count() > 0)
                  @foreach($order->chiTietDonHang as $item)
                  <div class="order-item">
                    @php
                      $productId = $item->chiTietSanPham->id_sp ?? null;
                      $product = $productId ? \App\Models\SanPham::with('hinhanh')->find($productId) : null;
                      $firstImage = $product && $product->hinhanh->count() > 0 ? $product->hinhanh->first() : null;
                    @endphp
                    
                    @if($firstImage)
                      <img src="{{ $firstImage->url ? url($firstImage->url) : '/fontend/img/aosomi1.png' }}" 
                           alt="{{ $item->tensanpham }}" 
                           class="rounded me-3" 
                           style="width: 80px; height: 80px; object-fit: cover;"
                           onerror="this.src='/fontend/img/aosomi1.png'">
                    @else
                      <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                           style="width: 80px; height: 80px;">
                        <i class="fas fa-box text-muted"></i>
                      </div>
                    @endif
                    <div class="flex-grow-1">
                      <h5 class="mb-1">{{ $item->tensanpham }}</h5>
                      <p class="text-muted mb-1">Sản phẩm</p>
                      <small class="text-muted">
                        <i class="fas fa-tag me-1"></i>{{ number_format($item->dongia, 0, ',', '.') }}₫
                      </small>
                    </div>
                    <div class="text-end">
                      <div class="mb-2">
                        <span class="badge bg-primary fs-6">x{{ $item->soluong }}</span>
                      </div>
                      <h5 class="mb-0 text-danger">{{ number_format($item->thanhtien, 0, ',', '.') }}₫</h5>
                    </div>
                  </div>
                  @endforeach
                @else
                  <div class="text-center py-4">
                    <i class="fas fa-box text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Không có sản phẩm nào</p>
                  </div>
                @endif
              </div>
            </div>
            
            <!-- Order Summary -->
            <div class="card mt-4">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fas fa-calculator me-2"></i>Tổng kết đơn hàng
                </h5>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                  <span>Tạm tính:</span>
                  <span>{{ number_format($order->tongtien, 0, ',', '.') }}₫</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span>Giảm giá:</span>
                  <span class="text-success">0₫</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span>Phí vận chuyển:</span>
                  <span class="text-success">Miễn phí</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                  <strong>Tổng cộng:</strong>
                  <strong class="text-danger fs-5">{{ number_format($order->tongtien, 0, ',', '.') }}₫</strong>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Order Status & Info -->
          <div class="col-md-4">
            <!-- Order Status Timeline -->
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fas fa-truck me-2"></i>Trạng thái đơn hàng
                </h5>
              </div>
              <div class="card-body">
                <div class="timeline">
                  <div class="timeline-item completed">
                    <h6 class="mb-1">Đặt hàng thành công</h6>
                    <small class="text-muted">15/01/2024 10:30</small>
                  </div>
                  <div class="timeline-item completed">
                    <h6 class="mb-1">Đã xác nhận</h6>
                    <small class="text-muted">15/01/2024 11:00</small>
                  </div>
                  <div class="timeline-item current">
                    <h6 class="mb-1">Đang chuẩn bị hàng</h6>
                    <small class="text-muted">15/01/2024 14:00</small>
                  </div>
                  <div class="timeline-item">
                    <h6 class="mb-1">Đang giao hàng</h6>
                    <small class="text-muted">Chưa cập nhật</small>
                  </div>
                  <div class="timeline-item">
                    <h6 class="mb-1">Giao hàng thành công</h6>
                    <small class="text-muted">Chưa cập nhật</small>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Shipping Info -->
            <div class="card mt-4">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fas fa-map-marker-alt me-2"></i>Thông tin giao hàng
                </h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <strong>Người nhận:</strong><br>
                  {{ $order->hoten }}<br>
                  <small class="text-muted">{{ $order->sodienthoai }}</small>
                </div>
                <div class="mb-3">
                  <strong>Địa chỉ:</strong><br>
                  <small>{{ trim($order->diachigiaohang, ', ') }}</small>
                </div>
                <div class="mb-3">
                  <strong>Phương thức thanh toán:</strong><br>
                  <span class="badge bg-success">
                    @if($order->phuongthucthanhtoan === 'cod')
                      Thanh toán khi nhận hàng
                    @elseif($order->phuongthucthanhtoan === 'banking')
                      Chuyển khoản ngân hàng
                    @elseif($order->phuongthucthanhtoan === 'momo')
                      Ví MoMo
                    @elseif($order->phuongthucthanhtoan === 'zalopay')
                      ZaloPay
                    @else
                      {{ ucfirst($order->phuongthucthanhtoan) }}
                    @endif
                  </span>
                </div>
                
                <!-- Trạng thái thanh toán -->
                <div class="mb-3">
                  <strong>Trạng thái thanh toán:</strong><br>
                  @php
                    $paymentStatus = $order->trangthaithanhtoan ?? 0;
                    $paymentStatusText = '';
                    $paymentStatusClass = '';
                    $paymentStatusIcon = '';
                    
                    switch($paymentStatus) {
                      case 0:
                        $paymentStatusText = 'Chưa thanh toán';
                        $paymentStatusClass = 'bg-warning';
                        $paymentStatusIcon = 'fas fa-clock';
                        break;
                      case 1:
                        $paymentStatusText = 'Đã thanh toán thành công';
                        $paymentStatusClass = 'bg-success';
                        $paymentStatusIcon = 'fas fa-check-circle';
                        break;
                      case 2:
                        $paymentStatusText = 'Thanh toán thất bại';
                        $paymentStatusClass = 'bg-danger';
                        $paymentStatusIcon = 'fas fa-times-circle';
                        break;
                      case 3:
                        $paymentStatusText = 'Đang xử lý thanh toán';
                        $paymentStatusClass = 'bg-info';
                        $paymentStatusIcon = 'fas fa-spinner';
                        break;
                      default:
                        $paymentStatusText = 'Chưa có thông tin thanh toán';
                        $paymentStatusClass = 'bg-secondary';
                        $paymentStatusIcon = 'fas fa-exclamation-triangle';
                    }
                  @endphp
                  
                  <div class="payment-status-display">
                    <span class="badge {{ $paymentStatusClass }} fs-6">
                      <i class="{{ $paymentStatusIcon }} me-1"></i>
                      {{ $paymentStatusText }}
                    </span>
                    
                    <!-- Thông tin giải thích chi tiết -->
                    <div class="mt-2">
                      @if($paymentStatus == 0)
                        <small class="text-muted">
                          <i class="fas fa-info-circle me-1"></i>
                          <strong>Chưa thanh toán:</strong> Đơn hàng chưa được thanh toán. Vui lòng thực hiện thanh toán để đơn hàng được xử lý.
                        </small>
                      @elseif($paymentStatus == 1)
                        <small class="text-success">
                          <i class="fas fa-check-circle me-1"></i>
                          <strong>Thanh toán thành công:</strong> Đơn hàng đã được thanh toán và sẽ được xử lý giao hàng.
                        </small>
                      @elseif($paymentStatus == 2)
                        <small class="text-danger">
                          <i class="fas fa-times-circle me-1"></i>
                          <strong>Thanh toán thất bại:</strong> Giao dịch thanh toán không thành công. Vui lòng thử lại hoặc liên hệ hỗ trợ.
                        </small>
                      @elseif($paymentStatus == 3)
                        <small class="text-info">
                          <i class="fas fa-spinner me-1"></i>
                          <strong>Đang xử lý:</strong> Giao dịch đang được xử lý. Vui lòng chờ trong giây lát.
                        </small>
                      @else
                        <small class="text-warning">
                          <i class="fas fa-exclamation-triangle me-1"></i>
                          <strong>Chưa có thông tin:</strong> Hệ thống chưa nhận được thông tin thanh toán. Vui lòng kiểm tra lại hoặc liên hệ hỗ trợ.
                        </small>
                      @endif
                    </div>
                    
                    @if($paymentStatus == 1 && isset($order->transaction_id))
                      <div class="mt-2">
                        <small class="text-muted">
                          <strong>Mã giao dịch:</strong> {{ $order->transaction_id }}<br>
                          <strong>Thời gian:</strong> {{ $order->payment_time ? date('d/m/Y H:i:s', strtotime($order->payment_time)) : 'N/A' }}
                        </small>
                      </div>
                    @endif
                    
                    @if($paymentStatus == 0 && $order->phuongthucthanhtoan === 'banking')
                      <div class="mt-2">
                        <a href="{{ route('bank.payment.info', $order->id) }}" class="btn btn-primary btn-sm">
                          <i class="fas fa-university me-1"></i>Xem thông tin chuyển khoản
                        </a>
                      </div>
                    @endif
                    
                    @if($paymentStatus == 2 || $paymentStatus == null)
                      <div class="mt-2">
                        <div class="alert alert-warning alert-sm">
                          <i class="fas fa-phone me-1"></i>
                          <strong>Cần hỗ trợ?</strong> Liên hệ hotline: <strong>1900 1234</strong> hoặc email: <strong>support@example.com</strong>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
                @if($order->ghichu)
                <div>
                  <strong>Ghi chú:</strong><br>
                  <small class="text-muted">{{ $order->ghichu }}</small>
                </div>
                @endif
              </div>
            </div>
            
            <!-- Actions -->
            <div class="card mt-4">
              <div class="card-body text-center">
                <button class="btn btn-outline-primary w-100 mb-2" onclick="window.print()">
                  <i class="fas fa-print me-2"></i>In hóa đơn
                </button>
                
                <a href="{{ route('checkout.check-payment') }}" class="btn btn-info w-100 mb-2">
                  <i class="fas fa-search me-2"></i>Kiểm tra trạng thái thanh toán
                </a>
                
                @if($order->trangthai === 'da_giao')
                <button class="btn btn-outline-success w-100 mb-2 reorder-btn" data-order-id="{{ $order->id }}">
                  <i class="fas fa-redo me-2"></i>Mua lại
                </button>
                @endif
                
                @if($order->phuongthucthanhtoan === 'chuyen_khoan' && in_array($order->trangthai, ['cho_thanh_toan', 'cho_xac_nhan']))
                <a href="{{ route('bank.payment.info', $order->id) }}" class="btn btn-success w-100 mb-2">
                  <i class="fas fa-university me-2"></i>Thanh toán ngân hàng
                </a>
                @endif
                
                @if(in_array($order->trangthai, ['cho_xac_nhan', 'da_xac_nhan']))
                <button class="btn btn-outline-danger w-100 cancel-order" data-order-id="{{ $order->id }}">
                  <i class="fas fa-times me-2"></i>Hủy đơn hàng
                </button>
                @else
                <div class="alert alert-info">
                  <small>Trạng thái hiện tại: <strong>{{ $order->trangthai_text }}</strong></small><br>
                  <small>Chỉ có thể hủy đơn hàng ở trạng thái "Chờ xác nhận" hoặc "Đã xác nhận"</small>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        
        <!-- Back Button -->
        <div class="text-center mt-4">
          <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách đơn hàng
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
  // Hủy đơn hàng
  $('.cancel-order').click(function() {
    const orderId = $(this).data('order-id');
    console.log('Cancel order clicked, order ID:', orderId);
    
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
      console.log('User confirmed cancellation');
      
      $.ajax({
        url: `/don-hang/${orderId}/cancel`,
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          console.log('Cancel response:', response);
          if (response.success) {
            // Không hiển thị alert, chỉ reload trang
            location.reload();
          } else {
            // Chỉ hiển thị alert khi có lỗi
            alert(response.message);
          }
        },
        error: function(xhr) {
          console.log('Cancel error:', xhr);
          const response = xhr.responseJSON;
          alert(response.message || 'Có lỗi xảy ra!');
        }
      });
    }
  });

  // Mua lại đơn hàng
  $('.reorder-btn').click(function() {
    const orderId = $(this).data('order-id');
    
    if (confirm('Bạn có muốn thêm các sản phẩm này vào giỏ hàng?')) {
      $.ajax({
        url: `/don-hang/${orderId}/reorder`,
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            alert(response.message);
            if (response.redirect_url) {
              window.location.href = response.redirect_url;
            }
          } else {
            alert(response.message);
          }
        },
        error: function(xhr) {
          const response = xhr.responseJSON;
          alert(response.message || 'Có lỗi xảy ra!');
        }
      });
    }
  });
});

</script>
@endsection
