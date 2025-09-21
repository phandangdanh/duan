@extends('fontend.layouts.app')

@section('title', 'Sản phẩm nổi bật')

@section('css')
<link rel="stylesheet" href="{{ asset('fontend/sanphamnoibat.css') }}">
@endsection

@section('content')
<div class="container py-5 sanphamnoibat">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
      <li class="breadcrumb-item active" aria-current="page">Sản phẩm nổi bật</li>
    </ol>
  </nav>

  <!-- Header -->
  <div class="text-center mb-5">
    <h1 class="fw-bold text-uppercase text-primary" style="letter-spacing: 1px;">Sản phẩm nổi bật</h1>
    <p class="text-muted">Khám phá những sản phẩm được yêu thích nhất</p>
  </div>

  <!-- Products Grid -->
  <div class="row g-4">
    @forelse($products as $product)
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card border-0 shadow-lg h-100 position-relative hover-lift">
          @php
            // Logic giống như trong quản lý sản phẩm
            $defaultImage = optional($product->hinhanh->where('is_default', 1)->first())->url;
            $imageExists = $defaultImage ? file_exists(public_path($defaultImage)) : false;
          @endphp
          @if($defaultImage && $imageExists)
            <img src="{{ asset($defaultImage) }}" 
                 class="card-img-top" 
                 style="height: 200px; object-fit: cover; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;" 
                 alt="{{ $product->tenSP }}">
          @else
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 200px; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
              <div class="text-center text-muted">
                <i class="fas fa-image fa-3x mb-2"></i>
                <p class="mb-0 small">Không có ảnh</p>
              </div>
            </div>
          @endif
          
          @php
            // Kiểm tra có khuyến mãi từ chi tiết sản phẩm
            $hasSale = $product->chitietsanpham
              ->where('gia_khuyenmai', '>', 0)
              ->where(function($item) {
                return $item->gia_khuyenmai < $item->gia;
              })
              ->count() > 0;
          @endphp
          @if($hasSale)
            <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
          @endif
          
          <div class="card-body text-center">
            <h6 class="fw-semibold text-dark mb-2">{{ Str::limit($product->tenSP, 40) }}</h6>
            
            <!-- Hiển thị giá -->
            <div class="mb-3">
              @php
                // Lấy giá từ chi tiết sản phẩm (biến thể)
                $variantPrices = $product->chitietsanpham
                  ->map(function($d){
                    return [
                      'gia' => $d->gia ? (float)$d->gia : 0,
                      'gia_khuyenmai' => $d->gia_khuyenmai ? (float)$d->gia_khuyenmai : 0
                    ];
                  })
                  ->filter(function($v){ return $v['gia'] > 0; });
                
                $minPrice = $variantPrices->min('gia');
                $maxPrice = $variantPrices->max('gia');
                $minSalePrice = $variantPrices->min('gia_khuyenmai');
                $maxSalePrice = $variantPrices->max('gia_khuyenmai');
                
                // Kiểm tra có khuyến mãi không (gia_khuyenmai > 0 và < gia)
                $hasSale = $variantPrices->where('gia_khuyenmai', '>', 0)->where('gia_khuyenmai', '<', function($item) { return $item['gia']; })->count() > 0;
              @endphp
              
              @if($variantPrices->count() > 0)
                @if($hasSale)
                  {{-- Có khuyến mãi --}}
                  <div class="d-flex justify-content-center align-items-center gap-2">
                    <span class="text-decoration-line-through text-muted small">{{ number_format($minPrice, 0, ',', '.') }} VNĐ</span>
                    <span class="fw-bold text-danger fs-6">{{ number_format($minSalePrice, 0, ',', '.') }} VNĐ</span>
                  </div>
                  @php
                    $discountPercent = round((($minPrice - $minSalePrice) / $minPrice) * 100);
                  @endphp
                  <small class="text-success">-{{ $discountPercent }}%</small>
                @else
                  {{-- Không có khuyến mãi --}}
                  @if($minPrice === $maxPrice)
                    <span class="fw-bold text-primary fs-6">{{ number_format($minPrice, 0, ',', '.') }} VNĐ</span>
                  @else
                    <span class="fw-bold text-primary fs-6">{{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }} VNĐ</span>
                  @endif
                @endif
              @else
                <span class="fw-bold text-muted fs-6">Liên hệ</span>
              @endif
            </div>
            
            <div class="d-flex justify-content-center gap-2">
              <a href="{{ route('product.detail', $product->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Xem chi tiết</a>
              <button class="btn btn-danger btn-sm rounded-pill px-3" onclick="addToCart({{ $product->id }})">Mua ngay</button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <i class="bi bi-box-seam fs-1 text-muted mb-3"></i>
        <h4 class="text-muted">Chưa có sản phẩm nổi bật nào</h4>
        <p class="text-muted">Hãy quay lại sau để xem những sản phẩm mới nhất!</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Về trang chủ</a>
      </div>
    @endforelse
  </div>

  <!-- Pagination -->
  @if($products->hasPages())
    <div class="d-flex justify-content-center mt-5">
      {{ $products->links('vendor.pagination.bootstrap-5') }}
    </div>
  @endif
</div>
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

<script>
function addToCart(productId) {
  // Tạo toast notification
  const toast = document.createElement('div');
  toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
  toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">
        <i class="fas fa-check-circle me-2"></i>
        Đã thêm sản phẩm vào giỏ hàng!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  `;
  
  document.body.appendChild(toast);
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();
  
  // Tự động xóa toast sau 3 giây
  setTimeout(() => {
    toast.remove();
  }, 3000);
  
  // TODO: Thêm logic thực sự để thêm vào giỏ hàng
  console.log('Thêm sản phẩm ID:', productId, 'vào giỏ hàng');
}
</script>
@endsection
