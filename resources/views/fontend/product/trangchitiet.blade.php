@extends('fontend.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
  <style>
    /* CSS cho disabled size options */
    [data-size].disabled {
      opacity: 0.5 !important;
      cursor: not-allowed !important;
      pointer-events: none !important;
      background-color: #f5f5f5 !important;
      color: #999 !important;
      border-color: #ddd !important;
    }

    [data-size].disabled:hover {
      background-color: #f5f5f5 !important;
      color: #999 !important;
      border-color: #ddd !important;
    }
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
      border: 3px solid transparent;
      transition: all 0.3s ease;
    }
    
    .thumbnail:hover {
      border-color: #007bff;
      transform: scale(1.05);
    }
    
    .thumbnail.active {
      border-color: #28a745;
      box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
    }
    
    .product-info-section {
      padding: 40px;
    }
    
    .product-title {
      font-size: 2.2rem;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 15px;
      line-height: 1.3;
    }
    
    .product-price {
      font-size: 2.5rem;
      font-weight: 800;
      color: #28a745;
      margin-bottom: 25px;
    }
    
    .product-description {
      font-size: 1.1rem;
      color: #6c757d;
      line-height: 1.6;
      margin-bottom: 30px;
    }
    
    .variant-section {
      margin-bottom: 25px;
    }
    
    .variant-group {
      margin-bottom: 20px;
    }
    
    .variant-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 10px;
      display: block;
    }
    
    .variant-options {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .variant-option {
      padding: 10px 20px;
      border: 2px solid #dee2e6;
      border-radius: 25px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
      background: #fff;
    }
    
    .variant-option:hover {
      border-color: #007bff;
      background: #f8f9fa;
    }
    
    .variant-option.active {
      border-color: #28a745;
      background: #28a745;
      color: #fff;
    }
    
    .quantity-section {
      margin-bottom: 30px;
    }
    
    .quantity-controls {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 15px;
    }
    
    .quantity-input {
      width: 80px;
      text-align: center;
      border: 2px solid #dee2e6;
      border-radius: 8px;
      padding: 10px;
      font-size: 1.1rem;
      font-weight: 600;
    }
    
    .quantity-controls button {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 2px solid #dee2e6;
      background: #fff;
      font-size: 1.2rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .quantity-controls button:hover {
      border-color: #007bff;
      background: #007bff;
      color: #fff;
    }
    
    .action-buttons {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
    }
    
    .btn-add-cart, .btn-buy-now {
      flex: 1;
      padding: 15px 25px;
      border-radius: 25px;
      font-size: 1.1rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }
    
    .btn-add-cart {
      background: linear-gradient(135deg, #ffc107, #ff8c00);
      color: #fff;
    }
    
    .btn-add-cart:hover {
      background: linear-gradient(135deg, #ff8c00, #ff6b00);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
    }
    
    .btn-buy-now {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: #fff;
    }
    
    .btn-buy-now:hover {
      background: linear-gradient(135deg, #20c997, #17a2b8);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    }
    
    .product-features {
      border-top: 1px solid #dee2e6;
      padding-top: 25px;
    }
    
    .feature-item {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      color: #6c757d;
    }
    
    .feature-item i {
      margin-right: 10px;
      color: #28a745;
      font-size: 1.2rem;
    }
    
    @media (max-width: 768px) {
      .product-detail-container {
        margin: 10px;
        border-radius: 10px;
      }
      
      .product-image-section,
      .product-info-section {
        padding: 20px;
      }
      
      .product-title {
        font-size: 1.8rem;
      }
      
      .product-price {
        font-size: 2rem;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .btn-add-cart, .btn-buy-now {
        width: 100%;
      }
    }
  </style>
@endsection

@section('content')
<div class="container py-5">
  <div class="row">
    <div class="col-12">
      <div class="product-detail-container">
        <div class="row g-0">
          <!-- Product Images -->
          <div class="col-lg-6">
            <div class="product-image-section">
              <img id="main-product-image" src="/fontend/img/aosomi1.png" alt="Product Image" class="main-product-image">
              
              <div id="thumbnail-gallery" class="thumbnail-gallery">
                <!-- Thumbnails will be loaded by JavaScript API -->
              </div>
            </div>
          </div>
          
          <!-- Product Info -->
          <div class="col-lg-6">
            <div class="product-info-section">
              <h1 id="product-title" class="product-title">Đang tải...</h1>
              <div id="product-price" class="product-price">Đang tải...</div>
              <p id="product-description" class="product-description">Đang tải...</p>
              
              <!-- Color Options -->
              <div class="variant-section mb-4">
                <div class="variant-group">
                  <label class="variant-label">Màu sắc:</label>
                  <div class="variant-options">
                    <!-- Colors will be loaded by JavaScript API -->
                  </div>
                </div>
              </div>
              
              <!-- Size Options -->
              <div class="variant-section mb-4">
                <div class="variant-group">
                  <label class="variant-label">Size:</label>
                  <div class="variant-options">
                    <!-- Sizes will be loaded by JavaScript API -->
                  </div>
                </div>
              </div>
              
              <!-- Quantity -->
              <div class="quantity-section mb-4">
                <label class="variant-label">Số lượng:</label>
                <div class="quantity-controls d-flex align-items-center">
                  <button class="btn btn-outline-secondary" onclick="productDetailManager.updateQuantity(-1)">-</button>
                  <input type="number" class="quantity-input" min="1" value="1" id="quantity">
                  <button class="btn btn-outline-secondary" onclick="productDetailManager.updateQuantity(1)">+</button>
                </div>
                <div id="stock-info" class="mt-2">
                  <small class="text-muted">
                    <strong>Số lượng có sẵn: <span id="available-stock">0</span></strong>
                    <br>
                    <span class="text-info">• Sản phẩm chính hiện có: <span id="main-stock">{{ $product->soLuong ?? 0 }}</span> sản phẩm</span>
                    <br>
                    <span class="text-success">• Biến thể hiện có: <span id="variant-stock">0</span> sản phẩm</span>
                    <br>
                    <span class="text-warning">• Tổng số lượng: <span id="total-stock">0</span> sản phẩm</span>
                  </small>
                </div>
                <div id="stock-error" class="mt-2" style="display: none;">
                  <small class="text-danger">Số lượng vượt quá tồn kho!</small>
                </div>
              </div>
              
              <!-- Action Buttons -->
              <div class="action-buttons">
                <button class="btn-add-cart" onclick="productDetailManager.addToCart()">
                  <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
                </button>
                <button class="btn-buy-now" onclick="productDetailManager.buyNow()">
                  <i class="fas fa-bolt me-2"></i>Mua ngay
                </button>
              </div>
              
              <!-- Product Features -->
              <div class="product-features">
                <div class="feature-item">
                  <i class="fas fa-truck"></i>
                  <span>Miễn phí vận chuyển</span>
                </div>
                <div class="feature-item">
                  <i class="fas fa-undo"></i>
                  <span>Đổi trả 30 ngày</span>
                </div>
                <div class="feature-item">
                  <i class="fas fa-shield-alt"></i>
                  <span>Bảo hành chính hãng</span>
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
<!-- Direct API Client -->
<script src="{{ asset('fontend/js/api/product-detail-api-client.js') }}"></script>
<!-- Product Detail Manager -->
<script src="{{ asset('fontend/js/product/product-detail-api.js') }}"></script>

<script>
// Simple initialization without complex API system
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, initializing product detail...');
  
  // Prevent duplicate initialization
  if (window.productDetailManager) {
    console.log('ProductDetailManager already initialized, skipping HTML init...');
    return;
  }
  
  // Get product ID from URL
  const pathParts = window.location.pathname.split('/');
  const productId = pathParts[pathParts.length - 1];
  console.log('Product ID from URL:', productId);
  
  // Validate product ID
  if (!productId || isNaN(productId)) {
    console.error('Invalid product ID:', productId);
    
    // Show error message
    const titleElement = document.getElementById('product-title');
    const priceElement = document.getElementById('product-price');
    const descElement = document.getElementById('product-description');
    
    if (titleElement) titleElement.textContent = 'Lỗi: ID sản phẩm không hợp lệ';
    if (priceElement) priceElement.textContent = 'Không thể tải giá';
    if (descElement) descElement.textContent = 'Vui lòng kiểm tra URL và thử lại.';
    return;
  }
  
  // Initialize product detail manager directly
  if (typeof productDetailManager !== 'undefined') {
    console.log('ProductDetailManager available, initializing...');
    productDetailManager.init(productId);
  } else {
    console.error('ProductDetailManager not available');
    
    // Fallback: set global productId for buyNow function
    window.productId = productId;
    console.log('Set global productId:', productId);
    
    // Show error message
    const titleElement = document.getElementById('product-title');
    const priceElement = document.getElementById('product-price');
    const descElement = document.getElementById('product-description');
    
    if (titleElement) titleElement.textContent = 'Lỗi tải dữ liệu';
    if (priceElement) priceElement.textContent = 'Không thể tải giá';
    if (descElement) descElement.textContent = 'Vui lòng tải lại trang hoặc thử lại sau.';
  }
});
</script>
@endsection