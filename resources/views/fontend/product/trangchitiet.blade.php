@extends('fontend.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
  <style>
    .product-detail-container {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    
    .product-image-section {
      background: #f8f9fa;
      padding: 30px;
    }
    
    .main-product-image {
      width: 100%;
      height: 400px;
      object-fit: contain;
      border-radius: 10px;
      background: #fff;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .thumbnail-gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    
    .thumbnail {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      cursor: pointer;
      border: 2px solid transparent;
      transition: all 0.3s ease;
    }
    
    .thumbnail:hover, .thumbnail.active {
      border-color: #198754;
      transform: scale(1.05);
    }
    
    .product-info-section {
      padding: 30px;
    }
    
    .product-title {
      font-size: 28px;
      font-weight: 700;
      color: #333;
      margin-bottom: 15px;
    }
    
    .product-rating {
      margin-bottom: 20px;
    }
    
    .price-section {
      margin-bottom: 25px;
    }
    
    .current-price {
      font-size: 32px;
      font-weight: 700;
      color: #198754;
    }
    
    .original-price {
      font-size: 20px;
      color: #6c757d;
      text-decoration: line-through;
      margin-left: 10px;
    }
    
    .discount-badge {
      background: #dc3545;
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 600;
      margin-left: 10px;
    }
    
    .contact-price {
      font-size: 24px;
      color: #6c757d;
      font-weight: 600;
    }
    
    .variant-section {
      margin-bottom: 25px;
    }
    
    .variant-group {
      margin-bottom: 20px;
    }
    
    .variant-label {
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
      display: block;
    }
    
    .variant-options {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .variant-option {
      padding: 8px 16px;
      border: 2px solid #dee2e6;
      border-radius: 25px;
      background: #fff;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .color-preview {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      border: 2px solid #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .variant-option:hover {
      border-color: #198754;
      color: #198754;
    }
    
    .variant-option.active {
      background: #198754;
      color: white;
      border-color: #198754;
    }
    
    .quantity-section {
      margin-bottom: 25px;
    }
    
    .quantity-input {
      width: 80px;
      text-align: center;
      border: 2px solid #dee2e6;
      border-radius: 8px;
      padding: 8px;
    }
    
    .action-buttons {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    
    .btn-buy-now {
      background: #198754;
      color: white;
      padding: 12px 30px;
      border-radius: 25px;
      font-weight: 600;
      border: none;
      transition: all 0.3s ease;
    }
    
    .btn-buy-now:hover {
      background: #157347;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
    }
    
    .btn-add-cart {
      background: #ffc107;
      color: #212529;
      padding: 12px 30px;
      border-radius: 25px;
      font-weight: 600;
      border: none;
      transition: all 0.3s ease;
    }
    
    .btn-add-cart:hover {
      background: #e0a800;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
    }
    
    .no-image-placeholder {
      width: 100%;
      height: 400px;
      background: #f8f9fa;
      border: 2px dashed #dee2e6;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #6c757d;
    }
    
    .no-image-placeholder i {
      font-size: 48px;
      margin-bottom: 15px;
    }
    
    /* Breadcrumb */
    .breadcrumb {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 15px 20px;
    }
    
    .breadcrumb-item a {
      color: #198754;
      text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
      text-decoration: underline;
    }
    
    /* Product code */
    .product-code .badge {
      font-size: 14px;
      padding: 8px 12px;
    }
    
    /* Quantity controls */
    .quantity-controls {
      gap: 10px;
    }
    
    .quantity-controls .btn {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
    }
    
    /* Additional info */
    .additional-info {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
      margin-top: 20px;
    }
    
    .info-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      padding: 8px 0;
    }
    
    .info-item i {
      font-size: 16px;
    }
    
    /* Tabs */
    .product-details-tabs {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.1);
      overflow: hidden;
      margin-top: 30px;
    }
    
    .nav-tabs {
      border-bottom: 2px solid #e9ecef;
      background: #f8f9fa;
      margin: 0;
    }
    
    .nav-tabs .nav-link {
      border: none;
      border-radius: 0;
      padding: 15px 25px;
      font-weight: 600;
      color: #6c757d;
      transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
      color: #198754;
      background: #e9ecef;
    }
    
    .nav-tabs .nav-link.active {
      color: #198754;
      background: #fff;
      border-bottom: 3px solid #198754;
    }
    
    .tab-content {
      background: #fff;
    }
    
    .description-content {
      line-height: 1.8;
      color: #495057;
    }
    
    /* Table styling */
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: #f8f9fa;
    }
    
    .table td {
      padding: 12px 15px;
      vertical-align: middle;
    }
    
    .table td:first-child {
      width: 200px;
      font-weight: 600;
      color: #495057;
    }
    
    /* Reviews section */
    .reviews-placeholder {
      text-align: center;
      padding: 60px 20px;
    }
    
    .reviews-placeholder i {
      color: #ffc107;
      margin-bottom: 20px;
    }
    
    /* Variant details table */
    .table-bordered {
      border: 1px solid #dee2e6;
    }
    
    .table-bordered th,
    .table-bordered td {
      border: 1px solid #dee2e6;
    }
    
    .table-primary th {
      background-color: #198754;
      color: white;
      font-weight: 600;
    }
    
    .table-hover tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    /* Color preview */
    .color-preview {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Statistics cards */
    .stat-card {
      border: 1px solid #e9ecef;
      transition: all 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .stat-card h4 {
      font-weight: 700;
    }
    
    /* Section headers */
    .section-header {
      border-bottom: 2px solid #198754;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    
    .section-header h5 {
      margin: 0;
      font-weight: 600;
    }
    
    /* Color and Size options */
    .color-option, .size-option {
      transition: all 0.3s ease;
    }
    
    .size-option.disabled {
      opacity: 0.3 !important;
      cursor: not-allowed !important;
      background-color: #f8f9fa !important;
      color: #6c757d !important;
      pointer-events: none;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .product-detail-container {
        margin: 0 -15px;
        border-radius: 0;
      }
      
      .product-image-section,
      .product-info-section {
        padding: 20px;
      }
      
      .main-product-image {
        height: 300px;
      }
      
      .thumbnail-gallery {
        grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
        gap: 10px;
      }
      
      .thumbnail {
        width: 60px;
        height: 60px;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .btn-buy-now,
      .btn-add-cart {
        width: 100%;
        margin-bottom: 10px;
      }
    }
  </style>
@endsection

@section('content')
<div class="container py-5">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
      @if($product->danhmuc)
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">{{ $product->danhmuc->name }}</a></li>
      @endif
      <li class="breadcrumb-item active" aria-current="page">{{ $product->tenSP }}</li>
    </ol>
  </nav>

  <div class="product-detail-container">
            <div class="text-center mb-4">
              <h1 class="display-6 fw-bold text-dark">Chi tiết sản phẩm</h1>
              @if($product->danhmuc)
                <div class="mt-2">
                  <span class="badge bg-secondary fs-6">{{ $product->danhmuc->name }}</span>
                </div>
              @endif
            </div>
    <div class="row g-0">
      <!-- Hình ảnh sản phẩm -->
      <div class="col-md-6">
        <div class="product-image-section">
          @php
            // Lấy ảnh chính (is_default = 1) hoặc ảnh đầu tiên
            $mainImage = $product->hinhanh->where('is_default', 1)->first() ?? $product->hinhanh->first();
            $imageExists = $mainImage ? file_exists(public_path($mainImage->url)) : false;
          @endphp
          
          @if($mainImage && $imageExists)
            <img id="main-product-image" 
                 src="{{ asset($mainImage->url) }}" 
                 alt="{{ $product->tenSP }}" 
                 class="main-product-image">
          @else
            <div class="no-image-placeholder">
              <i class="fas fa-image"></i>
              <h5>Không có ảnh</h5>
              <p>Sản phẩm chưa có hình ảnh</p>
            </div>
          @endif
          
          <!-- Thumbnail gallery -->
          @if($product->hinhanh->count() > 1)
            <div class="thumbnail-gallery">
              @foreach($product->hinhanh as $index => $image)
                @php
                  $thumbExists = file_exists(public_path($image->url));
                @endphp
                @if($thumbExists)
                  <img src="{{ asset($image->url) }}" 
                       class="thumbnail {{ $index === 0 ? 'active' : '' }}" 
                       onclick="changeMainImage('{{ asset($image->url) }}', this)"
                       alt="Thumbnail {{ $index + 1 }}">
                @endif
              @endforeach
            </div>
          @endif
        </div>
      </div>
      
      <!-- Thông tin sản phẩm -->
      <div class="col-md-6">
        <div class="product-info-section">
          <!-- Tên sản phẩm -->
          <h1 class="product-title">{{ $product->tenSP }}</h1>
          
          <!-- Mã sản phẩm -->
          <div class="product-code mb-3">
            <span class="badge bg-info fs-6">Mã: {{ $product->maSP }}</span>
          </div>
          
          <!-- Rating -->
          <div class="product-rating mb-3">
            <div class="text-warning fs-4">★★★★★</div>
            <span class="text-muted">52 đánh giá</span>
          </div>
          
          <!-- Giá sản phẩm -->
          <div class="price-section mb-4">
            <div id="product-price-display">
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
                <div class="current-price">{{ number_format($basePrice, 0, ',', '.') }} VNĐ</div>
              @elseif($variantPrices->count() > 0)
                @if($minVariant === $maxVariant)
                  <div class="current-price">{{ number_format($minVariant, 0, ',', '.') }} VNĐ</div>
                @else
                  <div class="current-price">{{ number_format($minVariant, 0, ',', '.') }} - {{ number_format($maxVariant, 0, ',', '.') }} VNĐ</div>
                @endif
              @else
                <div class="contact-price">Liên hệ</div>
              @endif
            </div>
          </div>
          
          <!-- Mô tả ngắn -->
          @if($product->moTa)
            <div class="product-description mb-4">
              <h6 class="fw-bold mb-2">Mô tả sản phẩm:</h6>
              <p class="text-muted">{{ Str::limit($product->moTa, 200) }}</p>
            </div>
          @endif
          
          <!-- Màu sắc -->
          @php
            $colors = $product->chitietsanpham->whereNotNull('id_mausac')->pluck('mausac')->filter()->unique('id');
          @endphp
          @if($colors->count() > 0)
            <div class="variant-section mb-4">
              <div class="variant-group">
                <label class="variant-label">Màu sắc:</label>
                <div class="variant-options">
                  @foreach($colors as $color)
                    <div class="variant-option" data-color="{{ $color->ten }}" data-color-code="{{ $color->ma_mau ?? '#ccc' }}">
                      <div class="color-preview" style="background-color: {{ $color->ma_mau ?? '#ccc' }}"></div>
                      {{ $color->ten }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif
          
          <!-- Size -->
          @php
            $sizes = $product->chitietsanpham->whereNotNull('id_size')->pluck('size')->filter()->unique('id');
          @endphp
          @if($sizes->count() > 0)
            <div class="variant-section mb-4">
              <div class="variant-group">
                <label class="variant-label">Size:</label>
                <div class="variant-options">
                  @foreach($sizes as $size)
                    <div class="variant-option" data-size="{{ $size->ten }}">
                      {{ $size->ten }}
    </div>
                  @endforeach
    </div>
  </div>
            </div>
          @endif
          
          <!-- Số lượng -->
          <div class="quantity-section mb-4">
            <label class="variant-label">Số lượng:</label>
            <div class="quantity-controls d-flex align-items-center">
              <button class="btn btn-outline-secondary" onclick="decreaseQuantity()">-</button>
              <input type="number" class="quantity-input" min="0" value="1" id="quantity" onchange="checkStock()">
              <button class="btn btn-outline-secondary" onclick="increaseQuantity()">+</button>
            </div>
            <div id="stock-info" class="mt-2">
              <small class="text-muted">
                <strong>Số lượng có sẵn: <span id="available-stock">0</span></strong>
                <br>
                <span class="text-info">• Sản phẩm chính hiện có: <span id="main-stock">{{ $product->soLuong ?? 0 }}</span> sản phẩm</span>
                <br>
                <span class="text-success">• Biến thể hiện có: <span id="variant-stock">0</span> sản phẩm</span>
              </small>
            </div>
            <div id="stock-error" class="mt-2" style="display: none;">
              <small class="text-danger">Số lượng vượt quá tồn kho!</small>
            </div>
          </div>
          
          <!-- Nút hành động -->
          <div class="action-buttons mb-4">
            <button class="btn-buy-now" onclick="buyNow()">
              <i class="fas fa-shopping-cart me-2"></i>Mua ngay
            </button>
            <button class="btn-add-cart" onclick="addToCartFromDetail()">
              <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
            </button>
</div>

          <!-- Thông tin bổ sung -->
          <div class="additional-info">
    <div class="row">
              <div class="col-6">
                <div class="info-item">
                  <i class="fas fa-truck text-success me-2"></i>
                  <span class="small">Miễn phí vận chuyển</span>
                </div>
              </div>
              <div class="col-6">
                <div class="info-item">
                  <i class="fas fa-undo text-success me-2"></i>
                  <span class="small">Đổi trả 30 ngày</span>
                </div>
              </div>
              <div class="col-6">
                <div class="info-item">
                  <i class="fas fa-shield-alt text-success me-2"></i>
                  <span class="small">Bảo hành chính hãng</span>
                </div>
              </div>
              <div class="col-6">
                <div class="info-item">
                  <i class="fas fa-headset text-success me-2"></i>
                  <span class="small">Hỗ trợ 24/7</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Chi tiết sản phẩm -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="product-details-tabs">
          <ul class="nav nav-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                <i class="fas fa-info-circle me-2"></i>Mô tả chi tiết
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                <i class="fas fa-list me-2"></i>Thông số kỹ thuật
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                <i class="fas fa-star me-2"></i>Đánh giá (52)
              </button>
            </li>
        </ul>
          
          <div class="tab-content" id="productTabsContent">
            <!-- Mô tả chi tiết -->
            <div class="tab-pane fade show active" id="description" role="tabpanel">
              <div class="p-4">
                @if($product->moTa)
                  <div class="description-content">
                    {!! nl2br(e($product->moTa)) !!}
                  </div>
                @else
                  <p class="text-muted">Sản phẩm chưa có mô tả chi tiết.</p>
                @endif
              </div>
            </div>
            
            <!-- Thông số kỹ thuật -->
            <div class="tab-pane fade" id="specifications" role="tabpanel">
              <div class="p-4">
                <!-- Thông tin cơ bản -->
                <h5 class="mb-3 text-primary">
                  <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                </h5>
                <div class="table-responsive mb-4">
                  <table class="table table-striped">
                    <tbody>
                      <tr>
                        <td class="fw-bold">Tên sản phẩm</td>
                        <td>{{ $product->tenSP }}</td>
                      </tr>
                      <tr>
                        <td class="fw-bold">Mã sản phẩm</td>
                        <td>{{ $product->maSP }}</td>
                      </tr>
                      <tr>
                        <td class="fw-bold">Danh mục</td>
                        <td>{{ $product->danhmuc->name ?? 'Chưa phân loại' }}</td>
                      </tr>
                      <tr>
                        <td class="fw-bold">Trạng thái</td>
                        <td>
                          @if($product->trangthai == 1)
                            <span class="badge bg-success">Đang kinh doanh</span>
                          @else
                            <span class="badge bg-danger">Ngừng kinh doanh</span>
                          @endif
                        </td>
                      </tr>
                      @if($product->base_price)
                        <tr>
                          <td class="fw-bold">Giá gốc</td>
                          <td>{{ number_format($product->base_price, 0, ',', '.') }} VNĐ</td>
                        </tr>
                      @endif
                      @if($product->base_sale_price)
                        <tr>
                          <td class="fw-bold">Giá khuyến mãi</td>
                          <td class="text-danger">{{ number_format($product->base_sale_price, 0, ',', '.') }} VNĐ</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>

                <!-- Chi tiết sản phẩm theo biến thể -->
                @if($product->chitietsanpham && $product->chitietsanpham->count() > 0)
                  <h5 class="mb-3 text-primary">
                    <i class="fas fa-list-alt me-2"></i>Chi tiết sản phẩm theo biến thể
                  </h5>
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <thead class="table-primary">
                        <tr>
                          <th class="text-center">STT</th>
                          <th class="text-center">Mã chi tiết</th>
                          <th class="text-center">Màu sắc</th>
                          <th class="text-center">Size</th>
                          <th class="text-center">Số lượng</th>
                          <th class="text-center">Giá bán</th>
                          <th class="text-center">Giá khuyến mãi</th>
                          <th class="text-center">Trạng thái</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($product->chitietsanpham as $index => $variant)
                          <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">
                              <span class="badge bg-secondary">SP{{ str_pad($variant->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="text-center">
                              @if($variant->mausac)
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                  <span class="color-preview" style="
                                    width: 20px;
                                    height: 20px;
                                    border-radius: 50%;
                                    display: inline-block;
                                    background-color: {{ $variant->mausac->mota ?? '#ccc' }};
                                    border: 2px solid #ddd;
                                  "></span>
                                  <span>{{ $variant->mausac->ten }}</span>
                                </div>
                              @else
                                <span class="text-muted">-</span>
                              @endif
                            </td>
                            <td class="text-center">
                              @if($variant->size)
                                <span class="badge bg-info">{{ $variant->size->ten }}</span>
                              @else
                                <span class="text-muted">-</span>
                              @endif
                            </td>
                            <td class="text-center">
                              @if($variant->soluong > 0)
                                <span class="badge bg-success">{{ number_format($variant->soluong) }}</span>
                              @else
                                <span class="badge bg-danger">Hết hàng</span>
                              @endif
                            </td>
                            <td class="text-center">
                              @if($variant->gia && $variant->gia > 0)
                                <strong>{{ number_format($variant->gia, 0, ',', '.') }} VNĐ</strong>
                              @else
                                @if($product->base_price)
                                  <span class="text-muted">Theo giá gốc</span>
                                @else
                                  <span class="text-muted">-</span>
                                @endif
                              @endif
                            </td>
                            <td class="text-center">
                              @if($variant->gia_khuyenmai && $variant->gia_khuyenmai > 0)
                                <strong class="text-danger">{{ number_format($variant->gia_khuyenmai, 0, ',', '.') }} VNĐ</strong>
                              @else
                                <span class="text-muted">-</span>
                              @endif
                            </td>
                            <td class="text-center">
                              @if($variant->soluong > 0)
                                <span class="badge bg-success">Còn hàng</span>
                              @else
                                <span class="badge bg-danger">Hết hàng</span>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  
                  <!-- Tóm tắt thống kê -->
                  <div class="row mt-4">
                    <div class="col-md-3">
                      <div class="stat-card text-center p-3 bg-light rounded">
                        <h6 class="text-muted mb-1">Tổng biến thể</h6>
                        <h4 class="text-primary mb-0">{{ $product->chitietsanpham->count() }}</h4>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="stat-card text-center p-3 bg-light rounded">
                        <h6 class="text-muted mb-1">Còn hàng</h6>
                        <h4 class="text-success mb-0">{{ $product->chitietsanpham->where('soluong', '>', 0)->count() }}</h4>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="stat-card text-center p-3 bg-light rounded">
                        <h6 class="text-muted mb-1">Hết hàng</h6>
                        <h4 class="text-danger mb-0">{{ $product->chitietsanpham->where('soluong', '<=', 0)->count() }}</h4>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="stat-card text-center p-3 bg-light rounded">
                        <h6 class="text-muted mb-1">Có khuyến mãi</h6>
                        <h4 class="text-warning mb-0">{{ $product->chitietsanpham->where('gia_khuyenmai', '>', 0)->count() }}</h4>
                      </div>
                    </div>
                  </div>
                @else
                  <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Sản phẩm chưa có biến thể màu sắc và size
                  </div>
                @endif
              </div>
            </div>
            
            <!-- Đánh giá -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
              <div class="p-4">
                <div class="text-center py-5">
                  <i class="fas fa-star fa-3x text-warning mb-3"></i>
                  <h5>Chưa có đánh giá nào</h5>
                  <p class="text-muted">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                  <button class="btn btn-primary">Viết đánh giá</button>
                </div>
              </div>
            </div>
          </div>
      </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
// Thay đổi ảnh chính khi click thumbnail
function changeMainImage(imageUrl, thumbnail) {
  const mainImage = document.getElementById('main-product-image');
  if (mainImage) {
    mainImage.src = imageUrl;
  }
  
  // Cập nhật trạng thái active
  document.querySelectorAll('.thumbnail').forEach(thumb => {
    thumb.classList.remove('active');
  });
  thumbnail.classList.add('active');
}

// Dữ liệu variant từ server
const productVariants = {!! json_encode($product->chitietsanpham->map(function($variant) {
  return [
    'id' => $variant->id,
    'color_id' => $variant->id_mausac,
    'color_name' => $variant->mausac ? $variant->mausac->ten : null,
    'color_code' => $variant->mausac ? $variant->mausac->ma_mau : null,
    'size_id' => $variant->id_size,
    'size_name' => $variant->size ? $variant->size->ten : null,
    'price' => $variant->gia,
    'sale_price' => $variant->gia_khuyenmai,
    'stock' => $variant->soLuong  // Sửa từ so_luong thành soLuong
  ];
})) !!};

// Xử lý chọn màu sắc
document.addEventListener('DOMContentLoaded', function() {
  // Debug: Log dữ liệu variants (có thể bật lại khi cần debug)
  console.log('Product variants:', productVariants);
  console.log('Product variants length:', productVariants.length);
  
  // Test function để kiểm tra filter
  window.testFilter = function(color, size) {
    console.log('=== TESTING FILTER ===');
    console.log('Testing with color:', color, 'size:', size);
    console.log('All variants:', productVariants);
    
    let testVariants = [...productVariants];
    
    if (color) {
      console.log('Filtering by color:', color);
      testVariants = testVariants.filter(v => {
        const match = v.color_name === color;
        console.log(`Color check: "${v.color_name}" === "${color}" = ${match}`);
        return match;
      });
      console.log('After color filter:', testVariants);
    }
    
    if (size) {
      console.log('Filtering by size:', size);
      testVariants = testVariants.filter(v => {
        const match = v.size_name === size;
        console.log(`Size check: "${v.size_name}" === "${size}" = ${match}`);
        return match;
      });
      console.log('After size filter:', testVariants);
    }
    
    if (testVariants.length > 0) {
      console.log('✅ Found variant:', testVariants[0]);
      console.log('✅ Stock:', testVariants[0].stock);
      return testVariants[0];
    } else {
      console.log('❌ No matching variant found');
      return null;
    }
  };
  
  // Test function để kiểm tra tất cả dữ liệu
  window.debugAll = function() {
    console.log('=== DEBUG ALL DATA ===');
    console.log('Product variants:', productVariants);
    console.log('Variants count:', productVariants.length);
    
    productVariants.forEach((v, i) => {
      console.log(`Variant ${i}:`, {
        id: v.id,
        color_name: v.color_name,
        size_name: v.size_name,
        stock: v.stock,
        price: v.price
      });
    });
    
    // Test các combination có thể
    const colors = [...new Set(productVariants.map(v => v.color_name))];
    const sizes = [...new Set(productVariants.map(v => v.size_name))];
    
    console.log('Available colors:', colors);
    console.log('Available sizes:', sizes);
    
    colors.forEach(color => {
      sizes.forEach(size => {
        const result = window.testFilter(color, size);
        if (result) {
          console.log(`✅ ${color} + ${size} = Stock: ${result.stock}`);
        } else {
          console.log(`❌ ${color} + ${size} = No match`);
        }
      });
    });
  };
  
  // Khởi tạo hiển thị tồn kho - sử dụng getDisplayStock từ CartService
  function updateStockDisplay() {
    // Gọi API để lấy số lượng hiển thị từ CartService
    fetch(`/api/cart/display-stock/{{ $product->id }}`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('available-stock').textContent = data.totalStock;
        document.getElementById('main-stock').textContent = data.mainStock;
        document.getElementById('variant-stock').textContent = data.variantStock;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      // Fallback về tính toán cũ
      const mainProductStock = {{ $product->soLuong ?? 0 }};
      const variantStock = productVariants.reduce((sum, variant) => {
    const stock = parseInt(variant.stock) || 0;
    return sum + stock;
  }, 0);
      const totalStock = mainProductStock + variantStock;
      
  document.getElementById('available-stock').textContent = totalStock;
      document.getElementById('main-stock').textContent = mainProductStock;
      document.getElementById('variant-stock').textContent = variantStock;
    });
  }
  
  // Khởi tạo hiển thị
  updateStockDisplay();
  
  // Function để cập nhật hiển thị size theo màu đã chọn
  function updateSizeOptions() {
    const selectedColor = document.querySelector('[data-color].active');
    const sizeOptions = document.querySelectorAll('[data-size]');
    
    if (!selectedColor) {
      // Nếu không chọn màu, hiển thị tất cả size
      sizeOptions.forEach(option => {
        option.style.display = 'inline-block';
        option.classList.remove('disabled');
      });
    } else {
      const selectedColorName = selectedColor.dataset.color;
      
      // Lọc các size có sẵn cho màu đã chọn
      const availableSizes = productVariants
        .filter(variant => variant.color_name === selectedColorName)
        .map(variant => variant.size_name);
      
      // Hiển thị/ẩn size options
      sizeOptions.forEach(option => {
        const sizeName = option.dataset.size;
        if (availableSizes.includes(sizeName)) {
          option.style.display = 'inline-block';
          option.classList.remove('disabled');
        } else {
          option.style.display = 'none';
          option.classList.add('disabled');
          option.classList.remove('active'); // Bỏ chọn nếu bị ẩn
        }
      });
    }
    
    // Kiểm tra và tự động chuyển sang size còn hàng
    autoSelectAvailableSize();
  }

  // Function tự động chọn size còn hàng
  function autoSelectAvailableSize() {
    const selectedColor = document.querySelector('[data-color].active');
    const selectedSize = document.querySelector('[data-size].active');
    const sizeOptions = document.querySelectorAll('[data-size]');
    
    // Nếu đã chọn size và size đó còn hàng, không cần thay đổi
    if (selectedSize && !selectedSize.classList.contains('disabled') && selectedSize.style.display !== 'none') {
      const currentStock = getCurrentVariantStock();
      if (currentStock > 0) {
        return; // Size hiện tại còn hàng
      }
    }
    
    // Tìm size còn hàng
    let availableSize = null;
    for (let option of sizeOptions) {
      if (option.style.display !== 'none' && !option.classList.contains('disabled')) {
        const sizeName = option.dataset.size;
        const colorName = selectedColor ? selectedColor.dataset.color : null;
        
        // Kiểm tra stock của size này
        const stock = getVariantStock(colorName, sizeName);
        if (stock > 0) {
          availableSize = option;
          break;
        }
      }
    }
    
    // Bỏ chọn size cũ và chọn size mới
    sizeOptions.forEach(opt => opt.classList.remove('active'));
    if (availableSize) {
      availableSize.classList.add('active');
    }
    
    // Cập nhật giá và stock
    updatePrice();
  }

  // Function lấy stock của variant hiện tại
  function getCurrentVariantStock() {
    const selectedColor = document.querySelector('[data-color].active');
    const selectedSize = document.querySelector('[data-size].active');
    
    if (!selectedColor && !selectedSize) {
      return getTotalStock();
    }
    
    const colorName = selectedColor ? selectedColor.dataset.color : null;
    const sizeName = selectedSize ? selectedSize.dataset.size : null;
    
    return getVariantStock(colorName, sizeName);
  }

  // Function lấy stock của variant cụ thể
  function getVariantStock(colorName, sizeName) {
    let matchingVariants = [...productVariants];
    
    if (colorName) {
      matchingVariants = matchingVariants.filter(v => v.color_name === colorName);
    }
    
    if (sizeName) {
      matchingVariants = matchingVariants.filter(v => v.size_name === sizeName);
    }
    
    if (matchingVariants.length > 0) {
      return parseInt(matchingVariants[0].stock) || 0;
    }
    
    return 0;
  }

  // Function lấy tổng stock
  function getTotalStock() {
    const mainProductStock = {{ $product->soLuong ?? 0 }};
    const variantStock = productVariants.reduce((sum, variant) => {
      const stock = parseInt(variant.stock) || 0;
      return sum + stock;
    }, 0);
    return mainProductStock + variantStock;
  }

  // Function tự động chọn variant còn hàng (màu + size)
  function autoSelectAvailableVariant() {
    const colorOptions = document.querySelectorAll('[data-color]');
    const sizeOptions = document.querySelectorAll('[data-size]');
    
    // Tìm variant còn hàng
    let availableColor = null;
    let availableSize = null;
    
    // Duyệt qua tất cả màu
    for (let colorOption of colorOptions) {
      const colorName = colorOption.dataset.color;
      
      // Duyệt qua tất cả size
      for (let sizeOption of sizeOptions) {
        const sizeName = sizeOption.dataset.size;
        
        // Kiểm tra stock của variant này
        const stock = getVariantStock(colorName, sizeName);
        if (stock > 0) {
          availableColor = colorOption;
          availableSize = sizeOption;
          break;
        }
      }
      
      if (availableColor && availableSize) break;
    }
    
    // Bỏ chọn tất cả
    colorOptions.forEach(opt => opt.classList.remove('active'));
    sizeOptions.forEach(opt => opt.classList.remove('active'));
    
    // Chọn variant còn hàng
    if (availableColor && availableSize) {
      availableColor.classList.add('active');
      availableSize.classList.add('active');
      
      // Cập nhật background màu
      const colorName = availableColor.dataset.color;
      const colorMap = {
        'Đỏ': '#dc3545', 'Xanh': '#007bff', 'Xanh lá': '#28a745',
        'Vàng': '#ffc107', 'Hồng': '#e83e8c', 'Tím': '#6f42c1',
        'Cam': '#fd7e14', 'Xám': '#6c757d', 'Đen': '#343a40', 'Trắng': '#ffffff'
      };
      const colorCode = colorMap[colorName] || '#6c757d';
      availableColor.style.backgroundColor = colorCode;
      availableColor.style.color = colorCode === '#ffffff' ? '#000000' : '#ffffff';
      
      // Cập nhật giá và stock
      updatePrice();
    } else {
      // Tất cả variant đều hết hàng
      showOutOfStockMessage();
    }
  }

  // Function hiển thị thông báo hết hàng
  function showOutOfStockMessage() {
    const stockInfo = document.getElementById('available-stock');
    
    // Cập nhật số lượng hiển thị
    if (stockInfo) {
      stockInfo.textContent = '0';
    }
    
    // Ẩn các nút hành động
    updateAddToCartButton(0);
    
    if (addToCartBtn) {
      addToCartBtn.disabled = true;
      addToCartBtn.innerHTML = '<i class="fas fa-times me-2"></i>HẾT HÀNG';
      addToCartBtn.classList.remove('btn-warning');
      addToCartBtn.classList.add('btn-secondary');
    }
    
    if (buyNowBtn) {
      buyNowBtn.disabled = true;
      buyNowBtn.innerHTML = '<i class="fas fa-times me-2"></i>HẾT HÀNG';
      buyNowBtn.classList.remove('btn-success');
      buyNowBtn.classList.add('btn-secondary');
    }
  }
  
  const colorOptions = document.querySelectorAll('[data-color]');
  colorOptions.forEach(option => {
    option.addEventListener('click', function() {
      // Nếu đã active thì bỏ chọn
      if (this.classList.contains('active')) {
        this.classList.remove('active');
        this.style.backgroundColor = '';
        this.style.color = '';
        updatePrice();
        updateSizeOptions(); // Cập nhật hiển thị size
        return;
      }
      
      // Bỏ chọn tất cả và chọn cái này
      colorOptions.forEach(opt => {
        opt.classList.remove('active');
        opt.style.backgroundColor = '';
        opt.style.color = '';
      });
      this.classList.add('active');
      
                // Cập nhật background màu theo tên màu
                const colorName = this.dataset.color;
                if (colorName) {
                  // Map tên màu thành mã màu
                  const colorMap = {
                    'Đỏ': '#dc3545',
                    'Xanh': '#007bff', 
                    'Xanh lá': '#28a745',
                    'Vàng': '#ffc107',
                    'Hồng': '#e83e8c',
                    'Tím': '#6f42c1',
                    'Cam': '#fd7e14',
                    'Xám': '#6c757d',
                    'Đen': '#343a40',
                    'Trắng': '#ffffff'
                  };
                  
                  const colorCode = colorMap[colorName] || '#6c757d';
                  this.style.backgroundColor = colorCode;
                  this.style.color = colorCode === '#ffffff' ? '#000000' : '#ffffff';
                }
      
      // Cập nhật hiển thị size theo màu đã chọn
      updateSizeOptions();
      
      // Cập nhật giá và số lượng theo màu đã chọn
      updatePrice();
    });
  });
  
  // Xử lý chọn size
  const sizeOptions = document.querySelectorAll('[data-size]');
  sizeOptions.forEach(option => {
    option.addEventListener('click', function() {
      // Không cho phép chọn size bị disabled
      if (this.classList.contains('disabled') || this.style.display === 'none') {
        return;
      }
      
      // Nếu đã active thì bỏ chọn
      if (this.classList.contains('active')) {
        this.classList.remove('active');
        updatePrice();
        return;
      }
      
      // Bỏ chọn tất cả và chọn cái này
      sizeOptions.forEach(opt => opt.classList.remove('active'));
      this.classList.add('active');
      
      // Cập nhật giá và số lượng theo size đã chọn
      updatePrice();
    });
  });
});

// Cập nhật giá theo variant đã chọn
function updatePrice() {
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  const priceDisplay = document.getElementById('product-price-display');
  
  console.log('=== updatePrice called ===');
  console.log('Selected color:', selectedColor ? selectedColor.dataset.color : 'none');
  console.log('Selected size:', selectedSize ? selectedSize.dataset.size : 'none');
  console.log('Total variants:', productVariants.length);
  
  // Tìm variant phù hợp
  let matchingVariants = [...productVariants]; // Tạo copy để không ảnh hưởng đến array gốc
  
  if (selectedColor) {
    const colorName = selectedColor.dataset.color;
    matchingVariants = matchingVariants.filter(v => v.color_name === colorName);
  }
  
  if (selectedSize) {
    const sizeName = selectedSize.dataset.size;
    matchingVariants = matchingVariants.filter(v => v.size_name === sizeName);
  }
  
  // Nếu không có màu được chọn → hiển thị giá chính (bất kể có size hay không)
  if (!selectedColor) {
    console.log('No color selected, showing main product price'); // Debug
    
    // Kiểm tra sản phẩm chính có còn hàng không
    const mainStock = {{ $product->soLuong ?? 0 }};
    if (mainStock <= 0) {
      console.log('Main product out of stock, showing message'); // Debug
      showMainProductOutOfStockMessage();
      return;
    }
    
    showDefaultPrice();
    updateStockDisplay(); // Sẽ hiển thị tổng số lượng
    return;
  }
  
  // Nếu không có variant phù hợp → hiển thị giá chính
  if (matchingVariants.length === 0) {
    console.log('No matching variant found, showing main product price'); // Debug
    
    // Kiểm tra sản phẩm chính có còn hàng không
    const mainStock = {{ $product->soLuong ?? 0 }};
    if (mainStock <= 0) {
      console.log('Main product out of stock, showing message'); // Debug
      showMainProductOutOfStockMessage();
      return;
    }
    
    showDefaultPrice();
    updateStockDisplay(); // Sẽ hiển thị tổng số lượng
    return;
  }
  
  if (matchingVariants.length > 0) {
    const variant = matchingVariants[0];
    const stock = parseInt(variant.stock) || 0;
    
    console.log('Found matching variant:', variant); // Debug
    
    // Kiểm tra nếu hết hàng
    if (stock <= 0) {
      // Tự động chuyển sang màu/size khác
      autoSelectAvailableVariant();
      return;
    }
    
    // Logic tính giá rõ ràng (giống CartService):
    // 1. Nếu variant có giá khuyến mãi > 0, dùng giá khuyến mãi
    // 2. Nếu variant có giá bán > 0, dùng giá bán
    // 3. Nếu variant không có giá, dùng giá sản phẩm chính
    let displayPrice = 0;
    if (variant.sale_price && variant.sale_price > 0) {
      displayPrice = variant.sale_price;
      console.log('Using sale price:', displayPrice); // Debug
    } else if (variant.price && variant.price > 0) {
      displayPrice = variant.price;
      console.log('Using variant price:', displayPrice); // Debug
    } else {
      // Fallback về giá sản phẩm chính
      displayPrice = {{ $product->base_price ?? 0 }};
      console.log('Using main product price:', displayPrice); // Debug
    }
    
    if (displayPrice > 0) {
      priceDisplay.innerHTML = `<div class="current-price">${formatPrice(displayPrice)} VNĐ</div>`;
    } else {
      priceDisplay.innerHTML = `<div class="contact-price">Liên hệ</div>`;
    }
    
    // Cập nhật hiển thị tồn kho cho variant cụ thể
    updateStockDisplay(stock);
  }
}

// Hiển thị giá mặc định (giá chính của sản phẩm)
function showDefaultPrice() {
  const priceDisplay = document.getElementById('product-price-display');
  
  console.log('Showing default price (main product price)'); // Debug
  
  // Hiển thị giá chính của sản phẩm
  const basePrice = {{ $product->base_price ?? 0 }};
  if (basePrice > 0) {
    priceDisplay.innerHTML = `<div class="current-price">${formatPrice(basePrice)} VNĐ</div>`;
    console.log('Main product price:', basePrice); // Debug
    } else {
    priceDisplay.innerHTML = `<div class="contact-price">Liên hệ</div>`;
    console.log('No main product price, showing contact'); // Debug
  }
}

// Hiển thị thông báo hết sản phẩm chính
function showMainProductOutOfStockMessage() {
  const priceDisplay = document.getElementById('product-price-display');
  const stockInfo = document.getElementById('available-stock');
  
  // Hiển thị thông báo thay vì giá
  priceDisplay.innerHTML = `
    <div class="alert alert-warning text-center" style="margin: 0; padding: 15px;">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <strong>Hết sản phẩm chính!</strong><br>
      <small>Vui lòng chọn màu sắc hoặc size khác để xem các biến thể có sẵn</small>
    </div>
  `;
  
  // Cập nhật thông tin tồn kho
  if (stockInfo) {
    stockInfo.textContent = '0';
  }
  
  // Disable các nút
  updateAddToCartButton(0);
  
  console.log('Main product out of stock message displayed'); // Debug
}

// Cập nhật hiển thị thống kê chi tiết (đã trừ giỏ hàng)
function updateDetailedStockDisplay(data) {
  // Tính toán số lượng đã trừ giỏ hàng cho từng loại
  const cartQuantity = data.cartQuantity || 0;
  const mainStockReal = data.mainStock || 0;
  const variantStockReal = data.variantStock || 0;
  const totalRealStock = mainStockReal + variantStockReal;
  
  // Tính số lượng còn lại cho sản phẩm chính và biến thể
  let mainStockDisplay = mainStockReal;
  let variantStockDisplay = variantStockReal;
  
  if (cartQuantity > 0) {
    // Tính tổng số lượng còn lại
    const remainingStock = Math.max(0, totalRealStock - cartQuantity);
    
    // Phân bổ số lượng còn lại cho sản phẩm chính và biến thể
    if (remainingStock <= mainStockReal) {
      // Nếu số lượng còn lại <= sản phẩm chính, tất cả thuộc về sản phẩm chính
      mainStockDisplay = remainingStock;
      variantStockDisplay = 0;
  } else {
      // Nếu số lượng còn lại > sản phẩm chính, sản phẩm chính = 0, phần còn lại thuộc biến thể
      mainStockDisplay = 0;
      variantStockDisplay = remainingStock - mainStockReal;
    }
  }
  
  // Cập nhật hiển thị
  document.getElementById('main-stock').textContent = mainStockDisplay;
  document.getElementById('variant-stock').textContent = variantStockDisplay;
  
  console.log('=== updateDetailedStockDisplay Debug ===');
  console.log('Input data:', data);
  console.log('mainStockReal:', mainStockReal);
  console.log('variantStockReal:', variantStockReal);
  console.log('cartQuantity:', cartQuantity);
  console.log('totalRealStock:', totalRealStock);
  console.log('mainStockDisplay:', mainStockDisplay);
  console.log('variantStockDisplay:', variantStockDisplay);
  console.log('remainingStock:', mainStockDisplay + variantStockDisplay);
  console.log('=== End Debug ===');
}

// Cập nhật hiển thị tồn kho
function updateStockDisplay(stock = null) {
  const stockInfo = document.getElementById('available-stock');
  const stockError = document.getElementById('stock-error');
  
  if (stock !== null && stock !== undefined) {
    // Hiển thị số lượng của variant cụ thể
    const stockValue = parseInt(stock) || 0;
    stockInfo.textContent = stockValue;
    
    // Cập nhật trạng thái nút "Thêm vào giỏ"
    updateAddToCartButton(stockValue);
    
    // Kiểm tra nếu hết hàng
    if (stockValue <= 0) {
      // Tự động chuyển sang variant khác
      autoSelectAvailableVariant();
      return;
    }
  } else {
    // Sử dụng API để lấy số lượng hiển thị
    console.log('=== updateStockDisplay: Calling API ===');
    fetch(`/api/cart/display-stock/{{ $product->id }}`, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })
    .then(response => {
      console.log('API Response status:', response.status); // Debug
      return response.json();
    })
    .then(data => {
      console.log('API Response data:', data); // Debug
      if (data.success) {
        // Hiển thị số lượng có sẵn (đã trừ giỏ hàng)
        document.getElementById('available-stock').textContent = data.totalStock;
        
        // Hiển thị chi tiết số lượng đã trừ giỏ hàng
        updateDetailedStockDisplay(data);
        
        // Cập nhật trạng thái nút "Thêm vào giỏ" dựa trên stock
        updateAddToCartButton(data.totalStock);
        
        // Kiểm tra nếu hết hàng
        if (data.totalStock <= 0) {
          // Hiển thị hết hàng
          showOutOfStockMessage();
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      // Fallback về tính toán cũ
      const mainProductStock = {{ $product->soLuong ?? 0 }};
      const variantStock = productVariants.reduce((sum, variant) => {
      const variantStock = parseInt(variant.stock) || 0;
      return sum + variantStock;
    }, 0);
      const totalStock = mainProductStock + variantStock;
      
      document.getElementById('available-stock').textContent = totalStock;
      document.getElementById('main-stock').textContent = mainProductStock;
      document.getElementById('variant-stock').textContent = variantStock;
      
      updateAddToCartButton(totalStock);
      
      if (totalStock <= 0) {
        showOutOfStockMessage();
        return;
      }
    });
  }
  
  // Hiển thị thông tin tồn kho
  stockInfo.parentElement.style.display = 'block';
  
  // Ẩn thông báo lỗi
  stockError.style.display = 'none';
}

// Cập nhật trạng thái nút "Thêm vào giỏ"
function updateAddToCartButton(stock) {
  const addToCartBtn = document.querySelector('.btn-add-cart');
  const buyNowBtn = document.querySelector('.btn-buy-now');
  
  if (stock <= 0) {
    // Hết hàng - ẩn nút
    addToCartBtn.style.display = 'none';
    buyNowBtn.style.display = 'none';
  } else {
    // Còn hàng - hiển thị nút
    addToCartBtn.style.display = 'block';
    addToCartBtn.disabled = false;
    addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ';
    addToCartBtn.classList.remove('btn-secondary');
    addToCartBtn.classList.add('btn-warning');
    
    buyNowBtn.style.display = 'block';
    buyNowBtn.disabled = false;
    buyNowBtn.innerHTML = '<i class="fas fa-bolt me-2"></i>Mua ngay';
    buyNowBtn.classList.remove('btn-secondary');
    buyNowBtn.classList.add('btn-success');
  }
}

// Kiểm tra tồn kho khi thay đổi số lượng
function checkStock() {
  const quantity = parseInt(document.getElementById('quantity').value);
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  const stockError = document.getElementById('stock-error');
  
  if (isNaN(quantity) || quantity < 0) {
    document.getElementById('quantity').value = 0;
    return;
  }
  
  // Tìm variant phù hợp
  let matchingVariants = productVariants;
  
  if (selectedColor) {
    const colorName = selectedColor.dataset.color;
    matchingVariants = matchingVariants.filter(v => v.color_name === colorName);
  }
  
  if (selectedSize) {
    const sizeName = selectedSize.dataset.size;
    matchingVariants = matchingVariants.filter(v => v.size_name === sizeName);
  }
  
  let availableStock = 0;
  if (matchingVariants.length > 0) {
    // Nếu có chọn màu/size, lấy số lượng của variant cụ thể
    availableStock = matchingVariants[0].stock || 0;
  } else {
    // Nếu không chọn màu/size, lấy tổng số lượng của tất cả variants
    availableStock = productVariants.reduce((sum, variant) => sum + (variant.stock || 0), 0);
  }
  
  // Cho phép số lượng bằng 0 nếu tồn kho = 0
  if (quantity > availableStock) {
    stockError.style.display = 'block';
    if (availableStock > 0) {
      document.getElementById('quantity').value = availableStock;
    } else {
      document.getElementById('quantity').value = 0;
    }
  } else {
    stockError.style.display = 'none';
  }
}

// Format giá tiền
function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price);
}

// Mua ngay
function buyNow() {
  const quantity = document.getElementById('quantity').value;
  
  // Kiểm tra số lượng
  if (!quantity || quantity <= 0) {
    showNotification('Vui lòng chọn số lượng hợp lệ!', 'error');
    return;
  }
  
  // Kiểm tra validation
  if (!validateSelection()) {
    return;
  }
  
  // Kiểm tra tồn kho
  if (!checkStockBeforeBuy()) {
    return;
  }
  
  // Chuyển đến trang thanh toán
  const productId = {{ $product->id }};
  const variantId = getSelectedVariantId();
  
  if (variantId) {
    window.location.href = `/checkout?product_id=${productId}&variant_id=${variantId}&quantity=${quantity}&action=buy_now`;
  } else {
    showNotification('Không thể xác định biến thể sản phẩm!', 'error');
  }
}

// Tăng số lượng
function increaseQuantity() {
  const quantityInput = document.getElementById('quantity');
  let currentValue = parseInt(quantityInput.value);
  quantityInput.value = currentValue + 1;
  checkStock();
}

// Giảm số lượng
function decreaseQuantity() {
  const quantityInput = document.getElementById('quantity');
  let currentValue = parseInt(quantityInput.value);
  if (currentValue > 0) {
    quantityInput.value = currentValue - 1;
    checkStock();
  }
}

// Function addToCart cũ đã được thay thế bằng addToCartFromDetail()

// Lấy ID variant được chọn
function getSelectedVariantId() {
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  // Nếu không chọn gì, trả về variant đầu tiên
  if (!selectedColor && !selectedSize) {
    return productVariants && productVariants.length > 0 ? productVariants[0].id : null;
  }
  
  // Tìm variant phù hợp
  let matchingVariants = productVariants;
  
  if (selectedColor) {
    const colorName = selectedColor.dataset.color;
    matchingVariants = matchingVariants.filter(v => v.color_name === colorName);
  }
  
  if (selectedSize) {
    const sizeName = selectedSize.dataset.size;
    matchingVariants = matchingVariants.filter(v => v.size_name === sizeName);
  }
  
  return matchingVariants.length > 0 ? matchingVariants[0].id : null;
}

// Kiểm tra tồn kho trước khi mua
function checkStockBeforeBuy() {
  const quantity = parseInt(document.getElementById('quantity').value);
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  // Tìm variant phù hợp
  let matchingVariants = productVariants;
  
  if (selectedColor) {
    const colorName = selectedColor.dataset.color;
    matchingVariants = matchingVariants.filter(v => v.color_name === colorName);
  }
  
  if (selectedSize) {
    const sizeName = selectedSize.dataset.size;
    matchingVariants = matchingVariants.filter(v => v.size_name === sizeName);
  }
  
  let availableStock = 0;
  if (matchingVariants.length > 0) {
    // Nếu có chọn màu/size, lấy số lượng của variant cụ thể
    availableStock = matchingVariants[0].stock || 0;
  } else {
    // Nếu không chọn màu/size, lấy tổng số lượng của tất cả variants
    availableStock = productVariants.reduce((sum, variant) => sum + (variant.stock || 0), 0);
  }
  
  if (quantity > availableStock) {
    showNotification(`Số lượng vượt quá tồn kho! Chỉ còn ${availableStock} sản phẩm.`, 'error');
    return false;
  }
  
  return true;
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
  // Tạo thông báo toast đơn giản
  const toast = document.createElement('div');
  toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
  toast.style.cssText = 'top: 100px; right: 20px; z-index: 99999; min-width: 300px; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
  toast.innerHTML = `
    <div class="d-flex align-items-center">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
      <span>${message}</span>
      <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
    </div>
  `;
  
  document.body.appendChild(toast);
  
  // Tự động xóa sau 4 giây
  setTimeout(() => {
    if (toast.parentElement) {
      toast.remove();
    }
  }, 4000);
}

// Kiểm tra validation trước khi thêm vào giỏ
function validateSelection() {
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  // Nếu có cả màu và size, phải chọn cả hai
  if (selectedColor && !selectedSize) {
    showNotification('Vui lòng chọn size!', 'error');
    return false;
  }
  
  // Nếu có size nhưng chưa chọn màu, vẫn cho phép (sẽ lấy variant đầu tiên)
  if (selectedSize && !selectedColor) {
    return true;
  }
  
  // Nếu không có cả màu và size, kiểm tra xem có variant nào không
  if (!selectedColor && !selectedSize) {
    if (productVariants && productVariants.length > 0) {
      // Nếu có variant, cho phép mua (sẽ lấy variant đầu tiên)
      return true;
    } else {
      showNotification('Sản phẩm không có biến thể!', 'error');
      return false;
    }
  }
  
  return true;
}

// Tự động chuyển sang variant có sẵn
function autoSelectAvailableVariant() {
  if (!productVariants || productVariants.length === 0) {
    return false;
  }
  
  // Tìm variant có stock > 0
  const availableVariants = productVariants.filter(v => v.stock > 0);
  
  if (availableVariants.length === 0) {
    return false; // Không có variant nào có sẵn
  }
  
  // Chọn variant đầu tiên có sẵn
  const selectedVariant = availableVariants[0];
  
  // Cập nhật UI
  updateVariantSelection(selectedVariant);
  
  return true;
}

// Cập nhật UI khi chọn variant
function updateVariantSelection(variant) {
  // Cập nhật màu sắc
  if (variant.color_name) {
    const colorButtons = document.querySelectorAll('[data-color]');
    colorButtons.forEach(btn => {
      btn.classList.remove('active');
      if (btn.dataset.color === variant.color_name) {
        btn.classList.add('active');
      }
    });
  }
  
  // Cập nhật size
  if (variant.size_name) {
    const sizeButtons = document.querySelectorAll('[data-size]');
    sizeButtons.forEach(btn => {
      btn.classList.remove('active');
      if (btn.dataset.size === variant.size_name) {
        btn.classList.add('active');
      }
    });
  }
  
  // Cập nhật giá
  updatePrice();
  
  // Hiển thị thông báo
  showNotification(`Đã chuyển sang ${variant.color_name || 'màu'} ${variant.size_name || 'size'} có sẵn`, 'info');
}

// Thêm vào giỏ hàng từ trang chi tiết
function addToCartFromDetail() {
  // Kiểm tra validation trước
  if (!validateSelection()) {
    return;
  }
  
  const productId = {{ $product->id }};
  const variantId = getSelectedVariantId();
  const quantity = parseInt(document.getElementById('quantity').value) || 1;
  
  console.log('Adding to cart:', {productId, variantId, quantity}); // Debug
  
  // Kiểm tra CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]');
  console.log('CSRF Token:', csrfToken ? csrfToken.getAttribute('content') : 'Not found');
  
  // Sử dụng function addToCart global từ layout và xử lý response
  console.log('Starting addToCart process...'); // Debug
  
  addToCart(productId, variantId, quantity)
    .then(response => {
      console.log('Add to cart response:', response); // Debug
      console.log('Response type:', typeof response); // Debug
      console.log('Response success:', response?.success); // Debug
      
      if (response && response.success) {
        console.log('Adding to cart successful, updating stock display...'); // Debug
        
        // Cập nhật hiển thị stock sau khi thêm vào giỏ
        updateStockDisplay();
        
        // Hiển thị thông báo thành công
        showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
        
        console.log('Stock display updated and notification shown'); // Debug
      } else {
        console.error('Add to cart failed:', response); // Debug
        showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
      }
    })
    .catch(error => {
      console.error('Error adding to cart:', error); // Debug
      console.error('Error details:', error.message); // Debug
      console.error('Error stack:', error.stack); // Debug
      // Hiển thị thông báo lỗi
      showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
    });
}
</script>
@endsection


