@extends('fontend.layouts.app')

@section('title', 'Sản phẩm')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/style.css') }}">
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
  <link rel="stylesheet" href="{{ asset('fontend/components.css') }}">
  <link rel="stylesheet" href="{{ asset('fontend/pagination.css') }}">
  <style>
    .filter-section {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.1);
      padding: 25px;
      margin-bottom: 30px;
    }
    
    .filter-title {
      color: #198754;
      font-weight: 700;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #e9ecef;
    }
    
    .form-check {
      margin-bottom: 12px;
    }
    
    .form-check-input:checked {
      background-color: #198754;
      border-color: #198754;
    }
    
    .form-check-label {
      font-weight: 500;
      color: #495057;
    }
    
    .search-sort-section {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.1);
      padding: 25px;
      margin-bottom: 30px;
    }
    
    .search-input {
      border: 2px solid #e9ecef;
      border-radius: 25px;
      padding: 12px 20px;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    
    .search-input:focus {
      border-color: #198754;
      box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }
    
    .form-select {
      border: 2px solid #e9ecef;
      border-radius: 10px;
      padding: 12px 15px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .form-select:focus {
      border-color: #198754;
      box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }
    
    .products-grid {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.1);
      padding: 25px;
    }
    
    .product-card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      height: 100%;
    }
    
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .product-image {
      height: 200px;
      object-fit: cover;
      border-radius: 15px 15px 0 0;
    }
    
    .no-image-placeholder {
      height: 200px;
      background: #f8f9fa;
      border-radius: 15px 15px 0 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #6c757d;
    }
    
    .product-title {
      font-size: 16px;
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
      line-height: 1.4;
    }
    
    .product-price {
      font-size: 18px;
      font-weight: 700;
      color: #198754;
      margin-bottom: 15px;
    }
    
    .btn-view-detail {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 12px 24px;
      font-weight: 700;
      font-size: 14px;
      letter-spacing: 0.5px;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      width: 100%;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
      text-transform: uppercase;
    }
    
    .btn-view-detail:hover {
      background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 15px 35px rgba(102, 126, 234, 0.5);
      color: white;
    }
    
    .btn-view-detail:active {
      transform: translateY(-1px) scale(0.98);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-view-detail::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
      transition: left 0.6s ease;
    }
    
    .btn-view-detail:hover::before {
      left: 100%;
    }
    
    .btn-buy-now {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 12px 24px;
      font-weight: 700;
      font-size: 14px;
      letter-spacing: 0.5px;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      min-width: 130px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
      text-transform: uppercase;
    }
    
    .btn-buy-now:hover {
      background: linear-gradient(135deg, #ee5a24 0%, #ff6b6b 100%);
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 15px 35px rgba(255, 107, 107, 0.5);
    }
    
    .btn-buy-now:active {
      transform: translateY(-1px) scale(0.98);
      box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
    }
    
    .btn-buy-now::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
      transition: left 0.6s ease;
    }
    
    .btn-buy-now:hover::before {
      left: 100%;
    }
    
    /* Thêm hiệu ứng pulse cho nút Mua ngay */
    .btn-buy-now::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      background: rgba(255,255,255,0.3);
      border-radius: 50%;
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    
    .btn-buy-now:hover::after {
      width: 300px;
      height: 300px;
    }
    
    /* Cải thiện card sản phẩm */
    .product-card {
      transition: all 0.3s ease;
      border-radius: 20px;
      overflow: hidden;
      position: relative;
    }
    
    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .product-card:hover .btn-view-detail,
    .product-card:hover .btn-buy-now {
      transform: translateY(-2px);
    }
    
    /* Thêm hiệu ứng loading cho nút */
    .btn-buy-now.loading {
      pointer-events: none;
      opacity: 0.7;
    }
    
    .btn-buy-now.loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      margin: -10px 0 0 -10px;
      border: 2px solid transparent;
      border-top: 2px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    /* Responsive cho mobile */
    @media (max-width: 768px) {
      .btn-view-detail,
      .btn-buy-now {
        padding: 10px 16px;
        font-size: 13px;
      }
      
      .btn-buy-now {
        min-width: 110px;
      }
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #6c757d;
    }
    
    .empty-state i {
      font-size: 64px;
      margin-bottom: 20px;
      color: #dee2e6;
    }
    
    .results-info {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 15px 20px;
      margin-bottom: 20px;
      border-left: 4px solid #198754;
    }
    
    .results-count {
      font-weight: 600;
      color: #198754;
    }
  </style>
@endsection

@section('content')
<div class="container py-5">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
      <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
    </ol>
  </nav>

  <div class="row">
    <!-- Sidebar Filter -->
    <div class="col-md-3 mb-4">
      <div class="filter-section">
        <h5 class="filter-title">
          <i class="fas fa-filter me-2"></i>Lọc sản phẩm
        </h5>
        
        <!-- Price Filter -->
        <div class="mb-4">
          <h6 class="fw-bold text-dark mb-3">Mức giá</h6>
          <div class="form-check">
            <input class="form-check-input price-filter" type="checkbox" value="0-100000" id="price1">
            <label class="form-check-label" for="price1">Dưới 100.000₫</label>
          </div>
          <div class="form-check">
            <input class="form-check-input price-filter" type="checkbox" value="100000-200000" id="price2">
            <label class="form-check-label" for="price2">100.000₫ - 200.000₫</label>
          </div>
          <div class="form-check">
            <input class="form-check-input price-filter" type="checkbox" value="200000-500000" id="price3">
            <label class="form-check-label" for="price3">200.000₫ - 500.000₫</label>
          </div>
          <div class="form-check">
            <input class="form-check-input price-filter" type="checkbox" value="500000-" id="price4">
            <label class="form-check-label" for="price4">Trên 500.000₫</label>
          </div>
        </div>
        
        <hr>
        
        <!-- Category Filter -->
        <div class="mb-4">
          <h6 class="fw-bold text-dark mb-3">Danh mục</h6>
          @forelse($categories as $category)
          <div class="form-check">
            <input class="form-check-input category-filter" type="checkbox" value="{{ $category->id }}" id="cat{{ $category->id }}">
              <label class="form-check-label" for="cat{{ $category->id }}">{{ $category->name ?? $category->ten }}</label>
            </div>
          @empty
            <p class="text-muted small">Chưa có danh mục nào</p>
          @endforelse
          </div>
        
        <!-- Clear Filters -->
        <button class="btn btn-outline-secondary btn-sm w-100" onclick="clearFilters()">
          <i class="fas fa-times me-2"></i>Xóa bộ lọc
        </button>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="col-md-9">
      <!-- Search & Sort -->
      <div class="search-sort-section">
        <div class="row align-items-center">
          <div class="col-md-6 mb-3 mb-md-0">
            <input type="text" id="product-search" class="form-control search-input" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search', '') }}">
        </div>
          <div class="col-md-3 mb-3 mb-md-0">
          <select id="sort-filter" class="form-select">
              <option value="id_desc" {{ request('sort') == 'id_desc' ? 'selected' : '' }}>Mới nhất</option>
              <option value="id_asc" {{ request('sort') == 'id_asc' ? 'selected' : '' }}>Cũ nhất</option>
              <option value="gia_asc" {{ request('sort') == 'gia_asc' ? 'selected' : '' }}>Giá tăng dần</option>
              <option value="gia_desc" {{ request('sort') == 'gia_desc' ? 'selected' : '' }}>Giá giảm dần</option>
              <option value="ten_asc" {{ request('sort') == 'ten_asc' ? 'selected' : '' }}>Tên A-Z</option>
              <option value="ten_desc" {{ request('sort') == 'ten_desc' ? 'selected' : '' }}>Tên Z-A</option>
          </select>
        </div>
        <div class="col-md-3">
          <select id="perpage-filter" class="form-select">
              <option value="12" {{ request('perpage') == '12' ? 'selected' : '' }}>12 sản phẩm/trang</option>
              <option value="24" {{ request('perpage') == '24' ? 'selected' : '' }}>24 sản phẩm/trang</option>
              <option value="48" {{ request('perpage') == '48' ? 'selected' : '' }}>48 sản phẩm/trang</option>
              <option value="all" {{ request('perpage') == 'all' ? 'selected' : '' }}>Hiển thị tất cả</option>
          </select>
        </div>
        </div>
      </div>

      <!-- Results Info -->
      <div class="results-info">
        <span class="results-count">
          @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $products->total() }} sản phẩm
          @else
            {{ $products->count() }} sản phẩm
          @endif
        </span>
        @if(request('search'))
          <span class="text-muted"> cho từ khóa "{{ request('search') }}"</span>
        @endif
      </div>

      <!-- Products Grid -->
      <div class="products-grid">
        <div class="row g-4">
          @forelse($products as $product)
            <div class="col-md-4 col-lg-3">
              <div class="card product-card h-100">
                @php
                  // Logic giống như trong quản lý sản phẩm
                  $defaultImage = optional($product->hinhanh->where('is_default', 1)->first())->url;
                  $imageExists = $defaultImage ? file_exists(public_path($defaultImage)) : false;
                @endphp
                @if($defaultImage && $imageExists)
                  <img src="{{ asset($defaultImage) }}" 
                       class="card-img-top product-image" 
                       alt="{{ $product->tenSP }}">
                @else
                  <div class="no-image-placeholder">
                    <i class="fas fa-image fa-3x mb-2"></i>
                    <p class="mb-0 small">Không có ảnh</p>
                  </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                  <!-- Tên danh mục -->
                  @if($product->danhmuc)
                    <div class="mb-2">
                      <span class="badge bg-secondary small">{{ $product->danhmuc->name ?? $product->danhmuc->ten }}</span>
                    </div>
                  @endif
                  
                  <h6 class="product-title">{{ Str::limit($product->tenSP, 50) }}</h6>
                  
                  <!-- Hiển thị giá -->
                  <div class="product-price">
                    @php
                      // Debug: Log product data
                      \Log::info('Product data for ID ' . $product->id, [
                        'base_price' => $product->base_price,
                        'base_sale_price' => $product->base_sale_price,
                        'variants_count' => $product->chitietsanpham ? $product->chitietsanpham->count() : 0,
                        'variants' => $product->chitietsanpham ? $product->chitietsanpham->toArray() : []
                      ]);
                      
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
                        <div class="d-flex flex-column">
                          <span class="text-decoration-line-through text-muted small">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                          <span class="fw-bold text-danger">{{ number_format($baseSalePrice, 0, ',', '.') }} VNĐ</span>
                        </div>
                      @else
                        <span class="fw-bold text-primary">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</span>
                      @endif
                    @elseif(isset($minVariant) && $minVariant > 0)
                      @if($minVariant === $maxVariant)
                        <span class="fw-bold text-primary">{{ number_format($minVariant, 0, ',', '.') }} VNĐ</span>
                      @else
                        <span class="fw-bold text-primary">{{ number_format($minVariant, 0, ',', '.') }} - {{ number_format($maxVariant, 0, ',', '.') }} VNĐ</span>
                      @endif
                @else
                      <span class="text-muted">Liên hệ</span>
                @endif
                  </div>
                  
                  <div class="mt-auto">
                    <div class="d-flex gap-3 align-items-center">
                      <a href="{{ route('product.detail', $product->id) }}" class="btn btn-view-detail flex-fill">
                        <i class="fas fa-eye me-2"></i>Xem chi tiết
                      </a>
                      <button class="btn btn-buy-now" onclick="buyNowFromHomepage({{ $product->id }})">
                        <i class="fas fa-shopping-cart me-2"></i>Mua ngay
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="empty-state">
                <i class="fas fa-search"></i>
                <h5>Không tìm thấy sản phẩm nào</h5>
                <p>Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                <button class="btn btn-primary" onclick="clearFilters()">Xóa bộ lọc</button>
              </div>
            </div>
          @endforelse
        </div>
        
        <!-- Pagination -->
        @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
          <div class="d-flex justify-content-center mt-5">
            {{ $products->links('vendor.pagination.bootstrap-5') }}
          </div>
        @elseif($products instanceof \Illuminate\Database\Eloquent\Collection && $products->count() > 0)
          <div class="d-flex justify-content-center mt-5">
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              Hiển thị tất cả {{ $products->count() }} sản phẩm
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script>
// Enhanced buy now functionality with loading effect
function buyNowFromHomepage(productId) {
  const button = event.target.closest('.btn-buy-now');
  const originalText = button.innerHTML;
  
  // Add loading state
  button.classList.add('loading');
  button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
  
  // Call the original function
  window.buyNowFromHomepage(productId)
    .catch(error => {
      console.error('Buy now error:', error);
      // Reset button on error
      button.classList.remove('loading');
      button.innerHTML = originalText;
    });
}

// Filter and Search functionality
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('product-search');
  const sortFilter = document.getElementById('sort-filter');
  const perpageFilter = document.getElementById('perpage-filter');
  const categoryFilters = document.querySelectorAll('.category-filter');
  const priceFilters = document.querySelectorAll('.price-filter');
  
  let searchTimeout;
  
  // Perform search and filter
  function performSearch() {
    const params = new URLSearchParams();
    
    // Search keyword
    if (searchInput.value.trim()) {
      params.append('search', searchInput.value.trim());
    }
    
    // Sort
    if (sortFilter.value) {
    params.append('sort', sortFilter.value);
    }
    
    // Per page
    if (perpageFilter.value) {
    params.append('perpage', perpageFilter.value);
    }
    
    // Categories
    const selectedCategories = Array.from(categoryFilters)
      .filter(cb => cb.checked)
      .map(cb => cb.value);
    
    if (selectedCategories.length > 0) {
      params.append('category', selectedCategories.join(','));
    }
    
    // Price ranges
    const selectedPrices = Array.from(priceFilters)
      .filter(cb => cb.checked)
      .map(cb => cb.value);
    
    if (selectedPrices.length > 0) {
      params.append('price', selectedPrices.join(','));
    }
    
    // Redirect to filtered page
    const currentUrl = new URL(window.location);
    const newUrl = `${currentUrl.pathname}?${params.toString()}`;
    window.location.href = newUrl;
  }
  
  // Search with debounce
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(performSearch, 800);
  });
  
  // Filter changes
  sortFilter.addEventListener('change', performSearch);
  perpageFilter.addEventListener('change', performSearch);
  
  categoryFilters.forEach(cb => {
    cb.addEventListener('change', performSearch);
  });
  
  priceFilters.forEach(cb => {
    cb.addEventListener('change', performSearch);
  });
});

// Clear all filters
function clearFilters() {
  // Clear search
  document.getElementById('product-search').value = '';
  
  // Clear checkboxes
  document.querySelectorAll('.category-filter').forEach(cb => cb.checked = false);
  document.querySelectorAll('.price-filter').forEach(cb => cb.checked = false);
  
  // Reset selects
  document.getElementById('sort-filter').value = 'id_desc';
  document.getElementById('perpage-filter').value = '12';
  
  // Redirect to clean URL
  window.location.href = window.location.pathname;
}

// Initialize filters from URL parameters
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  
  // Set search value
  const searchValue = urlParams.get('search');
  if (searchValue) {
    document.getElementById('product-search').value = searchValue;
  }
  
  // Set sort value
  const sortValue = urlParams.get('sort');
  if (sortValue) {
    document.getElementById('sort-filter').value = sortValue;
  }
  
  // Set perpage value
  const perpageValue = urlParams.get('perpage');
  if (perpageValue) {
    document.getElementById('perpage-filter').value = perpageValue;
  } else {
    // Default to 12 if no perpage in URL
    document.getElementById('perpage-filter').value = '12';
  }
  
  // Set category checkboxes
  const categoryValue = urlParams.get('category');
  if (categoryValue) {
    const categories = categoryValue.split(',');
    categories.forEach(catId => {
      const checkbox = document.getElementById(`cat${catId}`);
      if (checkbox) checkbox.checked = true;
    });
  }
  
  // Set price checkboxes
  const priceValue = urlParams.get('price');
  if (priceValue) {
    const prices = priceValue.split(',');
    prices.forEach(priceRange => {
      const checkbox = document.querySelector(`input[value="${priceRange}"]`);
      if (checkbox) checkbox.checked = true;
    });
  }
});

// Function addToCart đã được định nghĩa trong layout chính
// Không cần định nghĩa lại ở đây
</script>
@endsection


