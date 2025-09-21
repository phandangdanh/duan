@extends('fontend.layouts.app')

@section('title', 'Quên mật khẩu')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/dangki.css') }}">
@endsection

@section('content')
<div class="form-box">
  <div class="logo-box">
    <div class="logo">ThriftZone</div>
  </div>
  <h2>Quên mật khẩu</h2>
  
  @if (session('status'))
    <div class="alert alert-success">
      {{ session('status') }}
    </div>
  @endif
  
  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif
  
  <p class="mb-3">Vui lòng nhập địa chỉ email của bạn và chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu.</p>
  
  <form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
    </div>
    
    <button type="submit" class="btn btn-login">Gửi liên kết đặt lại mật khẩu</button>
  </form>
  
  <div class="dang-ky-link mt-3">
    <a href="{{ route('login') }}">Quay lại đăng nhập</a>
  </div>
</div>
@include('fontend.layouts.partials.footer')
@endsection
