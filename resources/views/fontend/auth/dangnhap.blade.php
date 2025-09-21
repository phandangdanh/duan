@extends('fontend.layouts.app')

@section('title', 'Đăng nhập')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/dangki.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .social-login {
      margin-top: 20px;
      text-align: center;
    }
    .social-login p {
      margin-bottom: 10px;
      color: #555;
    }
    .social-buttons {
      display: flex;
      justify-content: center;
    }
    .btn-google {
      background-color: #DB4437;
      color: white;
      padding: 8px 15px;
      border-radius: 4px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      margin: 0 5px;
    }
    .btn-google i {
      margin-right: 8px;
    }
    .btn-google:hover {
      background-color: #C53929;
      color: white;
    }
  </style>
@endsection

@section('content')
<div class="form-box">
  <div class="logo-box">
    <div class="logo">ThriftZone</div>
  </div>
  <h2>Đăng nhập</h2>
  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif
  <form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div class="mb-3">
      <label for="matkhau" class="form-label">Mật khẩu</label>
      <input type="password" class="form-control" id="matkhau" name="password" required>
    </div>
    <div class="forgot-password mb-3 text-end">
      <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
    </div>
    <button type="submit" class="btn btn-login">Đăng nhập</button>
  </form>


  <div class="social-login">
    <p>Hoặc đăng nhập bằng</p>
    <div class="social-buttons">
      <a href="{{ route('login.google') }}" class="btn btn-google">
        <i class="fab fa-google"></i> Google
      </a>
    </div>
    <p style="font-size: 12px; color: #888; margin-top: 10px;">
      Nếu muốn chọn tài khoản Google khác, hãy đăng xuất Google trước
    </p>
  </div>

  <div class="dang-ky-link">
    Chưa có tài khoản? <a href="{{ url('/dangki') }}">Đăng ký ngay</a>
  </div>
</div>
@include('fontend.layouts.partials.footer')
@endsection


