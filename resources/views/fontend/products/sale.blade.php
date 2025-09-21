@extends('fontend.layouts.app')

@section('title', 'Sản phẩm khuyến mãi')

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
    background-color: #dc3545;
    border-color: #dc3545;
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
      <li class="breadcrumb-item active" aria-current="page">Sản phẩm khuyến mãi</li>
    </ol>
  </nav>

  <!-- Header -->
  <div class="text-center mb-5">
    <h1 class="fw-bold text-uppercase text-danger" style="letter-spacing: 1px;">Sản phẩm khuyến mãi</h1>
    <p class="text-muted">Những ưu đãi hấp dẫn không thể bỏ qua</p>
  </div>

  <!-- Products Grid -->
  <div class="row g-4">
    @forelse($products as $product)
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card border-0 shadow-lg h-100 position-relative hover-lift">
          @if($product->hinhanh->isNotEmpty())
            @php
              $imageUrl = $product->hinhanh->first()->url;
              if (!str_starts_with($imageUrl, 'storage/')) {
                $imageUrl = 'storage/' . $imageUrl;
              }
            @endphp
            <img src="{{ asset($imageUrl) }}" 
                 class="card-img-top" 
                 style="height: 200px; object-fit: cover; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;" 
                 alt="{{ $product->tenSP }}"
                 onerror="this.src='{{ asset('fontend/img/aosomi1.png') }}'">
          @else
            <img src="{{ asset('fontend/img/aosomi1.png') }}" 
                 class="card-img-top" 
                 style="height: 200px; object-fit: cover; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;" 
                 alt="{{ $product->tenSP }}">
          @endif
          
          <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
          
          <div class="card-body text-center">
            <h6 class="fw-semibold text-dark mb-2">{{ Str::limit($product->tenSP, 40) }}</h6>
            
            <!-- Hiển thị giá -->
            <div class="mb-3">
              @php
                $basePrice = $product->base_price ?? 0;
                $salePrice = $product->base_sale_price ?? 0;
              @endphp
              
              @if($salePrice > 0 && $salePrice < $basePrice)
                <div class="d-flex justify-content-center align-items-center gap-2">
                  <span class="text-decoration-line-through text-muted small">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                  <span class="fw-bold text-danger fs-6">{{ number_format($salePrice, 0, ',', '.') }} VNĐ</span>
                </div>
                @php
                  $discountPercent = round((($basePrice - $salePrice) / $basePrice) * 100);
                @endphp
                <small class="text-success">-{{ $discountPercent }}%</small>
              @elseif($basePrice > 0)
                <span class="fw-bold text-primary fs-6">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
              @else
                <span class="fw-bold text-muted fs-6">Liên hệ</span>
              @endif
            </div>
            
            <div class="d-flex justify-content-center gap-2">
              <a href="{{ route('product.detail', $product->id) }}" class="btn btn-outline-danger btn-sm rounded-pill px-3">Xem chi tiết</a>
              <button class="btn btn-danger btn-sm rounded-pill px-3" onclick="addToCart({{ $product->id }})">Mua ngay</button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <i class="bi bi-tag fs-1 text-muted mb-3"></i>
        <h4 class="text-muted">Chưa có sản phẩm khuyến mãi nào</h4>
        <p class="text-muted">Hãy quay lại sau để xem những ưu đãi hấp dẫn!</p>
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
