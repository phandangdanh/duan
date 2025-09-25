@extends('fontend.layouts.app')

@section('title', 'Đặt hàng thành công')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
  <style>
    .success-container {
      max-width: 800px;
      margin: 0 auto;
      text-align: center;
      padding: 60px 20px;
    }
    .success-icon {
      font-size: 4rem;
      color: #4caf50;
      margin-bottom: 20px;
    }
    .order-info {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 30px;
      margin: 30px 0;
    }
    .order-details {
      text-align: left;
      margin-top: 20px;
    }
    .order-details .row {
      margin-bottom: 10px;
    }
    .order-details .col-md-3 {
      font-weight: bold;
      color: #666;
    }
    .order-details .col-md-9 {
      color: #333;
    }
    
    /* Timeline Styles */
    .timeline-container {
      margin-top: 20px;
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
      background: linear-gradient(to bottom, #4caf50, #2196f3, #ff9800, #9e9e9e);
    }
    
    .timeline-item {
      position: relative;
      margin-bottom: 30px;
      padding-left: 20px;
    }
    
    .timeline-marker {
      position: absolute;
      left: -22px;
      top: 5px;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: #e0e0e0;
      border: 3px solid white;
      box-shadow: 0 0 0 3px #e0e0e0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #666;
      font-size: 12px;
      transition: all 0.3s ease;
    }
    
    .timeline-item.completed .timeline-marker {
      background: #4caf50;
      color: white;
      box-shadow: 0 0 0 3px #e8f5e8;
    }
    
    .timeline-item.current .timeline-marker {
      background: #ff9800;
      color: white;
      box-shadow: 0 0 0 3px #fff3e0;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.7); }
      70% { box-shadow: 0 0 0 10px rgba(255, 152, 0, 0); }
      100% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0); }
    }
    
    .timeline-content {
      background: white;
      padding: 15px 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-left: 4px solid #e0e0e0;
      transition: all 0.3s ease;
    }
    
    .timeline-item.completed .timeline-content {
      border-left-color: #4caf50;
      background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);
    }
    
    .timeline-item.current .timeline-content {
      border-left-color: #ff9800;
      background: linear-gradient(135deg, #fff8f0 0%, #ffffff 100%);
    }
    
    .timeline-content h6 {
      color: #333;
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .timeline-content p {
      color: #666;
      font-size: 14px;
      margin-bottom: 0;
    }
    
    .timeline-content small {
      color: #999;
      font-weight: 500;
    }
  </style>
@endsection

@section('content')
<div class="success-container">
  <div class="success-icon">
    <i class="fas fa-check-circle"></i>
  </div>
  
  <h1 class="text-success mb-3">Đặt hàng thành công!</h1>
  <p class="lead text-muted mb-4">
    Cảm ơn bạn đã mua sắm tại ThriftZone. Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.
  </p>
  
  <div class="order-info">
    <h4 class="mb-4">
      <i class="fas fa-receipt me-2"></i>Thông tin đơn hàng
    </h4>
    
    <div class="order-details">
      <div class="row">
        <div class="col-md-3">Mã đơn hàng:</div>
        <div class="col-md-9">
          <strong class="text-primary">#{{ $order->id }}</strong>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-3">Ngày đặt:</div>
        <div class="col-md-9">{{ $order->ngaytao->format('d/m/Y H:i') }}</div>
      </div>
      
      <div class="row">
        <div class="col-md-3">Tổng tiền:</div>
        <div class="col-md-9">
          <strong class="text-danger">{{ number_format($order->tongtien, 0, ',', '.') }}₫</strong>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-3">Trạng thái:</div>
        <div class="col-md-9">
          <span class="badge {{ $order->trang_thai_badge_class }}">{{ $order->trang_thai_text }}</span>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-3">Phương thức thanh toán:</div>
        <div class="col-md-9">
          @if($order->phuongthucthanhtoan === 'cod')
            <i class="fas fa-money-bill-wave me-2"></i>Thanh toán khi nhận hàng
          @elseif($order->phuongthucthanhtoan === 'banking')
            <i class="fas fa-university me-2"></i>Chuyển khoản ngân hàng
          @elseif($order->phuongthucthanhtoan === 'momo')
            <i class="fas fa-mobile-alt me-2"></i>Ví MoMo
          @elseif($order->phuongthucthanhtoan === 'zalopay')
            <i class="fas fa-qrcode me-2"></i>ZaloPay
          @else
            {{ ucfirst($order->phuongthucthanhtoan) }}
          @endif
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-3">Địa chỉ giao hàng:</div>
        <div class="col-md-9">{{ trim($order->diachigiaohang, ', ') }}</div>
      </div>
      
      @if($order->ghichu)
      <div class="row">
        <div class="col-md-3">Ghi chú:</div>
        <div class="col-md-9">{{ $order->ghichu }}</div>
      </div>
      @endif
    </div>
  </div>
  
  <!-- Chi tiết sản phẩm -->
  @if($order->chiTietDonHang && $order->chiTietDonHang->count() > 0)
  <div class="order-info">
    <h4 class="mb-4">
      <i class="fas fa-shopping-bag me-2"></i>Sản phẩm đã đặt
    </h4>
    
    <div class="row">
      @foreach($order->chiTietDonHang as $item)
      <div class="col-md-6 mb-3">
        <div class="d-flex align-items-center p-3 border rounded">
          <div class="me-3">
            @php
              $productId = $item->chiTietSanPham->id_sp ?? null;
              $product = $productId ? \App\Models\SanPham::with('hinhanh')->find($productId) : null;
              $firstImage = $product && $product->hinhanh->count() > 0 ? $product->hinhanh->first() : null;
            @endphp
            
            @if($firstImage)
              <img src="{{ $firstImage->url ? url($firstImage->url) : '/fontend/img/aosomi1.png' }}" 
                   alt="{{ $item->tensanpham }}" 
                   class="rounded" 
                   style="width: 60px; height: 60px; object-fit: cover;"
                   onerror="this.src='/fontend/img/aosomi1.png'">
            @else
              <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                   style="width: 60px; height: 60px;">
                <i class="fas fa-box text-muted"></i>
              </div>
            @endif
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-1">{{ $item->tensanpham }}</h6>
            <small class="text-muted">{{ number_format($item->dongia, 0, ',', '.') }}₫</small>
            <div class="mt-1">
              <span class="badge bg-primary">x{{ $item->soluong }}</span>
            </div>
          </div>
          <div class="text-end">
            <strong class="text-danger">{{ number_format($item->thanhtien, 0, ',', '.') }}₫</strong>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif
  
  <div class="row">
    <div class="col-md-6 mb-3">
      <a href="{{ route('products') }}" class="btn btn-outline-primary w-100">
        <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
      </a>
    </div>
    <div class="col-md-6 mb-3">
      @if($order->phuongthucthanhtoan === 'chuyen_khoan')
        <a href="{{ route('bank.payment.info', $order->id) }}" class="btn btn-success w-100 mb-2">
          <i class="fas fa-university me-2"></i>Thông tin chuyển khoản
        </a>
      @endif
      <a href="{{ route('orders.detail', $order->id) }}" class="btn btn-primary w-100">
        <i class="fas fa-truck me-2"></i>Xem chi tiết đơn hàng
      </a>
    </div>
  </div>
  
  <div class="mt-4">
    <h5><i class="fas fa-route me-2"></i>Tiến trình đơn hàng</h5>
    <div class="timeline-container">
      <div class="timeline">
        <!-- Bước 1: Đặt hàng thành công -->
        <div class="timeline-item completed">
          <div class="timeline-marker">
            <i class="fas fa-check"></i>
          </div>
          <div class="timeline-content">
            <h6 class="mb-1">Đặt hàng thành công</h6>
            <small class="text-muted">{{ $order->ngaytao ? $order->ngaytao->format('d/m/Y H:i') : 'N/A' }}</small>
            <p class="mb-0">Chúng tôi đã nhận được đơn hàng của bạn</p>
          </div>
        </div>
        
        <!-- Bước 2: Đang xử lý -->
        <div class="timeline-item {{ $order->trangthai === 'cho_xac_nhan' ? 'current' : (($order->trangthai === 'da_xac_nhan' || in_array($order->trangthai, ['dang_giao', 'da_giao'])) ? 'completed' : '') }}">
          <div class="timeline-marker">
            <i class="fas fa-clock"></i>
          </div>
          <div class="timeline-content">
            <h6 class="mb-1">Đang xử lý</h6>
            <small class="text-muted">
              @if($order->trangthai === 'da_xac_nhan' || in_array($order->trangthai, ['dang_giao', 'da_giao']))
                {{ $order->ngaycapnhat ? $order->ngaycapnhat->format('d/m/Y H:i') : 'Đã xác nhận' }}
              @else
                Chưa cập nhật
              @endif
            </small>
            <p class="mb-0">Chúng tôi đang chuẩn bị hàng cho bạn</p>
          </div>
        </div>
        
        <!-- Bước 3: Đang giao hàng -->
        <div class="timeline-item {{ $order->trangthai === 'dang_giao' ? 'current' : (($order->trangthai === 'da_giao') ? 'completed' : '') }}">
          <div class="timeline-marker">
            <i class="fas fa-truck"></i>
          </div>
          <div class="timeline-content">
            <h6 class="mb-1">Đang giao hàng</h6>
            <small class="text-muted">
              @if($order->trangthai === 'da_giao')
                {{ $order->ngaycapnhat ? $order->ngaycapnhat->format('d/m/Y H:i') : 'Đã giao' }}
              @else
                Chưa cập nhật
              @endif
            </small>
            <p class="mb-0">Shipper sẽ giao hàng đến bạn</p>
          </div>
        </div>
        
        <!-- Bước 4: Giao hàng thành công -->
        <div class="timeline-item {{ $order->trangthai === 'da_giao' ? 'completed' : '' }}">
          <div class="timeline-marker">
            <i class="fas fa-home"></i>
          </div>
          <div class="timeline-content">
            <h6 class="mb-1">Giao hàng thành công</h6>
            <small class="text-muted">
              @if($order->trangthai === 'da_giao')
                {{ $order->ngaycapnhat ? $order->ngaycapnhat->format('d/m/Y H:i') : 'Đã hoàn thành' }}
              @else
                Chưa cập nhật
              @endif
            </small>
            <p class="mb-0">Đơn hàng đã được giao thành công</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="mt-5">
    <h5>Thông tin liên hệ</h5>
    <p class="text-muted">
      Nếu bạn có bất kỳ câu hỏi nào về đơn hàng, vui lòng liên hệ với chúng tôi:
    </p>
    <div class="row">
      <div class="col-md-6">
        <p><i class="fas fa-phone me-2"></i>Hotline: 1900 1234</p>
      </div>
      <div class="col-md-6">
        <p><i class="fas fa-envelope me-2"></i>Email: support@thriftzone.com</p>
      </div>
    </div>
  </div>
</div>


@endsection
