@extends('fontend.layouts.app')

@section('title', 'Sản phẩm')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/style.css') }}">
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
@endsection

@section('content')
<section class="banner-khuyen-mai position-relative">
  <img src="{{ asset('fontend/img/6254356 (1).jpg') }}" alt="Banner" class="w-100 d-block" style="max-height: 180px; object-fit: cover;">
  <button class="btn-close position-absolute top-0 end-0 m-2" aria-label="Đóng" onclick="this.parentElement.style.display='none'"></button>
</section>

<!-- navbar moved to layout -->

<div class="container py-5">
  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="filter-box bg-white p-4 rounded shadow-sm">
        <h5>Lọc theo mức giá</h5>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="price1"><label class="form-check-label" for="price1">Dưới 100.000đ</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="price2"><label class="form-check-label" for="price2">100.000đ - 200.000đ</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="price3"><label class="form-check-label" for="price3">Trên 200.000đ</label></div>
        <hr>
        <h5>Loại sản phẩm</h5>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="type1"><label class="form-check-label" for="type1">Áo sơ mi</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="type2"><label class="form-check-label" for="type2">Quần jean</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="type3"><label class="form-check-label" for="type3">Váy đầm</label></div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card product-card">
            <img src="{{ asset('fontend/img/aosomi1.png') }}" class="card-img-top" alt="Sản phẩm 1">
            <div class="card-body">
              <h5 class="card-title">Áo sơ mi nam vintage</h5>
              <p class="price">199.000đ</p>
              <a href="{{ url('/trangchitiet') }}" class="btn btn-warning w-100">Xem chi tiết</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
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


