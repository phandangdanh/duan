@extends('fontend.layouts.app')

@section('title', 'Đăng nhập')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/dangki.css') }}">
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
  <form method="POST" action="#">
    @csrf
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div class="mb-3">
      <label for="matkhau" class="form-label">Mật khẩu</label>
      <input type="password" class="form-control" id="matkhau" name="password" required>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="nhoMatKhau" name="remember">
      <label class="form-check-label" for="nhoMatKhau">Nhớ mật khẩu</label>
    </div>
    <button type="submit" class="btn btn-login">Đăng nhập</button>
  </form>


  <div class="dang-ky-link">
    Chưa có tài khoản? <a href="{{ url('/dangki') }}">Đăng ký ngay</a>
  </div>
</div>
@include('fontend.layouts.partials.footer')
@endsection


