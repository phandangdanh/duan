<nav class="navbar navbar-expand-lg navbar-light bg-warning shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">ThriftZone</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-chinh">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu-chinh">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Trang chủ</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/gioithieu') }}">Giới thiệu</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/sanpham') }}">Sản phẩm</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">Liên hệ</a></li>
      </ul>
      <div class="d-flex align-items-center">
        <!-- Giỏ hàng -->
        <a href="{{ route('cart') }}" class="btn btn-outline-dark me-3 position-relative">
          <i class="fas fa-shopping-cart me-1"></i>
          Giỏ hàng
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" style="font-size: 0.7rem;">
            0
          </span>
        </a>
        
        @auth
          <div class="dropdown me-2">
            <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="rounded-circle bg-warning text-dark d-inline-flex justify-content-center align-items-center me-2" style="width:32px;height:32px;">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
              </span>
              <span class="fw-semibold">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#">Tài khoản của tôi</a></li>
              <li><a class="dropdown-item" href="#">Đơn hàng của tôi</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                  @csrf
                  <button type="submit" class="btn btn-outline-danger w-100">Đăng xuất</button>
                </form>
              </li>
            </ul>
          </div>
        @else
          <a href="{{ url('/dangnhap') }}" class="btn btn-outline-dark me-2">Đăng nhập</a>
          <a href="{{ url('/dangki') }}" class="btn btn-dark me-2">Đăng ký</a>
          
        @endauth
      </div>
    </div>
  </div>
</nav>


