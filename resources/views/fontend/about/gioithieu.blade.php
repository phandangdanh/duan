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

@endsection


