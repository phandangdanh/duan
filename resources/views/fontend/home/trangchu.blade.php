@extends('fontend.layouts.app')

@section('title', 'Trang chủ')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
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
  </style>
@endsection

@section('content')
<section class="banner-khuyen-mai position-relative">
  <img src="{{ asset('fontend/img/6254356 (1).jpg') }}" 
       alt="Banner khuyến mãi" 
       class="w-100 d-block" style="max-height: 180px; object-fit: cover;">
  <button class="btn-close position-absolute top-0 end-0 m-2" aria-label="Đóng" onclick="this.parentElement.style.display='none'"></button>
</section>

<!-- navbar moved to layout -->

<section class="danh-muc-noi-bat py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-primary" style="letter-spacing: 1px;">Danh mục sản phẩm nổi bật</h2>
    <div class="row g-4">
      @forelse($categories->take(8) as $category)
        <div class="col-6 col-md-3">
          <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
            <div class="border rounded-3 text-center p-4 shadow-sm bg-white h-100 hover-lift">
              <i class="bi bi-grid-3x3-gap fs-1 text-primary mb-3"></i>
              <h6 class="mb-0 text-dark">{{ $category->name }}</h6>
            </div>
          </a>
        </div>
      @empty
        <div class="col-12 text-center">
          <p class="text-muted">Chưa có danh mục nào</p>
        </div>
      @endforelse
    </div>
  </div>
</section>

<section class="san-pham-noi-bat py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-black" style="letter-spacing: 1px;">Sản phẩm nổi bật</h2>
    <div class="row g-4">
      @forelse($featuredProducts->take(8) as $product)
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
              <!-- Tên danh mục -->
              @if($product->danhmuc)
                <div class="mb-2">
                  <span class="badge bg-secondary small">{{ $product->danhmuc->name ?? $product->danhmuc->ten }}</span>
                </div>
              @endif
              
              <h6 class="fw-semibold text-dark mb-2">{{ Str::limit($product->tenSP, 40) }}</h6>
              
              <!-- Hiển thị giá -->
              <div class="mb-3">
                @php
                  // Logic giống như trong quản lý sản phẩm
                  $basePrice = $product->base_price ?? null;
                  $baseSalePrice = $product->base_sale_price ?? null;
                  
                  // Ưu tiên base_price nếu có
                  if ($basePrice && $basePrice > 0) {
                    $displayPrice = $basePrice;
                    $isSale = $baseSalePrice && $baseSalePrice > 0 && $baseSalePrice < $basePrice;
                  } else {
                    // Fallback về variant prices
                    $variantPrices = $product->chitietsanpham
                      ->map(function($d){
                        $price = $d->gia_khuyenmai && $d->gia_khuyenmai > 0 ? $d->gia_khuyenmai : $d->gia;
                        return is_null($price) ? null : (float)$price;
                      })
                      ->filter(function($v){ return !is_null($v) && $v >= 0; });
                      
                    $minVariant = $variantPrices->min();
                    $maxVariant = $variantPrices->max();
                    $displayPrice = $minVariant;
                    $isSale = false;
                  }
                @endphp
                
                @if($basePrice && $basePrice > 0)
                  @if($isSale)
                    <div class="d-flex flex-column align-items-center">
                      <span class="text-decoration-line-through text-muted small">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                      <span class="fw-bold text-danger fs-6">{{ number_format($baseSalePrice, 0, ',', '.') }} VNĐ</span>
                    </div>
                  @else
                    <span class="fw-bold text-primary fs-6">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                  @endif
                @elseif(isset($minVariant) && $minVariant > 0)
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
                <a href="{{ route('product.detail', $product->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Xem chi tiết</a>
                <button class="btn btn-danger btn-sm rounded-pill px-3" onclick="buyNowFromHomepage({{ $product->id }})">Mua ngay</button>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-center">
          <p class="text-muted">Chưa có sản phẩm nào</p>
        </div>
      @endforelse
    </div>
    
    <!-- Nút xem thêm sản phẩm nổi bật -->
    <div class="text-center mt-4">
      <a href="{{ route('products.featured') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5">
        <i class="bi bi-arrow-right me-2"></i>Xem tất cả sản phẩm nổi bật
      </a>
    </div>
  </div>
</section>

<!-- Thêm section sản phẩm khuyến mãi -->
@if($saleProducts->isNotEmpty())
<section class="san-pham-khuyen-mai py-5 bg-white">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-danger" style="letter-spacing: 1px;">Sản phẩm khuyến mãi</h2>
    <div class="row g-4">
      @foreach($saleProducts->take(6) as $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-lg h-100 position-relative hover-lift">
            @php
              // Logic giống như trong sản phẩm nổi bật
              $defaultImage = optional($product->hinhanh->where('is_default', 1)->first())->url;
              $imageExists = $defaultImage ? file_exists(public_path($defaultImage)) : false;
            @endphp
            @if($defaultImage && $imageExists)
              <img src="{{ asset($defaultImage) }}" 
                   class="card-img-top" 
                   style="height: 150px; object-fit: cover; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;" 
                   alt="{{ $product->tenSP }}">
            @else
              <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                   style="height: 150px; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                <div class="text-center text-muted">
                  <i class="fas fa-image fa-2x mb-2"></i>
                  <p class="mb-0 small">Không có ảnh</p>
                </div>
              </div>
            @endif
            
            <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
            
            <div class="card-body text-center p-2">
              <!-- Tên danh mục -->
              @if($product->danhmuc)
                <div class="mb-2">
                  <span class="badge bg-secondary small">{{ $product->danhmuc->name ?? $product->danhmuc->ten }}</span>
                </div>
              @endif
              
              <h6 class="fw-semibold text-dark">{{ Str::limit($product->tenSP, 25) }}</h6>
              
              <div class="mb-2">
                @php
                  $basePrice = $product->base_price ?? 0;
                  $salePrice = $product->base_sale_price ?? 0;
                @endphp
                
                @if($salePrice > 0 && $salePrice < $basePrice)
                  <div class="d-flex flex-column align-items-center">
                    <span class="text-decoration-line-through text-muted small">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                    <span class="fw-bold text-danger small">{{ number_format($salePrice, 0, ',', '.') }} VNĐ</span>
                  </div>
                  @php
                    $discountPercent = round((($basePrice - $salePrice) / $basePrice) * 100);
                  @endphp
                  <small class="text-success">-{{ $discountPercent }}%</small>
                @elseif($basePrice > 0)
                  <span class="fw-bold text-primary small">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                @else
                  <span class="fw-bold text-muted small">Liên hệ</span>
                @endif
              </div>
              
              <div class="d-flex gap-2">
                <a href="{{ route('product.detail', $product->id) }}" class="btn btn-outline-primary btn-sm flex-fill">Xem chi tiết</a>
                <button class="btn btn-danger btn-sm" onclick="buyNowFromHomepage({{ $product->id }})">Mua ngay</button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    
    <!-- Nút xem thêm sản phẩm khuyến mãi -->
    <div class="text-center mt-4">
      <a href="{{ route('products.sale') }}" class="btn btn-outline-danger btn-lg rounded-pill px-5">
        <i class="bi bi-arrow-right me-2"></i>Xem tất cả sản phẩm khuyến mãi
      </a>
    </div>
  </div>
</section>
@endif

<!-- Thêm section sản phẩm bán chạy -->
@if($bestSellingProducts->isNotEmpty())
<section class="san-pham-ban-chay py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-success" style="letter-spacing: 1px;">Sản phẩm bán chạy</h2>
    <div class="row g-4">
      @foreach($bestSellingProducts->take(6) as $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-lg h-100 position-relative hover-lift">
            @php
              // Logic giống như trong quản lý sản phẩm
              $defaultImage = optional($product->hinhanh->where('is_default', 1)->first())->url;
              $imageExists = $defaultImage ? file_exists(public_path($defaultImage)) : false;
            @endphp
            @if($defaultImage && $imageExists)
              <img src="{{ asset($defaultImage) }}" 
                   class="card-img-top" 
                   style="height: 150px; object-fit: cover; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;" 
                   alt="{{ $product->tenSP }}">
            @else
              <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                   style="height: 150px; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                <div class="text-center text-muted">
                  <i class="fas fa-image fa-2x mb-2"></i>
                  <p class="mb-0 small">Không có ảnh</p>
                </div>
              </div>
            @endif
            
            <span class="badge bg-success position-absolute top-0 start-0 m-2">Hot</span>
            
            <div class="card-body text-center p-2">
              <!-- Tên danh mục -->
              @if($product->danhmuc)
                <div class="mb-2">
                  <span class="badge bg-secondary small">{{ $product->danhmuc->name ?? $product->danhmuc->ten }}</span>
                </div>
              @endif
              
              <h6 class="fw-semibold text-dark">{{ Str::limit($product->tenSP, 25) }}</h6>
              
              <!-- Hiển thị giá -->
              <div class="mb-2">
                @php
                  // Logic giống như trong quản lý sản phẩm
                  $basePrice = $product->base_price ?? null;
                  $baseSalePrice = $product->base_sale_price ?? null;
                  
                  // Ưu tiên base_price nếu có
                  if ($basePrice && $basePrice > 0) {
                    $displayPrice = $basePrice;
                    $isSale = $baseSalePrice && $baseSalePrice > 0 && $baseSalePrice < $basePrice;
                  } else {
                    // Fallback về variant prices
                    $variantPrices = $product->chitietsanpham
                      ->map(function($d){
                        $price = $d->gia_khuyenmai && $d->gia_khuyenmai > 0 ? $d->gia_khuyenmai : $d->gia;
                        return is_null($price) ? null : (float)$price;
                      })
                      ->filter(function($v){ return !is_null($v) && $v >= 0; });
                      
                    $minVariant = $variantPrices->min();
                    $maxVariant = $variantPrices->max();
                    $displayPrice = $minVariant;
                    $isSale = false;
                  }
                @endphp
                
                @if($basePrice && $basePrice > 0)
                  @if($isSale)
                    <div class="d-flex flex-column align-items-center">
                      <span class="text-decoration-line-through text-muted small">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                      <span class="fw-bold text-danger small">{{ number_format($baseSalePrice, 0, ',', '.') }} VNĐ</span>
                    </div>
                  @else
                    <span class="fw-bold text-primary small">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                  @endif
                @elseif(isset($minVariant) && $minVariant > 0)
                  @if($minVariant === $maxVariant)
                    <span class="fw-bold text-primary small">{{ number_format($minVariant, 0, ',', '.') }} VNĐ</span>
                  @else
                    <span class="fw-bold text-primary small">{{ number_format($minVariant, 0, ',', '.') }} - {{ number_format($maxVariant, 0, ',', '.') }} VNĐ</span>
                  @endif
                @else
                  <span class="fw-bold text-muted small">Liên hệ</span>
                @endif
              </div>
              
              <div class="d-flex gap-2">
                <a href="{{ route('product.detail', $product->id) }}" class="btn btn-outline-success btn-sm flex-fill">Xem chi tiết</a>
                <button class="btn btn-success btn-sm" onclick="buyNowFromHomepage({{ $product->id }})">Mua ngay</button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    
    <!-- Nút xem thêm sản phẩm bán chạy -->
    <div class="text-center mt-4">
      <a href="{{ route('products.bestselling') }}" class="btn btn-outline-success btn-lg rounded-pill px-5">
        <i class="bi bi-arrow-right me-2"></i>Xem tất cả sản phẩm bán chạy
      </a>
    </div>
  </div>
</section>
@endif


<script>
// Function addToCart đã được định nghĩa trong layout chính
// Không cần định nghĩa lại ở đây
</script>
@endsection


