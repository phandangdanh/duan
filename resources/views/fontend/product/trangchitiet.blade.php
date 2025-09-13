@extends('fontend.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
@endsection

@section('content')
<div class="container py-5">
  <div class="row">
    <div class="col-md-5">
      <img id="main-image" src="{{ asset('fontend/img/aosomi1.png') }}" alt="Sản phẩm" class="img-fluid mb-3">
      <div class="d-flex">
        <img src="{{ asset('fontend/img/aosomi1.png') }}" class="thumbnail-img active" onclick="document.getElementById('main-image').src=this.src">
        <img src="{{ asset('fontend/img/aosomi2.png') }}" class="thumbnail-img" onclick="document.getElementById('main-image').src=this.src">
        <img src="{{ asset('fontend/img/aosomi3.png') }}" class="thumbnail-img" onclick="document.getElementById('main-image').src=this.src">
      </div>
    </div>
    <div class="col-md-7">
      <h4>Áo Sơ Mi Nam Kẻ Sọc Đen Trắng 2025 Phong Cách Hàn Quốc</h4>
      <div class="mb-2 text-warning">★★★★★ <span class="text-secondary">52 đánh giá</span></div>
      <div class="mb-2"><span class="price-sale">59.000₫</span> <span class="price-original">104.000₫</span> <span class="text-danger"> -43%</span></div>
      <div class="mt-4"><strong>Nhóm Màu: </strong><div class="product-options d-flex flex-wrap"><button class="btn btn-outline-secondary active">Đen Trắng</button><button class="btn btn-outline-secondary">Đỏ</button></div></div>
      <div class="mt-3"><strong>Size: </strong><div class="product-options d-flex flex-wrap"><button class="btn btn-outline-secondary">M</button><button class="btn btn-outline-secondary active">L</button></div></div>
      <div class="mt-3 d-flex align-items-center"><strong class="me-3">Số lượng:</strong><input type="number" class="form-control qty-input" min="1" value="1" style="width:60px"></div>
      <div class="mt-4 d-flex gap-2"><button class="btn btn-buy-now px-4">Mua ngay</button><button class="btn btn-outline-warning px-4">Thêm vào giỏ</button></div>
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


