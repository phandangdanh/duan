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
    
    /* Loading styles */
    .loading-container {
      min-height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .error-container {
      min-height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }
  </style>
@endsection

@section('content')
<!-- Banner khuyến mãi -->
<section class="banner-khuyen-mai position-relative">
  <img src="{{ asset('fontend/img/6254356 (1).jpg') }}" 
       alt="Banner khuyến mãi" 
       class="w-100 d-block" style="max-height: 180px; object-fit: cover;">
  <button class="btn-close position-absolute top-0 end-0 m-2" aria-label="Đóng" onclick="this.parentElement.style.display='none'"></button>
</section>

<!-- Danh mục nổi bật -->
<section class="danh-muc-noi-bat py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-primary" style="letter-spacing: 1px;">Danh mục sản phẩm nổi bật</h2>
    <div class="row g-4" id="categories-container">
      <!-- Loading state -->
      <div class="col-12 loading-container">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải danh mục...</p>
      </div>
    </div>
  </div>
</section>

<!-- Sản phẩm nổi bật -->
<section class="san-pham-noi-bat py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-black" style="letter-spacing: 1px;">Sản phẩm nổi bật</h2>
    <div class="row g-4" id="featured-products-container">
      <!-- Loading state -->
      <div class="col-12 loading-container">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải sản phẩm nổi bật...</p>
      </div>
    </div>
    
    <!-- Nút xem thêm sản phẩm nổi bật -->
    <div class="text-center mt-4">
      <a href="{{ route('products.featured') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5">
        <i class="bi bi-arrow-right me-2"></i>Xem tất cả sản phẩm nổi bật
      </a>
    </div>
  </div>
</section>

<!-- Sản phẩm khuyến mãi -->
<section class="san-pham-khuyen-mai py-5 bg-white" id="sale-products-section" style="display: none;">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-danger" style="letter-spacing: 1px;">Sản phẩm khuyến mãi</h2>
    <div class="row g-4" id="sale-products-container">
      <!-- Loading state -->
      <div class="col-12 loading-container">
        <div class="spinner-border text-danger" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải sản phẩm khuyến mãi...</p>
      </div>
    </div>
    
    <!-- Nút xem thêm sản phẩm khuyến mãi -->
    <div class="text-center mt-4">
      <a href="{{ route('products.sale') }}" class="btn btn-outline-danger btn-lg rounded-pill px-5">
        <i class="bi bi-arrow-right me-2"></i>Xem tất cả sản phẩm khuyến mãi
      </a>
    </div>
  </div>
</section>

<!-- Sản phẩm bán chạy -->
<section class="san-pham-ban-chay py-5 bg-light" id="best-selling-section" style="display: none;">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold text-uppercase text-success" style="letter-spacing: 1px;">Sản phẩm bán chạy</h2>
    <div class="row g-4" id="best-selling-container">
      <!-- Loading state -->
      <div class="col-12 loading-container">
        <div class="spinner-border text-success" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải sản phẩm bán chạy...</p>
      </div>
    </div>
    
    <!-- Nút xem thêm sản phẩm bán chạy -->
    <div class="text-center mt-4">
      <a href="{{ route('products.bestselling') }}" class="btn btn-outline-success btn-lg rounded-pill px-5">
        <i class="bi bi-arrow-right me-2"></i>Xem tất cả sản phẩm bán chạy
      </a>
    </div>
  </div>
</section>

@endsection

@section('js')
<!-- Home API Manager -->
<script src="{{ asset('fontend/js/home-api.js') }}"></script>

<script>
// Initialize API-based homepage
document.addEventListener('apiReady', function(event) {
    console.log('API is ready, initializing homepage...');
    
    const homeManager = new HomePageManager();
    
    // Load all homepage data
    homeManager.loadAllData();
});

// Fallback if API is not ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for API to initialize
    setTimeout(() => {
        if (typeof window.HomePageManager !== 'undefined') {
            console.log('HomePageManager available, initializing...');
            const homeManager = new HomePageManager();
            homeManager.loadAllData();
        } else {
            console.warn('API not ready, using fallback...');
            // Show error message
            showHomePageError();
        }
    }, 1000);
});

function showHomePageError() {
    const containers = [
        'categories-container',
        'featured-products-container', 
        'sale-products-container',
        'best-selling-container'
    ];
    
    containers.forEach(containerId => {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="col-12 error-container">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-muted">Không thể tải dữ liệu</h5>
                    <p class="text-muted small">Vui lòng tải lại trang hoặc thử lại sau</p>
                    <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                        <i class="fas fa-redo me-1"></i>Tải lại
                    </button>
                </div>
            `;
        }
    });
}
</script>
@endsection
