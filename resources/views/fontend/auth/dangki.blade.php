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

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form id="formDangKy" method="POST" action="{{ url('/dangki') }}" novalidate>
    @csrf
    <div class="mb-3">
      <label for="name" class="form-label">Tên người dùng</label>
      <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Mật khẩu</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
      <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
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


