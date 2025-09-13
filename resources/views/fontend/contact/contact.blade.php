@extends('fontend.layouts.app')

@section('title', 'Liên hệ')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
@endsection

@section('content')
<section class="banner-khuyen-mai position-relative">
  <img src="{{ asset('fontend/img/6254356 (1).jpg') }}" alt="Banner khuyến mãi" class="w-100 d-block" style="max-height: 180px; object-fit: cover;">
  <button class="btn-close position-absolute top-0 end-0 m-2" aria-label="Đóng" onclick="this.parentElement.style.display='none'"></button>
</section>

<!-- navbar moved to layout -->

<section class="contact-section container mt-5">
  <div class="row">
    <div class="col-md-5">
      <h3 class="contact-title">Liên hệ với ThriftZone</h3>
      <div class="contact-info">
        <p><i class="bi bi-geo-alt-fill"></i> 123 Lê Văn Việt, TP.HCM</p>
        <p><i class="bi bi-envelope-fill"></i> support@thriftzone.vn</p>
        <p><i class="bi bi-telephone-fill"></i> 0909 123 456</p>
        <p><i class="bi bi-clock-fill"></i> Thứ 2 - Chủ nhật: 8h - 22h</p>
      </div>
    </div>
    <div class="col-md-7">
      <h3 class="contact-title">Gửi phản hồi cho chúng tôi</h3>
      <div class="bg-white p-4 rounded shadow-sm">
        <form>
          <div class="mb-3"><label class="form-label">Họ và tên</label><input type="text" class="form-control" placeholder="Nguyễn Văn A" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" placeholder="email@example.com" required></div>
          <div class="mb-3"><label class="form-label">Nội dung</label><textarea class="form-control" rows="5" placeholder="Viết lời nhắn của bạn..." required></textarea></div>
          <button type="submit" class="btn btn-submit btn-warning">Gửi liên hệ</button>
        </form>
      </div>
    </div>
  </div>
  <div class="row mt-5">
    <div class="col-12">
      <h5 class="mb-3">Vị trí của chúng tôi</h5>
      <div class="ratio ratio-16x9">
        <iframe src="https://www.google.com/maps?q=FPT+Polytechnic,+TPHCM&output=embed" allowfullscreen loading="lazy"></iframe>
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


