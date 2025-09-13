@extends('fontend.layouts.app')

@section('title', 'Giới thiệu')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
@endsection

@section('content')
<section class="banner-khuyen-mai position-relative">
  <img src="{{ asset('fontend/img/6254356 (1).jpg') }}" alt="Banner" class="w-100 d-block" style="max-height: 180px; object-fit: cover;">
  <button class="btn-close position-absolute top-0 end-0 m-2" aria-label="Đóng" onclick="this.parentElement.style.display='none'"></button>
</section>

<!-- navbar moved to layout -->

<div class="container text-center mt-5">
  <h1 class="display-5 fw-bold">Chào mừng đến với <span class="highlight">ThriftZone</span></h1>
  <p class="lead">Nền tảng thời trang secondhand chất lượng, tiết kiệm và thân thiện môi trường</p>
</div>

<div class="container">
  <div class="section-title">Tầm nhìn & Sứ mệnh</div>
  <p>Tại ThriftZone, chúng tôi mong muốn trở thành nền tảng mua sắm đồ secondhand hàng đầu Việt Nam.</p>
</div>

<footer class="bg-warning text-dark mt-5 pt-5 pb-4">
  <div class="container text-md-left">
    <div class="row">
      <div class="col-md-4 mb-4">
        <h5 class="text-uppercase fw-bold mb-3">Thông tin liên hệ</h5>
        <ul class="list-unstyled">
          <li>Địa chỉ: TP.HCM</li>
          <li>Email: support@thriftzone.vn</li>
          <li>Điện thoại: 0909 123 456</li>
        </ul>
      </div>
      <div class="col-md-4 mb-4">
        <h5 class="text-uppercase fw-bold mb-3">Về ThriftZone</h5>
        <ul class="list-unstyled">
          <li><a href="#" class="text-dark text-decoration-none">Giới thiệu</a></li>
          <li><a href="#" class="text-dark text-decoration-none">Chính sách đổi trả</a></li>
          <li><a href="#" class="text-dark text-decoration-none">Hướng dẫn mua hàng</a></li>
        </ul>
      </div>
      <div class="col-md-4 mb-4">
        <h5 class="text-uppercase fw-bold mb-3">Hỗ trợ khách hàng</h5>
        <ul class="list-unstyled">
          <li><a href="#" class="text-dark text-decoration-none">Liên hệ</a></li>
          <li><a href="#" class="text-dark text-decoration-none">FAQ</a></li>
          <li><a href="#" class="text-dark text-decoration-none">Phản hồi</a></li>
        </ul>
      </div>
    </div>
    <div class="text-center pt-3 border-top border-dark mt-3">© 2025 <strong>ThriftZone</strong> – All rights reserved.</div>
  </div>
</footer>
@endsection


