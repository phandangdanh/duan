@extends('fontend.layouts.app')

@section('title', 'Trang chủ')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
@endsection

@section('content')
<section class="banner-khuyen-mai position-relative">
  <img src="{{ asset('fontend/img/6254356 (1).jpg') }}" 
       alt="Banner khuyến mãi" 
       class="w-100 d-block" style="max-height: 180px; object-fit: cover;">
  <button class="btn-close position-absolute top-0 end-0 m-2" aria-label="Đóng" onclick="this.parentElement.style.display='none'"></button>
</section>

<!-- navbar moved to layout -->

<section class="danh-muc-noi-bat py-5 bg-light">
  <div class="container">
    <h2 class="mb-4">Danh mục sản phẩm nổi bật</h2>
    <div class="row g-4">
      <div class="col-6 col-md-3"><div class="border rounded-3 text-center p-4 shadow-sm bg-white h-100"><i class="bi bi-person-workspace fs-1 text-success mb-3"></i><h5 class="mb-0">Áo khoác</h5></div></div>
      <div class="col-6 col-md-3"><div class="border rounded-3 text-center p-4 shadow-sm bg-white h-100"><i class="bi bi-person fs-1 text-success mb-3"></i><h5 class="mb-0">Áo thun</h5></div></div>
      <div class="col-6 col-md-3"><div class="border rounded-3 text-center p-4 shadow-sm bg-white h-100"><i class="bi bi-sliders2 fs-1 text-success mb-3"></i><h5 class="mb-0">Quần jean</h5></div></div>
      <div class="col-6 col-md-3"><div class="border rounded-3 text-center p-4 shadow-sm bg-white h-100"><i class="bi bi-person-bounding-box fs-1 text-warning mb-3"></i><h5 class="mb-0">Váy</h5></div></div>
    </div>
  </div>
</section>

<section class="san-pham-noi-bat py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-black" style="letter-spacing: 1px;">Sản phẩm nổi bật</h2>
    <div class="row g-4">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="card border-0 shadow-lg h-100 position-relative">
          <img src="{{ asset('fontend/img/aosomi1.png') }}" class="card-img-top" style="height: 250px; object-fit: cover; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;" alt="Áo khoác denim">
          <div class="card-body text-center">
            <h5 class="fw-semibold">Áo khoác Denim</h5>
            <p class="text-muted small mb-3">Phong cách cổ điển, phù hợp mọi mùa.</p>
            <div class="d-flex justify-content-center gap-2">
              <a href="{{ url('/trangchitiet') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">Xem chi tiết</a>
              <button class="btn btn-danger btn-sm rounded-pill px-3">Mua ngay</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

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


