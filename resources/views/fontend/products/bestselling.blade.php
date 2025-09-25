@extends('fontend.layouts.app')

@section('title', 'Sản phẩm bán chạy')

@section('css')
<style>
  .hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
  }
  .card {
    border-radius: 15px !important;
  }
  .btn {
    border-radius: 25px !important;
  }
  .badge {
    border-radius: 20px !important;
    font-size: 0.75rem;
  }
  
  /* Fix pagination styling */
  .pagination {
    justify-content: center;
  }
  
  .pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
  }
  
  .pagination .page-item.active .page-link {
    background-color: #198754;
    border-color: #198754;
  }
  
  /* Fix any arrow display issues */
  .arrow-left, .arrow-right {
    display: none !important;
  }
</style>
@endsection

@section('content')
<div class="container py-5">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
      <li class="breadcrumb-item active" aria-current="page">Sản phẩm bán chạy</li>
    </ol>
  </nav>

  <!-- Header -->
  <div class="text-center mb-5">
    <h1 class="fw-bold text-uppercase text-success" style="letter-spacing: 1px;">Sản phẩm bán chạy</h1>
    <p class="text-muted">Những sản phẩm được khách hàng yêu thích nhất</p>
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
          
          <span class="badge bg-success position-absolute top-0 start-0 m-2">Hot</span>
          
          <div class="card-body text-center">
            <h6 class="fw-semibold text-dark mb-2">{{ Str::limit($product->tenSP, 40) }}</h6>
            
            <!-- Hiển thị giá -->
            <div class="mb-3">
              @php
                // Logic giống như trong quản lý sản phẩm
                $basePrice = $product->base_price ?? null;
                $variantPrices = $product->chitietsanpham
                  ->map(function($d){
                    $price = $d->gia_khuyenmai && $d->gia_khuyenmai > 0 ? $d->gia_khuyenmai : $d->gia;
                    return is_null($price) ? null : (float)$price;
                  })
                  ->filter(function($v){ return !is_null($v) && $v >= 0; });
                  
                $minVariant = $variantPrices->min();
                $maxVariant = $variantPrices->max();
              @endphp
              
              @if($basePrice && $basePrice > 0)
                <span class="fw-bold text-primary fs-6">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
              @elseif($variantPrices->count() > 0)
                @if($minVariant === $maxVariant)
                  <span class="fw-bold text-primary fs-6">{{ number_format($minVariant, 0, ',', '.') }} VNĐ</span>
                @else
                  <span class="fw-bold text-primary fs-6">{{ number_format($minVariant, 0, ',', '.') }} - {{ number_format($maxVariant, 0, ',', '.') }} VNĐ</span>
                @endif
              @else
                <span class="fw-bold text-muted fs-6">Liên hệ</span>
              @endif
            </div>
            
            <div class="d-flex justify-content-center gap-2">
              <a href="{{ route('product.detail', $product->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-3">Xem chi tiết</a>
              <button class="btn btn-success btn-sm rounded-pill px-3" onclick="addToCart({{ $product->id }})">Mua ngay</button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <i class="bi bi-fire fs-1 text-muted mb-3"></i>
        <h4 class="text-muted">Chưa có sản phẩm bán chạy nào</h4>
        <p class="text-muted">Hãy quay lại sau để xem những sản phẩm được yêu thích!</p>
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

