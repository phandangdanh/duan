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
          <span class="badge bg-warning">{{ $order->trangthai_text }}</span>
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
        <div class="col-md-9">{{ $order->diachigiaohang }}</div>
      </div>
      
      @if($order->ghichu)
      <div class="row">
        <div class="col-md-3">Ghi chú:</div>
        <div class="col-md-9">{{ $order->ghichu }}</div>
      </div>
      @endif
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-6 mb-3">
      <a href="{{ route('products') }}" class="btn btn-outline-primary w-100">
        <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
      </a>
    </div>
    <div class="col-md-6 mb-3">
      <a href="{{ route('order.tracking', $order->id) }}" class="btn btn-primary w-100">
        <i class="fas fa-truck me-2"></i>Theo dõi đơn hàng
      </a>
    </div>
  </div>
  
  <div class="mt-4">
    <h5>Bước tiếp theo:</h5>
    <div class="row text-start">
      <div class="col-md-4 mb-3">
        <div class="d-flex align-items-center">
          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
               style="width: 40px; height: 40px;">
            <i class="fas fa-check"></i>
          </div>
          <div>
            <h6 class="mb-1">Đặt hàng thành công</h6>
            <small class="text-muted">Chúng tôi đã nhận được đơn hàng của bạn</small>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-3">
        <div class="d-flex align-items-center">
          <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
               style="width: 40px; height: 40px;">
            <i class="fas fa-clock"></i>
          </div>
          <div>
            <h6 class="mb-1">Đang xử lý</h6>
            <small class="text-muted">Chúng tôi đang chuẩn bị hàng cho bạn</small>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-3">
        <div class="d-flex align-items-center">
          <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
               style="width: 40px; height: 40px;">
            <i class="fas fa-truck"></i>
          </div>
          <div>
            <h6 class="mb-1">Đang giao hàng</h6>
            <small class="text-muted">Shipper sẽ giao hàng đến bạn</small>
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
