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

@endsection


