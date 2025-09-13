@extends('fontend.layouts.app')

@section('title', 'Đăng ký')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/dangki.css') }}">
@endsection

@section('content')
<div class="form-box">
  <div class="logo-box">
    <div class="logo">ThriftZone</div>
  </div>
  <h2>Đăng ký</h2>
  <form id="formDangKy" novalidate>
    <div class="mb-3">
      <label for="ten" class="form-label">Tên người dùng</label>
      <input type="text" class="form-control" id="ten" name="ten" required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
      <label for="matkhau" class="form-label">Mật khẩu</label>
      <input type="password" class="form-control" id="matkhau" name="matkhau" required>
    </div>
    <div class="mb-3">
      <label for="nhaplaimatkhau" class="form-label">Nhập lại mật khẩu</label>
      <input type="password" class="form-control" id="nhaplaimatkhau" name="nhaplaimatkhau" required>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="nhoMatKhau">
      <label class="form-check-label" for="nhoMatKhau">Nhớ mật khẩu</label>
    </div>
    <button type="submit" class="btn btn-register">Đăng ký</button>
  </form>
{{-- 
  <button class="btn-google">
    <img src="{{ asset('fontend/img/Google__G__logo.svg.webp') }}" alt="google icon">
    Đăng nhập bằng Google
  </button> --}}

  <div class="dang-nhap-link">
    Đã có tài khoản? <a href="{{ url('/dangnhap') }}">Đăng nhập ngay</a>
  </div>
</div>
@include('fontend.layouts.partials.footer')
@endsection


