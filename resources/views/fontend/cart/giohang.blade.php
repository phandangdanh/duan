@extends('fontend.layouts.app')

@section('title', 'Giỏ hàng')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
@endsection

@section('content')
<!-- navbar moved to layout -->

<section class="py-5">
  <div class="container">
    <h2 class="mb-4">Giỏ hàng của bạn</h2>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
          <tr><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Tạm tính</th><th></th></tr>
        </thead>
        <tbody>
          <tr>
            <td><div class="d-flex align-items-center"><img src="{{ asset('fontend/img/aokhoac1.png') }}" width="60" height="60" class="me-3" /><span>Áo khoác Denim Xanh</span></div></td>
            <td>220.000₫</td>
            <td><input type="number" value="1" min="1" class="form-control w-50" /></td>
            <td>220.000₫</td>
            <td><button class="btn btn-danger btn-sm">Xóa</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="text-end">
      <h5>Tổng cộng: <strong class="text-danger">220.000₫</strong></h5>
      <a href="#" class="btn btn-success mt-3">Thanh toán</a>
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


