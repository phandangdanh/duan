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
    
    .variant-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 10px;
      display: block;
    }
    
    .color-btn, .size-btn {
      margin-right: 10px;
      margin-bottom: 10px;
      border-radius: 25px;
      padding: 8px 16px;
      transition: all 0.3s ease;
    }
    
    .color-btn.active, .size-btn.active {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
    }
    
    .size-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
      pointer-events: none;
    }
    
    .quantity-section {
      margin-bottom: 25px;
    }
    
    .quantity-controls {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .quantity-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 2px solid #007bff;
      background: white;
      color: #007bff;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    
    .quantity-btn:hover {
      background: #007bff;
      color: white;
    }
    
    .quantity-input {
      width: 80px;
      text-align: center;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      padding: 8px;
      font-weight: 600;
    }
    
    .action-buttons {
      margin-bottom: 30px;
    }
    
    .btn-action {
      width: 100%;
      padding: 15px;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 10px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }
    
    .btn-add-cart {
      background: linear-gradient(45deg, #ff6b35, #f7931e);
      border: none;
      color: white;
    }
    
    .btn-add-cart:hover {
      background: linear-gradient(45deg, #e55a2b, #e0841a);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
    }
    
    .btn-buy-now {
      background: linear-gradient(45deg, #28a745, #20c997);
      border: none;
      color: white;
    }
    
    .btn-buy-now:hover {
      background: linear-gradient(45deg, #218838, #1ea085);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }
    
    .service-guarantees {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
      margin-top: 20px;
    }
    
    .guarantee-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }
    
    .guarantee-item:last-child {
      margin-bottom: 0;
    }
    
    .guarantee-icon {
      width: 24px;
      height: 24px;
      margin-right: 10px;
      color: #28a745;
    }
    
    .related-products-section {
      margin-top: 50px;
      padding: 30px 0;
      background: #f8f9fa;
    }
    
    .section-title {
      font-size: 2rem;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 30px;
      text-align: center;
    }
    
    .hover-lift {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.9);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    
    .loading-spinner {
      text-align: center;
    }
    
    .loading-spinner .spinner-border {
      width: 3rem;
      height: 3rem;
    }
  </style>
@endsection

@section('content')
<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
  <div class="loading-spinner">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Đang tải...</span>
    </div>
    <p class="mt-3 text-muted">Đang tải thông tin sản phẩm...</p>
  </div>
</div>

<div class="container my-5">
  <div class="product-detail-container">
    <div class="row g-0">
      <!-- Product Images -->
      <div class="col-lg-6">
        <div class="product-image-section">
          <img id="main-product-image" 
               src="/fontend/img/aosomi1.png" 
               alt="Product Image" 
               class="main-product-image">
          
          <div id="thumbnail-gallery" class="thumbnail-gallery">
            <!-- Thumbnails will be loaded here -->
          </div>
        </div>
      </div>
      
      <!-- Product Info -->
      <div class="col-lg-6">
        <div class="product-info-section">
          <h1 id="product-title" class="product-title">Đang tải...</h1>
          <div id="product-price" class="product-price">0 VNĐ</div>
          <p id="product-description" class="product-description">Đang tải mô tả...</p>
          
          <!-- Color Selection -->
          <div class="variant-section">
            <label class="variant-label">Màu sắc:</label>
            <div id="color-options">
              <!-- Color options will be loaded here -->
            </div>
          </div>
          
          <!-- Size Selection -->
          <div class="variant-section">
            <label class="variant-label">Kích thước:</label>
            <div id="size-options">
              <!-- Size options will be loaded here -->
            </div>
          </div>
          
          <!-- Quantity Selection -->
          <div class="quantity-section">
            <label class="variant-label">Số lượng:</label>
            <div class="quantity-controls">
              <button type="button" id="quantity-minus" class="quantity-btn">-</button>
              <input type="number" id="quantity-input" class="quantity-input" value="1" min="1">
              <button type="button" id="quantity-plus" class="quantity-btn">+</button>
            </div>
          </div>
          
          <!-- Stock Info -->
          <div id="stock-info" class="mb-4">
            <!-- Stock info will be loaded here -->
          </div>
          
          <!-- Action Buttons -->
          <div class="action-buttons">
            <button type="button" id="add-to-cart-btn" class="btn btn-action btn-add-cart">
              <i class="fas fa-shopping-cart me-2"></i>THÊM VÀO GIỎ
            </button>
            <button type="button" id="buy-now-btn" class="btn btn-action btn-buy-now">
              <i class="fas fa-bolt me-2"></i>MUA NGAY
            </button>
          </div>
          
          <!-- Service Guarantees -->
          <div class="service-guarantees">
            <div class="guarantee-item">
              <i class="fas fa-truck guarantee-icon"></i>
              <span>Miễn phí vận chuyển</span>
            </div>
            <div class="guarantee-item">
              <i class="fas fa-undo guarantee-icon"></i>
              <span>Đổi trả 30 ngày</span>
            </div>
            <div class="guarantee-item">
              <i class="fas fa-shield-alt guarantee-icon"></i>
              <span>Bảo hành chính hãng</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Related Products -->
<section class="related-products-section">
  <div class="container">
    <h2 class="section-title">Sản phẩm liên quan</h2>
    <div class="row" id="related-products-container">
      <!-- Related products will be loaded here -->
    </div>
  </div>
</section>
@endsection

@section('js')
<!-- API System -->
<script src="{{ asset('fontend/js/api/index.js') }}"></script>
<!-- Product Detail API Client -->
<script src="{{ asset('fontend/js/api/product-detail-api-client.js') }}"></script>
<!-- Product Detail Manager -->
<script src="{{ asset('fontend/js/product/product-detail-api.js') }}"></script>

<script>
// Initialize product detail page
document.addEventListener('apiReady', function(event) {
  console.log('API is ready, initializing product detail...');
  
  // Get product ID from URL
  const pathParts = window.location.pathname.split('/');
  const productId = pathParts[pathParts.length - 1];
  
  if (productId && !isNaN(productId)) {
    // Initialize product detail manager
    productDetailManager.init(parseInt(productId));
  } else {
    // Show error if no valid product ID
    document.getElementById('product-title').textContent = 'Sản phẩm không tồn tại';
    document.getElementById('product-description').textContent = 'Không thể tìm thấy sản phẩm với ID này.';
  }
});

// Fallback if API is not ready
document.addEventListener('DOMContentLoaded', function() {
  // Wait a bit for API to initialize
  setTimeout(() => {
    if (typeof productDetailManager !== 'undefined') {
      console.log('ProductDetailManager available, initializing...');
      
      // Get product ID from URL
      const pathParts = window.location.pathname.split('/');
      const productId = pathParts[pathParts.length - 1];
      
      if (productId && !isNaN(productId)) {
        productDetailManager.init(parseInt(productId));
      } else {
        document.getElementById('product-title').textContent = 'Sản phẩm không tồn tại';
        document.getElementById('product-description').textContent = 'Không thể tìm thấy sản phẩm với ID này.';
      }
    } else {
      console.warn('API not ready, using fallback...');
      // Show error message
      document.getElementById('product-title').textContent = 'Không thể tải dữ liệu';
      document.getElementById('product-description').textContent = 'Vui lòng tải lại trang hoặc thử lại sau.';
    }
  }, 1000);
});
</script>
@endsection
