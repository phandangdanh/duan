@extends('fontend.layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/dangki.css') }}">
@endsection

@section('content')
<div class="form-box">
  <div class="logo-box">
    <div class="logo">ThriftZone</div>
  </div>
  <h2>Đặt lại mật khẩu</h2>
  
  @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif
  
  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif
  
  <form method="POST" action="{{ route('password.update') }}">
    @csrf
    
    <input type="hidden" name="token" value="{{ $token }}">
    
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly>
    </div>
    
    <div class="mb-3">
      <label for="password" class="form-label">Mật khẩu mới</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    
    <div class="mb-3">
      <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
      <input type="password" class="form-control" id="password-confirm" name="password_confirmation" required>
    </div>
    
    <button type="submit" class="btn btn-login">Đặt lại mật khẩu</button>
  </form>
  
  <div class="dang-ky-link mt-3">
    <a href="{{ route('login') }}">Quay lại đăng nhập</a>
  </div>
</div>
@include('fontend.layouts.partials.footer')
@endsection
