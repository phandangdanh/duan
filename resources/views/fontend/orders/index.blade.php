@extends('fontend.layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('css')
<link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
<style>
.order-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.order-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  overflow: hidden;
  transition: transform 0.3s ease;
}

.order-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.order-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.order-info {
  padding: 20px;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
}

.status-cho-xac-nhan { background: #fff3cd; color: #856404; }
.status-da-xac-nhan { background: #d1ecf1; color: #0c5460; }
.status-dang-giao { background: #d4edda; color: #155724; }
.status-da-giao { background: #d1ecf1; color: #0c5460; }
.status-da-huy { background: #f8d7da; color: #721c24; }
.status-hoan-tra { background: #e2e3e5; color: #383d41; }

.order-items {
  margin-top: 15px;
}

.order-item {
  display: flex;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.order-item:last-child {
  border-bottom: none;
}

.order-item img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 5px;
  margin-right: 15px;
}

.order-actions {
  padding: 15px 20px;
  background: #f8f9fa;
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #6c757d;
}

.empty-state i {
  font-size: 4rem;
  margin-bottom: 20px;
  color: #dee2e6;
}

/* Enhanced Styles */
.order-card {
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
}

.order-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  border-color: #667eea;
}

.order-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.status-badge {
  position: relative;
  z-index: 1;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.order-item {
  transition: all 0.3s ease;
  border-radius: 8px;
}

.order-item:hover {
  background: #f8f9fa;
  transform: translateX(5px);
}

.order-actions .btn {
  transition: all 0.3s ease;
  font-weight: 500;
}

.order-actions .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Responsive improvements */
@media (max-width: 768px) {
  .order-header {
    flex-direction: column;
    text-align: center;
  }
  
  .order-header .text-end {
    text-align: center !important;
    margin-top: 15px;
  }
  
  .order-info .row {
    flex-direction: column;
  }
  
  .order-info .col-md-6 {
    margin-bottom: 15px;
  }
}
</style>
@endsection

@section('content')
<div class="order-container">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2><i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi</h2>
          <a href="{{ route('products') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tiếp tục mua sắm
          </a>
        </div>

        @if($orders->count() > 0)
          @foreach($orders as $order)
    <div class="order-card">
      <div class="order-header">
        <div>
                <h4 class="mb-1">Đơn hàng #{{ $order->id }}</h4>
                <small class="opacity-75">
                  <i class="fas fa-calendar me-1"></i>
                  {{ $order->ngaytao ? $order->ngaytao->format('d/m/Y H:i') : 'N/A' }}
                </small>
        </div>
        <div class="text-end">
                <span class="status-badge status-{{ $order->trangthai }}">
                  {{ $order->trang_thai_text }}
                </span>
          <div class="mt-2">
                  <strong>{{ number_format($order->tongtien, 0, ',', '.') }}₫</strong>
          </div>
        </div>
      </div>
      
            <div class="order-info">
              <div class="row">
                <div class="col-md-6">
                  <p class="mb-1"><strong>Người nhận:</strong> {{ $order->hoten }}</p>
                  <p class="mb-1"><strong>SĐT:</strong> {{ $order->sodienthoai }}</p>
                  <p class="mb-0"><strong>Địa chỉ:</strong> {{ trim($order->diachigiaohang, ', ') }}</p>
                </div>
                <div class="col-md-6">
                  <p class="mb-1"><strong>Phương thức thanh toán:</strong> 
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
                  </p>
                  @if($order->ghichu)
                  <p class="mb-0"><strong>Ghi chú:</strong> {{ $order->ghichu }}</p>
                  @endif
                </div>
              </div>

              @if($order->chiTietDonHang && $order->chiTietDonHang->count() > 0)
      <div class="order-items">
                <h6 class="mb-3"><i class="fas fa-box me-2"></i>Sản phẩm:</h6>
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
                         style="width: 50px; height: 50px; object-fit: cover;"
                         onerror="this.src='/fontend/img/aosomi1.png'">
                  @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px;">
                      <i class="fas fa-box text-muted"></i>
                    </div>
                  @endif
            <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $item->tensanpham }}</h6>
                    <small class="text-muted">{{ number_format($item->dongia, 0, ',', '.') }}₫</small>
            </div>
            <div class="text-end">
                    <span class="badge bg-primary me-2">x{{ $item->soluong }}</span>
                    <strong class="text-danger">{{ number_format($item->thanhtien, 0, ',', '.') }}₫</strong>
            </div>
          </div>
                @endforeach
      </div>
              @endif
        </div>
        
            <div class="order-actions">
              <a href="{{ route('orders.detail', $order->id) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-eye me-1"></i>Xem chi tiết
              </a>
              
              @if(in_array($order->trangthai, ['cho_xac_nhan', 'da_xac_nhan']))
              <button class="btn btn-outline-danger btn-sm cancel-order" data-order-id="{{ $order->id }}">
                <i class="fas fa-times me-1"></i>Hủy đơn hàng
          </button>
              @endif
              
              @if($order->trangthai === 'da_giao')
              <button class="btn btn-outline-success btn-sm reorder-btn" data-order-id="{{ $order->id }}">
              <i class="fas fa-redo me-1"></i>Mua lại
            </button>
              @endif
            </div>
          </div>
          @endforeach

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
          </div>
        @else
          <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <h4>Chưa có đơn hàng nào</h4>
            <p>Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!</p>
            <a href="{{ route('products') }}" class="btn btn-primary">
              <i class="fas fa-shopping-cart me-2"></i>Mua sắm ngay
            </a>
          </div>
        @endif
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
    
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
      $.ajax({
        url: `/don-hang/${orderId}/cancel`,
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            alert(response.message);
            location.reload();
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
