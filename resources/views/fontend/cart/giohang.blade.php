@extends('fontend.layouts.app')

@section('title', 'Giỏ hàng')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
  <style>
    .cart-item-image {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }
    .quantity-input {
      width: 80px;
    }
    .cart-empty {
      text-align: center;
      padding: 60px 20px;
    }
    .cart-empty i {
      font-size: 4rem;
      color: #ccc;
      margin-bottom: 20px;
    }
    
    /* CSS cho modal xóa đẹp */
    .delete-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    
    .delete-modal.show {
      display: flex;
    }
    
    .delete-modal-content {
      background: white;
      border-radius: 15px;
      padding: 30px;
      max-width: 400px;
      width: 90%;
      text-align: center;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
      transform: scale(0.8);
      transition: all 0.3s ease;
    }
    
    .delete-modal.show .delete-modal-content {
      transform: scale(1);
    }
    
    .delete-modal-icon {
      font-size: 60px;
      color: #ff6b6b;
      margin-bottom: 20px;
    }
    
    .delete-modal-title {
      font-size: 24px;
      font-weight: 600;
      color: #333;
      margin-bottom: 15px;
    }
    
    .delete-modal-message {
      font-size: 16px;
      color: #666;
      margin-bottom: 30px;
      line-height: 1.5;
    }
    
    .delete-modal-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
    }
    
    .btn-confirm-delete {
      background: linear-gradient(135deg, #ff6b6b, #ee5a52);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px 25px;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .btn-confirm-delete:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    }
    
    .btn-cancel-delete {
      background: #6c757d;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px 25px;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .btn-cancel-delete:hover {
      background: #5a6268;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
    }
    
    /* CSS cho nút xóa tất cả */
    .btn-clear-all {
      background: linear-gradient(135deg, #dc3545, #c82333);
      border: none;
      color: white;
      border-radius: 8px;
      padding: 12px 20px;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }
    
    .btn-clear-all:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }
  </style>
@endsection

@section('content')
<!-- navbar moved to layout -->

<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Giỏ hàng của bạn</h2>
    </div>

    <!-- Cart will be loaded dynamically from localStorage -->
    <div id="cart-empty" class="cart-empty" style="display: none;">
        <i class="fas fa-shopping-cart"></i>
        <h4>Giỏ hàng trống</h4>
        <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
        <a href="{{ route('products') }}" class="btn btn-primary">
          <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
        </a>
    </div>

    <!-- Load cart from localStorage only -->
    <script>
    // Load cart from localStorage only (no session sync)
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🔄 Loading cart from localStorage only...');
      
      // Fix zero prices in localStorage first
      fixZeroPricesInLocalStorage();
      
      // Update UI
      updateCartUI();
    });
    </script>

    <div id="cart-content" style="display: none;">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
            <tr>
              <th>Sản phẩm</th>
                <th>Loại</th>
              <th>Giá</th>
              <th>Số lượng</th>
              <th>Tạm tính</th>
              <th>Thao tác</th>
            </tr>
        </thead>
          <tbody id="cart-items">
              <!-- Cart items will be loaded dynamically -->
        </tbody>
      </table>
    </div>

      <div class="row mt-4">
        <div class="col-md-8">
          <a href="{{ route('products') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
          </a>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Tổng cộng</h5>
              <div class="d-flex justify-content-between">
                <span>Tạm tính:</span>
                <span id="cart-total">0₫</span>
              </div>
              <hr>
              <div class="d-flex justify-content-between">
                <strong>Tổng cộng:</strong>
                <strong class="text-danger" id="cart-final-total">0₫</strong>
              </div>
              <a href="{{ route('checkout') }}" class="btn btn-success w-100 mt-3">
                <i class="fas fa-credit-card me-2"></i>Thanh toán
              </a>
            </div>
          </div>
    </div>
    </div>
    </div>
  </div>
</section>

<!-- Modal xóa đẹp -->
<div id="deleteModal" class="delete-modal">
  <div class="delete-modal-content">
    <div class="delete-modal-icon">
      <i class="fas fa-exclamation-triangle"></i>
      </div>
    <div class="delete-modal-title">Xác nhận xóa</div>
    <div class="delete-modal-message" id="deleteModalMessage">
      Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?
      </div>
    <div class="delete-modal-buttons">
      <button class="btn-confirm-delete" id="confirmDeleteBtn">
        <i class="fas fa-trash me-2"></i>Xóa
      </button>
      <button class="btn-cancel-delete" onclick="closeDeleteModal()">
        <i class="fas fa-times me-2"></i>Hủy
      </button>
      </div>
      </div>
    </div>

@endsection

@section('js')
<!-- Cart API JavaScript -->
<script src="{{ asset('fontend/js/cart/cart-api.js') }}"></script>

<script>
// Function để tăng số lượng sản phẩm
function increaseQuantity(productId, variantId) {
  console.log('increaseQuantity called:', {productId, variantId});
  
  try {
    // Get current cart data
    const cartData = localStorage.getItem('cart_data');
    let cartItems = [];
    
    if (cartData) {
      try {
        cartItems = JSON.parse(cartData);
      } catch (error) {
        console.error('Error parsing cart data:', error);
      }
    }
    
    // Find item
    const itemIndex = cartItems.findIndex(item => 
      item.product_id === productId && item.variant_id === variantId
    );
    
    console.log(`🔍 Finding item: productId=${productId}, variantId=${variantId}`);
    console.log(`🔍 Cart items:`, cartItems);
    console.log(`🔍 Found item index:`, itemIndex);
    
    if (itemIndex !== -1) {
      const currentQuantity = cartItems[itemIndex].quantity;
      const newQuantity = currentQuantity + 1;
      
      console.log(`🔍 Current item:`, cartItems[itemIndex]);
      console.log(`🔍 Current quantity: ${currentQuantity}, New quantity: ${newQuantity}`);
      
      // Check stock availability
      const stockCheck = checkStockAvailability(productId, variantId, newQuantity);
      if (!stockCheck.available) {
        // Chỉ hiển thị notification khi có message
        if (stockCheck.message) {
          showNotification(stockCheck.message, 'error');
        }
        return;
      }
      
      // Update quantity
      cartItems[itemIndex].quantity = newQuantity;
      localStorage.setItem('cart_data', JSON.stringify(cartItems));
      
      console.log('🔍 Updated cart item:', cartItems[itemIndex]);
      console.log('🔍 Updated cart data:', JSON.stringify(cartItems));
      
      // Không reduce stock khi tăng quantity trong cart
      // Stock chỉ được reduce khi thêm từ product detail page
      
      // Update UI
      updateCartUI();
      updateQuantityDisplay(productId, variantId, newQuantity);
      
      showNotification('Đã tăng số lượng', 'success');
    } else {
      showNotification('Sản phẩm không tồn tại trong giỏ hàng', 'error');
    }
    
  } catch (error) {
    console.error('Error increasing quantity:', error);
    showNotification('Có lỗi xảy ra khi tăng số lượng', 'error');
  }
}

// Function để giảm số lượng sản phẩm
function decreaseQuantity(productId, variantId) {
  console.log('decreaseQuantity called:', {productId, variantId});
  
  try {
    // Get current cart data
    const cartData = localStorage.getItem('cart_data');
    let cartItems = [];
    
    if (cartData) {
      try {
        cartItems = JSON.parse(cartData);
      } catch (error) {
        console.error('Error parsing cart data:', error);
      }
    }
    
    // Find item
    const itemIndex = cartItems.findIndex(item => 
      item.product_id === productId && item.variant_id === variantId
    );
    
    if (itemIndex !== -1) {
      const currentQuantity = cartItems[itemIndex].quantity;
      
      if (currentQuantity <= 1) {
        // Remove item if quantity is 1
        cartItems.splice(itemIndex, 1);
        localStorage.setItem('cart_data', JSON.stringify(cartItems));
        
        // Restore stock when removing item completely
        restoreStockInLocalStorage(productId, variantId, 1);
        
        updateCartUI();
        showNotification('Sản phẩm đã được xóa khỏi giỏ hàng', 'info');
    return;
  }
  
      const newQuantity = currentQuantity - 1;
      
      // Update quantity
      cartItems[itemIndex].quantity = newQuantity;
      localStorage.setItem('cart_data', JSON.stringify(cartItems));
      
      // Không restore stock khi chỉ giảm quantity trong cart
      // Stock chỉ được restore khi xóa hoàn toàn sản phẩm
      
      // Update UI
      updateCartUI();
      updateQuantityDisplay(productId, variantId, newQuantity);
      
      showNotification('Đã giảm số lượng', 'success');
    } else {
      showNotification('Sản phẩm không tồn tại trong giỏ hàng', 'error');
    }
    
  } catch (error) {
    console.error('Error decreasing quantity:', error);
    showNotification('Có lỗi xảy ra khi giảm số lượng', 'error');
  }
}

// Function để kiểm tra stock availability
function checkStockAvailability(productId, variantId, requestedQuantity) {
  const storageKey = `product_stock_${productId}`;
  const stored = localStorage.getItem(storageKey);
  
  console.log(`🔍 checkStockAvailability: productId=${productId}, variantId=${variantId}, requested=${requestedQuantity}`);
  console.log(`🔍 Storage key: ${storageKey}`);
  console.log(`🔍 Stored data:`, stored);
  
  if (!stored) {
    console.log('No stock data found, allowing quantity increase');
    return { available: true, message: '' }; // No stock data, allow update
  }
  
  const stockData = JSON.parse(stored);
  console.log(`🔍 Parsed stock data:`, stockData);
  
  let availableStock = 0;
  let stockType = '';
  
  if (variantId && variantId !== 0) {
    // Check variant stock
    const variantKey = `variant_${variantId}`;
    availableStock = stockData.variants?.[variantKey] || 0;
    stockType = 'variant';
    console.log(`🔍 Checking VARIANT stock: ${variantKey} = ${availableStock}`);
    console.log(`🔍 All variants available:`, stockData.variants);
  } else {
    // Check main product stock
    availableStock = stockData.mainStock || 0;
    stockType = 'main';
    console.log(`🔍 Checking MAIN PRODUCT stock: ${availableStock}`);
    console.log(`🔍 Full stock data for main product:`, stockData);
  }
  
  console.log(`🔍 Final stock check: type=${stockType}, available=${availableStock}, requested=${requestedQuantity}`);
  
  console.log(`🔍 Stock check: requested=${requestedQuantity}, available=${availableStock}`);
  
  // Check if completely out of stock
  if (availableStock <= 0) {
    const itemType = variantId && variantId !== 0 ? 'biến thể' : 'sản phẩm chính';
    const message = `Sản phẩm đã hết hàng! ${itemType} không còn sản phẩm nào.`;
    console.log(`🔍 Stock check failed: ${message}`);
    
    // Hiển thị thông báo đặc biệt cho sản phẩm chính hết hàng
    if (!variantId || variantId === 0) {
      showNotification('⚠️ Sản phẩm chính đã hết hàng! Vui lòng chọn màu sắc và size khác.', 'warning');
    }
    
    return {
      available: false,
      message: message
    };
  }
  
  // Kiểm tra sản phẩm chính
  if (!variantId || variantId === 0) {
    if (availableStock === 0) {
      console.log(`🔍 Sản phẩm chính hết hàng hoàn toàn (0), từ chối`);
      showNotification('⚠️ Sản phẩm chính đã hết hàng! Vui lòng chọn màu sắc và size khác.', 'warning');
      return {
        available: false,
        message: 'Sản phẩm chính đã hết hàng!'
      };
    }
    
    // Kiểm tra xem có vượt quá số lượng có sẵn không
    if (requestedQuantity > availableStock) {
      console.log(`🔍 Sản phẩm chính còn ${availableStock} sản phẩm, từ chối vượt quá`);
      return {
        available: false,
        message: '' // Không có message để không hiển thị banner
      };
    }
    
    // Cho phép thêm khi không vượt quá
    console.log(`🔍 Sản phẩm chính còn ${availableStock} sản phẩm, cho phép thêm`);
    return { available: true, message: '' };
  }
  
  // Đối với biến thể, vẫn kiểm tra như cũ
  if (requestedQuantity > availableStock) {
    const itemType = variantId && variantId !== 0 ? 'biến thể' : 'sản phẩm chính';
    console.log(`🔍 Stock check failed: ${itemType} chỉ còn ${availableStock} sản phẩm`);
    
    return {
      available: false,
      message: '' // Không có message để không hiển thị banner
    };
  }
  
  // Cho phép thêm khi số lượng hiện tại bằng số lượng có sẵn
  if (requestedQuantity === availableStock) {
    console.log(`🔍 Số lượng hiện tại (${requestedQuantity}) bằng số lượng có sẵn (${availableStock}), cho phép thêm`);
    return { available: true, message: '' };
  }
  
  console.log('🔍 Stock check passed');
  return { available: true, message: '' };
}

// Function để cập nhật hiển thị quantity
function updateQuantityDisplay(productId, variantId, quantity) {
  const row = document.querySelector(`tr[data-product-id="${productId}"][data-variant-id="${variantId}"]`);
  if (!row) return;
  
  const quantityInput = row.querySelector('input[type="number"]');
  if (quantityInput) {
    quantityInput.value = quantity;
  }
  
  // Update button states
  const minusBtn = row.querySelector('.quantity-minus');
  const plusBtn = row.querySelector('.quantity-plus');
  
  if (minusBtn) {
    if (quantity <= 1) {
      minusBtn.style.display = 'none';
      minusBtn.disabled = true;
    } else {
      minusBtn.style.display = 'inline-block';
    minusBtn.disabled = false;
    }
  }
  
  if (plusBtn) {
    // Always enable plus button - let increaseQuantity handle stock validation
    plusBtn.style.display = 'inline-block';
    plusBtn.disabled = false;
  }
}

// Biến lưu thông tin sản phẩm cần xóa
let deleteProductId = null;
let deleteVariantId = null;

// Hiển thị modal xác nhận xóa
function showDeleteModal(productId, variantId) {
  console.log('showDeleteModal called:', { productId, variantId });
  
  deleteProductId = productId;
  deleteVariantId = variantId;
  
  const modal = document.querySelector('.delete-modal');
  if (modal) {
    modal.classList.add('show');
  }
}

// Đóng modal xóa
function closeDeleteModal() {
  const modal = document.querySelector('.delete-modal');
  if (modal) {
    modal.classList.remove('show');
  }
  
  deleteProductId = null;
  deleteVariantId = null;
}

// Xóa sản phẩm khỏi giỏ hàng - localStorage only
function removeFromCart(productId, variantId) {
  try {
    console.log('🗑️ Removing item:', { productId, variantId });
    
    // Get current cart data
    const cartData = localStorage.getItem('cart_data');
    let cartItems = [];
    
    if (cartData) {
      try {
        cartItems = JSON.parse(cartData);
        console.log('🔍 Current cart items:', cartItems);
      } catch (error) {
        console.error('Error parsing cart data:', error);
      }
    }
    
    // Remove item
    const originalLength = cartItems.length;
    cartItems = cartItems.filter(item => 
      !(item.product_id === productId && item.variant_id === variantId)
    );
    
    console.log('🔍 Items before:', originalLength, 'Items after:', cartItems.length);
    
    localStorage.setItem('cart_data', JSON.stringify(cartItems));
    console.log('✅ Item removed from localStorage');
    
    // Update UI
    updateCartUI();
    showNotification('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
    
  } catch (error) {
    console.error('Error removing from cart:', error);
    showNotification('Có lỗi xảy ra khi xóa sản phẩm', 'error');
  }
}

// Xóa toàn bộ giỏ hàng - localStorage only
function clearCart() {
  try {
    console.log('🗑️ Clearing entire cart...');
    
    // Clear cart data
    localStorage.setItem('cart_data', JSON.stringify([]));
    console.log('✅ Cart cleared from localStorage');
    
    // Update UI
    updateCartUI();
    showNotification('Đã xóa tất cả sản phẩm khỏi giỏ hàng', 'success');
    
  } catch (error) {
    console.error('Error clearing cart:', error);
    showNotification('Có lỗi xảy ra khi xóa giỏ hàng', 'error');
  }
}

// Cập nhật trạng thái nút số lượng
// Function này đã được thay thế bằng updateQuantityDisplay

// Render cart items HTML từ localStorage
function renderCartItems() {
  console.log('renderCartItems called');
  
  try {
    // Get cart data from localStorage
    const cartData = localStorage.getItem('cart_data');
    let cartItems = [];
    
    if (cartData) {
      try {
        cartItems = JSON.parse(cartData);
        console.log('🔍 Cart items in renderCartItems:', cartItems);
      } catch (error) {
        console.error('Error parsing cart data:', error);
      }
    }
    
    const cartItemsContainer = document.getElementById('cart-items');
    const cartEmptyDiv = document.getElementById('cart-empty');
    const cartContentDiv = document.getElementById('cart-content');
    
    if (!cartItemsContainer) {
      console.error('Cart items container not found');
      return;
    }
    
    // Clear existing content
    cartItemsContainer.innerHTML = '';
    
    if (cartItems.length === 0) {
      // Show empty cart
      if (cartEmptyDiv) cartEmptyDiv.style.display = 'block';
      if (cartContentDiv) cartContentDiv.style.display = 'none';
      return;
    }
    
    // Hide empty cart, show content
    if (cartEmptyDiv) cartEmptyDiv.style.display = 'none';
    if (cartContentDiv) cartContentDiv.style.display = 'block';
    
    // Render each cart item
    cartItems.forEach(item => {
      console.log('🔍 Rendering item:', item);
      
      const row = document.createElement('tr');
      row.setAttribute('data-product-id', item.product_id);
      row.setAttribute('data-variant-id', item.variant_id || '');
      
      // Ensure price is a number
      const price = parseFloat(item.price) || 0;
      const quantity = parseInt(item.quantity) || 1;
      const subtotal = price * quantity;
      
      console.log('🔍 Price calculation:', { 
        originalPrice: item.price, 
        parsedPrice: price, 
        quantity: quantity, 
        subtotal: subtotal 
      });
      
      row.innerHTML = `
        <td>
          <div class="d-flex align-items-center">
            <img src="${item.image || '/images/no-image.png'}" 
                 class="cart-item-image me-3" 
                 alt="${item.product_name || 'Sản phẩm'}"
                 onerror="this.src='/images/no-image.png'">
            <div>
              <h6 class="mb-1">${item.product_name || 'Sản phẩm'}</h6>
              <small class="text-muted">Sản phẩm gốc</small>
            </div>
          </div>
        </td>
        <td>
          <span class="badge bg-primary">Sản phẩm chính</span>
        </td>
        <td>
          <span class="text-warning fw-bold">${formatPrice(price)}</span>
        </td>
        <td>
          <div class="quantity-controls d-flex align-items-center">
            <button class="btn btn-sm btn-outline-secondary quantity-minus" 
                    onclick="decreaseQuantity(${item.product_id}, ${item.variant_id || 'null'})"
                    ${item.quantity <= 1 ? 'style="display:none"' : ''}>
              <i class="fas fa-minus"></i>
            </button>
            <input type="number" class="form-control quantity-input text-center" 
                   value="${quantity}" min="1" readonly>
            <button class="btn btn-sm btn-outline-secondary quantity-plus" 
                    onclick="increaseQuantity(${item.product_id}, ${item.variant_id || 'null'})">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </td>
        <td>
          <span class="fw-bold">${formatPrice(subtotal)}</span>
        </td>
        <td>
          <button class="btn btn-sm btn-outline-danger" 
                  onclick="showDeleteModal(${item.product_id}, ${item.variant_id || 'null'})"
                  title="Xóa sản phẩm">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `;
      
      cartItemsContainer.appendChild(row);
    });
    
    console.log('✅ Cart items rendered successfully');
    
  } catch (error) {
    console.error('Error in renderCartItems:', error);
  }
}

// Cập nhật giao diện giỏ hàng
function updateCartUI() {
  console.log('updateCartUI called');
  
  try {
    // Render cart items first
    renderCartItems();
    
    // Get cart data from localStorage
    const cartData = localStorage.getItem('cart_data');
    let cartItems = [];
    
    if (cartData) {
      try {
        cartItems = JSON.parse(cartData);
        console.log('🔍 Cart items in updateCartUI:', cartItems);
      } catch (error) {
        console.error('Error parsing cart data:', error);
      }
    }
    
    // Calculate totals
    const totalQuantity = cartItems.reduce((sum, item) => sum + item.quantity, 0);
    const totalPrice = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    console.log('🔍 Cart totals:', { totalQuantity, totalPrice, cartItems });
    
    // Cập nhật tổng tiền
      const cartTotalElement = document.getElementById('cart-total');
      const cartFinalTotalElement = document.getElementById('cart-final-total');
      
      if (cartTotalElement) {
      cartTotalElement.textContent = formatPrice(totalPrice);
      }
      if (cartFinalTotalElement) {
      cartFinalTotalElement.textContent = formatPrice(totalPrice);
    }
    
    // Cập nhật số lượng giỏ hàng trong header (nếu có)
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
      cartCountElement.textContent = totalQuantity;
    }
    
    // Check if cart is empty and show notification
    if (totalQuantity === 0) {
      showNotification('Giỏ hàng đã trống!', 'info');
    }
    
  } catch (error) {
    console.error('Error in updateCartUI:', error);
  }
}

// Format giá tiền
function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price) + '₫';
}

// Sửa giá 0 trong localStorage
function fixZeroPricesInLocalStorage() {
  try {
    const cartData = localStorage.getItem('cart_data');
    if (!cartData) return;
    
    let cartItems = JSON.parse(cartData);
    let hasZeroPrice = false;
    
    cartItems.forEach(item => {
      if (!item.price || item.price === 0) {
        console.log('🔧 Fixing zero price for item:', item);
        
        // Set default price based on product_id
        // Common prices for different product types
        const defaultPrices = {
          1: 500000,   // Product 1: 500k
          2: 300000,   // Product 2: 300k  
          3: 400000,   // Product 3: 400k
          4: 600000,   // Product 4: 600k
          5: 350000,   // Product 5: 350k
          6: 450000,   // Product 6: 450k
          7: 550000,   // Product 7: 550k
          8: 250000,   // Product 8: 250k
          9: 700000,   // Product 9: 700k
          10: 200000   // Product 10: 200k
        };
        
        item.price = defaultPrices[item.product_id] || 500000; // Default 500k
        hasZeroPrice = true;
        
        console.log('✅ Fixed price for item:', item);
      }
    });
    
    if (hasZeroPrice) {
      localStorage.setItem('cart_data', JSON.stringify(cartItems));
      console.log('✅ Fixed zero prices in localStorage');
    }
    
  } catch (error) {
    console.error('Error fixing zero prices:', error);
  }
}


// Hiển thị modal xóa
function showDeleteModal() {
  document.getElementById('deleteModal').classList.add('show');
}

// Đóng modal xóa
function closeDeleteModal() {
  document.getElementById('deleteModal').classList.remove('show');
}

// Xác nhận xóa
function confirmDelete() {
  if (deleteProductId !== null && deleteVariantId !== null) {
    // Xóa sản phẩm cụ thể từ localStorage
    removeFromCart(deleteProductId, deleteVariantId);
  } else {
    // Xóa toàn bộ giỏ hàng từ localStorage
    clearCart();
  }
  closeDeleteModal();
}

// Thực hiện xóa sản phẩm
function performDelete() {
  fetch('/cart/remove', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      product_id: deleteProductId,
      variant_id: deleteVariantId
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Xóa sản phẩm khỏi giao diện ngay lập tức
      const row = document.querySelector(`tr[data-product-id="${deleteProductId}"][data-variant-id="${deleteVariantId}"]`);
      if (row) {
        row.remove();
      }
      
      // Kiểm tra nếu giỏ hàng trống
      const remainingRows = document.querySelectorAll('tbody tr');
      if (remainingRows.length === 0) {
        location.reload(); // Reload để hiển thị trang giỏ hàng trống
        return;
      }
      
      updateCartUI(data);
      showNotification(data.message, 'success');
    } else {
      showNotification(data.message, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Có lỗi xảy ra!', 'error');
  });
}

// Thực hiện xóa toàn bộ giỏ hàng
function performClearCart() {
  fetch('/cart/clear', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      updateCartUI(data);
      showNotification(data.message, 'success');
    } else {
      showNotification(data.message, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Có lỗi xảy ra!', 'error');
  });
}

// Đóng modal khi click bên ngoài
document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeDeleteModal();
  }
});

// Gán function confirmDelete cho nút xác nhận
document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

// Cập nhật trạng thái nút số lượng khi trang load
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOMContentLoaded: Initializing button states...');
  
  // Auto-sync stock data from product detail
  setTimeout(() => {
    console.log('🔄 Auto-syncing stock data...');
    syncStockFromProductDetail();
  }, 1000);
  
  // Force reset stock data after 2 seconds to ensure correct values
  setTimeout(() => {
    console.log('🔄 Force resetting stock data...');
    forceResetStockCompletely();
  }, 2000);
  
  // Delay to avoid conflicts with cart-api.js initialization
  setTimeout(() => {
  const quantityRows = document.querySelectorAll('tr[data-product-id]');
    console.log('Found quantity rows:', quantityRows.length);
    
  quantityRows.forEach(row => {
    const productId = row.getAttribute('data-product-id');
    const variantId = row.getAttribute('data-variant-id');
    const quantityInput = row.querySelector('input[type="number"]');
    if (quantityInput) {
      const quantity = parseInt(quantityInput.value);
        console.log('Initializing buttons for:', {productId, variantId, quantity});
        updateQuantityDisplay(productId, variantId, quantity);
      }
    });
  }, 500); // Wait for cart-api.js to finish
});

// Function để reset stock data (debug)
function resetStockData() {
  const storageKey = 'product_stock_106';
  localStorage.removeItem(storageKey);
  console.log('Stock data reset for product 106');
  showNotification('Stock data đã được reset', 'info');
}

// Function để tạo stock data mới với đầy đủ variants (debug)
function createNewStockData() {
  const storageKey = 'product_stock_106';
  
  // Tạo stock data mới với đầy đủ variants
  const newStockData = {
    mainStock: 10, // Giả sử có 10 sản phẩm chính
    variants: {
      variant_136: 10, // Đỏ - 38
      variant_137: 10, // Đỏ - 39  
      variant_138: 10, // Đỏ - 40
      variant_139: 10, // Xanh - 38
      variant_140: 10  // Xanh - 39
    }
  };
  
  localStorage.setItem(storageKey, JSON.stringify(newStockData));
  console.log('New stock data created:', newStockData);
  showNotification('Stock data mới đã được tạo với đầy đủ variants', 'success');
}

// Function để reset stock data về giá trị ban đầu
function resetStockToOriginal() {
  const storageKey = 'product_stock_106';
  
  // Lấy cart data hiện tại để tính toán stock đúng
  const cartData = localStorage.getItem('cart_data');
  let cartItems = [];
  if (cartData) {
    try {
      cartItems = JSON.parse(cartData);
    } catch (error) {
      console.error('Error parsing cart data:', error);
    }
  }
  
  // Tính toán stock dựa trên cart hiện tại
  let mainStockUsed = 0;
  let variantStockUsed = {};
  
  cartItems.forEach(item => {
    if (item.variant_id && item.variant_id !== 0) {
      const variantKey = `variant_${item.variant_id}`;
      variantStockUsed[variantKey] = (variantStockUsed[variantKey] || 0) + item.quantity;
    } else {
      mainStockUsed += item.quantity;
    }
  });
  
  console.log('📊 Cart analysis:', {
    mainStockUsed,
    variantStockUsed,
    cartItems: cartItems.map(item => ({id: item.product_id, variant: item.variant_id, qty: item.quantity}))
  });
  
  // Reset về giá trị ban đầu nhưng trừ đi số lượng đã dùng trong cart
  const originalStockData = {
    mainStock: Math.max(0, 10 - mainStockUsed), // 10 - số lượng đã dùng
    variants: {
      variant_136: Math.max(0, 10 - (variantStockUsed.variant_136 || 0)),
      variant_137: Math.max(0, 10 - (variantStockUsed.variant_137 || 0)),
      variant_138: Math.max(0, 10 - (variantStockUsed.variant_138 || 0)),
      variant_139: Math.max(0, 10 - (variantStockUsed.variant_139 || 0)),
      variant_140: Math.max(0, 10 - (variantStockUsed.variant_140 || 0))
    }
  };
  
  localStorage.setItem(storageKey, JSON.stringify(originalStockData));
  console.log('Stock data reset to original (adjusted for cart):', originalStockData);
  // Bỏ thông báo toast để không làm phiền người dùng
  
  // Reload cart để áp dụng stock data mới
  if (window.cartAPIManager) {
    window.cartAPIManager.renderCartItems();
  }
}

// Function để sync stock data từ product detail page
function syncStockFromProductDetail() {
  console.log('🔄 Syncing stock data from product detail...');
  
  // Lấy tất cả product IDs từ cart
  const cartData = localStorage.getItem('cart_data');
  if (!cartData) return;
  
  const cartItems = JSON.parse(cartData);
  const productIds = [...new Set(cartItems.map(item => item.product_id))];
  
  productIds.forEach(productId => {
    const productDetailStock = localStorage.getItem(`product_stock_${productId}`);
    
    if (productDetailStock) {
      const stockData = JSON.parse(productDetailStock);
      console.log(`📦 Found product detail stock data for product ${productId}:`, stockData);
      
      // Giữ nguyên stock data từ product detail
      const completeStockData = {
        mainStock: stockData.mainStock || 0,
        variants: stockData.variants || {}
      };
      
      console.log(`📦 Complete stock data for product ${productId}:`, completeStockData);
      
      // Kiểm tra và thông báo nếu sản phẩm chính hết hàng
      if (completeStockData.mainStock === 0) {
        showNotification('⚠️ Sản phẩm chính đã hết hàng! Vui lòng chọn màu sắc và size khác.', 'warning');
      }
      
      // Cập nhật stock data cho cart
      localStorage.setItem(`product_stock_${productId}`, JSON.stringify(completeStockData));
      
      console.log(`✅ Stock data synced for product ${productId}`);
    } else {
      console.log(`❌ No product detail stock data found for product ${productId}`);
    }
  });
  
  // Reload cart để áp dụng stock data mới
  if (window.cartAPIManager) {
    window.cartAPIManager.renderCartItems();
  }
}

// Function để reduce stock khi tăng quantity
function reduceStockInLocalStorage(productId, variantId, quantity) {
  console.log(`📉 Reducing stock: productId=${productId}, variantId=${variantId}, quantity=${quantity}`);
  
  const storageKey = `product_stock_${productId}`;
  const stored = localStorage.getItem(storageKey);
  
  if (stored) {
    const stockData = JSON.parse(stored);
    
    if (variantId && variantId !== 0) {
      // Reduce variant stock
      const variantKey = `variant_${variantId}`;
      if (stockData.variants && stockData.variants[variantKey] !== undefined) {
        stockData.variants[variantKey] = Math.max(0, stockData.variants[variantKey] - quantity);
        console.log(`📉 Reduced variant ${variantKey}: -${quantity} = ${stockData.variants[variantKey]}`);
      }
    } else {
      // Reduce main product stock
      stockData.mainStock = Math.max(0, stockData.mainStock - quantity);
      console.log(`📉 Reduced main product: -${quantity} = ${stockData.mainStock}`);
    }
    
    localStorage.setItem(storageKey, JSON.stringify(stockData));
    console.log('📉 Stock reduced:', stockData);
  } else {
    console.log('❌ No stock data found to reduce');
  }
}

// Function để restore stock khi giảm quantity
function restoreStockInLocalStorage(productId, variantId, quantity) {
  console.log(`🔄 Restoring stock: productId=${productId}, variantId=${variantId}, quantity=${quantity}`);
  
  const storageKey = `product_stock_${productId}`;
  const stored = localStorage.getItem(storageKey);
  
  if (stored) {
    const stockData = JSON.parse(stored);
    
    if (variantId && variantId !== 0) {
      // Restore variant stock
      const variantKey = `variant_${variantId}`;
      if (stockData.variants && stockData.variants[variantKey] !== undefined) {
        stockData.variants[variantKey] += quantity;
        console.log(`🔄 Restored variant ${variantKey}: +${quantity} = ${stockData.variants[variantKey]}`);
      }
    } else {
      // Restore main product stock
      stockData.mainStock += quantity;
      console.log(`🔄 Restored main product: +${quantity} = ${stockData.mainStock}`);
    }
    
    localStorage.setItem(storageKey, JSON.stringify(stockData));
    console.log('🔄 Stock restored:', stockData);
  } else {
    console.log('❌ No stock data found to restore');
  }
}

// Function để xem stock data hiện tại (debug)
function viewStockData() {
  // Lấy tất cả product IDs từ cart
  const cartData = localStorage.getItem('cart_data');
  if (!cartData) {
    showNotification('No cart data found', 'info');
    return;
  }
  
  const cartItems = JSON.parse(cartData);
  const productIds = [...new Set(cartItems.map(item => item.product_id))];
  
  console.log('Cart product IDs:', productIds);
  
  productIds.forEach(productId => {
    const storageKey = `product_stock_${productId}`;
    const stored = localStorage.getItem(storageKey);
    console.log(`Product ${productId} stock data:`, stored);
    
    if (stored) {
      const stockData = JSON.parse(stored);
      console.log(`Product ${productId} parsed stock data:`, stockData);
      showNotification(`Product ${productId}: Main=${stockData.mainStock}, Variants=${JSON.stringify(stockData.variants)}`, 'info');
    } else {
      showNotification(`Product ${productId}: No stock data found`, 'info');
    }
  });
}

// Function để debug cart data và stock data
function debugCartData() {
  console.log('🔍 Debugging cart data...');
  
  // Lấy cart data
  const cartData = localStorage.getItem('cart_data');
  if (!cartData) {
    console.log('❌ No cart data found');
    showNotification('No cart data found', 'error');
    return;
  }
  
  const cartItems = JSON.parse(cartData);
  console.log('🔍 Cart items:', cartItems);
  
  // Hiển thị chi tiết từng item
  cartItems.forEach((item, index) => {
    console.log(`🔍 Item ${index}:`, {
      product_id: item.product_id,
      variant_id: item.variant_id,
      quantity: item.quantity,
      price: item.price,
      total: item.price * item.quantity
    });
  });
  
  // Tính tổng
  const totalQuantity = cartItems.reduce((sum, item) => sum + item.quantity, 0);
  const totalPrice = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  
  console.log('🔍 Cart totals:', { totalQuantity, totalPrice });
  showNotification(`Cart: ${totalQuantity} items, Total: ${formatPrice(totalPrice)}`, 'info');
  
  // Debug stock data
  const productIds = [...new Set(cartItems.map(item => item.product_id))];
  console.log('🔍 Cart product IDs:', productIds);
  
  productIds.forEach(productId => {
    const storageKey = `product_stock_${productId}`;
    const stored = localStorage.getItem(storageKey);
    
    console.log(`🔍 Product ${productId} stock data:`, stored);
    
    if (stored) {
      const stockData = JSON.parse(stored);
      console.log(`🔍 Product ${productId} parsed stock data:`, stockData);
      console.log(`🔍 Product ${productId} mainStock:`, stockData.mainStock);
      console.log(`🔍 Product ${productId} variants:`, stockData.variants);
    } else {
      console.log(`❌ Product ${productId}: No stock data found`);
    }
  });
}

// Function để debug stock data và force sync
function debugStockData() {
  console.log('🔍 Debugging stock data...');
  
  // Lấy tất cả product IDs từ cart
  const cartData = localStorage.getItem('cart_data');
  if (!cartData) {
    console.log('❌ No cart data found');
    return;
  }
  
  const cartItems = JSON.parse(cartData);
  const productIds = [...new Set(cartItems.map(item => item.product_id))];
  
  console.log('🔍 Cart product IDs:', productIds);
  
  productIds.forEach(productId => {
    const storageKey = `product_stock_${productId}`;
    const stored = localStorage.getItem(storageKey);
    
    console.log(`🔍 Product ${productId} stock data:`, stored);
    
    if (stored) {
      const stockData = JSON.parse(stored);
      console.log(`🔍 Product ${productId} parsed stock data:`, stockData);
      console.log(`🔍 Product ${productId} mainStock:`, stockData.mainStock);
      console.log(`🔍 Product ${productId} variants:`, stockData.variants);
    } else {
      console.log(`❌ Product ${productId}: No stock data found`);
    }
  });
  
  // Force sync từ product detail
  console.log('🔄 Force syncing from product detail...');
  syncStockFromProductDetail();
}

// Function để force reset stock data ngay lập tức
function forceResetStock() {
  console.log('🔄 Force resetting stock data immediately...');
  resetStockToOriginal();
  // Bỏ thông báo toast để không làm phiền người dùng
}

// Function để kiểm tra và sửa stock data cho sản phẩm chính
function fixMainProductStock() {
  const storageKey = 'product_stock_106';
  const stored = localStorage.getItem(storageKey);
  
  if (stored) {
    const stockData = JSON.parse(stored);
    console.log('🔧 Current stock data before fix:', stockData);
    
    // Force set main product stock to 10
    stockData.mainStock = 10;
    
    localStorage.setItem(storageKey, JSON.stringify(stockData));
    console.log('🔧 Fixed main product stock:', stockData);
    // Bỏ thông báo toast để không làm phiền người dùng
  } else {
    console.log('❌ No stock data found to fix');
    showNotification('Không tìm thấy stock data để sửa', 'error');
  }
}

// Function để debug cart và stock data
function debugCartAndStock() {
  console.log('🔍 === DEBUG CART AND STOCK ===');
  
  // Debug cart data
  const cartData = localStorage.getItem('cart_data');
  let cartItems = [];
  if (cartData) {
    try {
      cartItems = JSON.parse(cartData);
    } catch (error) {
      console.error('Error parsing cart data:', error);
    }
  }
  
  console.log('🛒 Current cart items:', cartItems.map(item => ({
    product_id: item.product_id,
    variant_id: item.variant_id,
    quantity: item.quantity,
    name: item.name
  })));
  
  // Debug stock data
  const storageKey = 'product_stock_106';
  const stored = localStorage.getItem(storageKey);
  if (stored) {
    const stockData = JSON.parse(stored);
    console.log('📦 Current stock data:', stockData);
    
    // Tính toán stock đúng
    let mainStockUsed = 0;
    let variantStockUsed = {};
    
    cartItems.forEach(item => {
      if (item.variant_id && item.variant_id !== 0) {
        const variantKey = `variant_${item.variant_id}`;
        variantStockUsed[variantKey] = (variantStockUsed[variantKey] || 0) + item.quantity;
      } else {
        mainStockUsed += item.quantity;
      }
    });
    
    console.log('📊 Stock calculation:', {
      mainStockUsed,
      variantStockUsed,
      expectedMainStock: Math.max(0, 10 - mainStockUsed),
      expectedVariant137: Math.max(0, 10 - (variantStockUsed.variant_137 || 0))
    });
  }
  
  console.log('🔍 === END DEBUG ===');
}

// Function để force reset stock về giá trị ban đầu hoàn toàn
function forceResetStockCompletely() {
  console.log('🔄 Force resetting stock data...');
  
  // Lấy tất cả product IDs từ cart
  const cartData = localStorage.getItem('cart_data');
  if (!cartData) return;
  
  const cartItems = JSON.parse(cartData);
  const productIds = [...new Set(cartItems.map(item => item.product_id))];
  
  productIds.forEach(productId => {
    const storageKey = `product_stock_${productId}`;
    
    // Lấy stock data gốc từ product detail page
    const productDetailStock = localStorage.getItem(storageKey);
    if (productDetailStock) {
      const originalStockData = JSON.parse(productDetailStock);
      console.log(`🔄 Resetting stock for product ${productId} to original:`, originalStockData);
      
      // Kiểm tra và thông báo nếu sản phẩm chính hết hàng
      if (originalStockData.mainStock === 0) {
        showNotification('⚠️ Sản phẩm chính đã hết hàng! Vui lòng chọn màu sắc và size khác.', 'warning');
      }
      
      // Reset về giá trị gốc từ product detail
      localStorage.setItem(storageKey, JSON.stringify(originalStockData));
    } else {
      console.log(`❌ No original stock data found for product ${productId}`);
    }
  });
  
  // Reload cart để áp dụng stock data mới
  if (window.cartAPIManager) {
    window.cartAPIManager.renderCartItems();
  }
}

// Function hiển thị thông báo
function showNotification(message, type = 'info') {
  // Tạo toast notification
  const toast = document.createElement('div');
  toast.className = `toast-notification toast-${type}`;
  toast.innerHTML = `
    <div class="toast-content">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
      <span>${message}</span>
  </div>
  `;
  
  // Thêm CSS cho toast
  toast.style.cssText = `
    position: fixed;
    top: 100px;
    right: 20px;
    background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#17a2b8'};
    color: ${type === 'warning' ? '#000' : 'white'};
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 99999;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    max-width: 300px;
    font-size: 14px;
  `;
  
  document.body.appendChild(toast);
  
  // Animation hiện
  setTimeout(() => {
    toast.style.transform = 'translateX(0)';
  }, 100);
  
  // Tự động xóa sau 3 giây - thông báo warning hiển thị lâu hơn
  const displayTime = type === 'warning' ? 5000 : 3000;
  setTimeout(() => {
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => {
      if (toast.parentElement) {
        toast.remove();
      }
    }, 300);
  }, displayTime);
}
</script>
@endsection


