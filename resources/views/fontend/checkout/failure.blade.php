@extends('fontend.layouts.app')

@section('title', 'Đặt hàng thất bại')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
  <style>
    .failure-container {
      max-width: 600px;
      margin: 0 auto;
      text-align: center;
      padding: 60px 20px;
    }
    .failure-icon {
      font-size: 4rem;
      color: #f44336;
      margin-bottom: 20px;
    }
    .failure-info {
      background: #ffebee;
      border: 1px solid #ffcdd2;
      border-radius: 10px;
      padding: 30px;
      margin: 30px 0;
    }
  </style>
@endsection

@section('content')
<div class="failure-container">
  <div class="failure-icon">
    <i class="fas fa-times-circle"></i>
  </div>
  
  <h1 class="text-danger mb-3">Đặt hàng thất bại!</h1>
  <p class="lead text-muted mb-4">
    Rất tiếc, có vấn đề xảy ra trong quá trình xử lý đơn hàng của bạn.
  </p>
  
  <div class="failure-info">
    <h4 class="mb-4">
      <i class="fas fa-exclamation-triangle me-2"></i>Thông tin lỗi
    </h4>
    
    @if($order)
      <div class="text-start">
        <div class="row mb-3">
          <div class="col-md-3"><strong>Mã đơn hàng:</strong></div>
          <div class="col-md-9">#{{ $order->id }}</div>
        </div>
        
        <div class="row mb-3">
          <div class="col-md-3"><strong>Ngày đặt:</strong></div>
          <div class="col-md-9">{{ $order->ngaytao->format('d/m/Y H:i') }}</div>
        </div>
        
        <div class="row mb-3">
          <div class="col-md-3"><strong>Trạng thái:</strong></div>
          <div class="col-md-9">
            <span class="badge bg-danger">{{ $order->trangthai_text }}</span>
          </div>
        </div>
      </div>
    @endif
    
    <div class="alert alert-warning mt-4">
      <h5>Nguyên nhân có thể:</h5>
      <ul class="text-start mb-0">
        <li>Lỗi kết nối mạng</li>
        <li>Thông tin thanh toán không hợp lệ</li>
        <li>Hệ thống đang bảo trì</li>
        <li>Sản phẩm đã hết hàng</li>
      </ul>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-6 mb-3">
      <a href="{{ route('cart') }}" class="btn btn-outline-primary w-100">
        <i class="fas fa-shopping-cart me-2"></i>Quay lại giỏ hàng
      </a>
    </div>
    <div class="col-md-6 mb-3">
      <a href="{{ route('products') }}" class="btn btn-primary w-100">
        <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
      </a>
    </div>
  </div>
  
  <div class="mt-5">
    <h5>Cần hỗ trợ?</h5>
    <p class="text-muted">
      Nếu vấn đề vẫn tiếp tục, vui lòng liên hệ với chúng tôi:
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
