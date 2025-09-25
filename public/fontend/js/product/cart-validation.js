/**
 * Cart Validation and Operations Functions
 * Handles cart operations, quantity controls, and validation logic
 */

/**
 * Function để hiển thị thông báo toast
 */
function showNotification(message, type = 'info') {
  console.log('=== showNotification called ===');
  console.log('Message:', message);
  console.log('Type:', type);
  
  // Sử dụng alert đơn giản để tránh delay
  alert(message);
}

/**
 * Function để load toastr library
 */
function loadToastr() {
  return new Promise((resolve, reject) => {
    // Kiểm tra xem jQuery đã load chưa
    if (typeof $ === 'undefined') {
      console.log('jQuery not loaded, loading jQuery first...');
      const jqueryScript = document.createElement('script');
      jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
      jqueryScript.onload = () => {
        console.log('jQuery loaded, now loading toastr...');
        loadToastrScript().then(resolve).catch(reject);
      };
      jqueryScript.onerror = reject;
      document.head.appendChild(jqueryScript);
    } else {
      console.log('jQuery already loaded, loading toastr...');
      loadToastrScript().then(resolve).catch(reject);
    }
  });
}

/**
 * Function để load toastr script
 */
function loadToastrScript() {
  return new Promise((resolve, reject) => {
    const toastrScript = document.createElement('script');
    toastrScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js';
    toastrScript.onload = () => {
      console.log('Toastr script loaded');
      
      // Load toastr CSS
      const toastrCSS = document.createElement('link');
      toastrCSS.rel = 'stylesheet';
      toastrCSS.href = 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css';
      document.head.appendChild(toastrCSS);
      
      // Configure toastr
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      };
      
      resolve();
    };
    toastrScript.onerror = reject;
    document.head.appendChild(toastrScript);
  });
}

/**
 * Function để hiển thị toastr notification
 */
function showToastrNotification(message, type) {
  console.log('=== showToastrNotification called ===');
  console.log('Message:', message);
  console.log('Type:', type);
  
  switch(type) {
    case 'success':
      toastr.success(message);
      break;
    case 'error':
      toastr.error(message);
      break;
    case 'warning':
      toastr.warning(message);
      break;
    case 'info':
    default:
      toastr.info(message);
      break;
  }
}

/**
 * Function để validate selection trước khi thêm vào giỏ
 */
function validateSelection() {
  console.log('=== validateSelection called ===');
  
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  const mainProductStockValue = mainProductStock;
  
  console.log('Selected color:', selectedColor ? selectedColor.dataset.color : 'none');
  console.log('Selected size:', selectedSize ? selectedSize.dataset.size : 'none');
  console.log('Main product stock:', mainProductStockValue);
  
  // Nếu sản phẩm chính còn hàng, không cần bắt buộc chọn màu/size
  if (mainProductStockValue > 0) {
    console.log('Main product in stock, validation passed');
    return true;
  }
  
  // Nếu sản phẩm chính hết hàng, bắt buộc phải chọn màu và size
  const hasColorOptions = document.querySelectorAll('[data-color]').length > 0;
  const hasSizeOptions = document.querySelectorAll('[data-size]').length > 0;
  
  console.log('Has color options:', hasColorOptions);
  console.log('Has size options:', hasSizeOptions);
  
  if (hasColorOptions && hasSizeOptions) {
    if (selectedColor && !selectedSize) {
      console.log('Color selected but no size selected');
      showNotification('Vui lòng chọn size!', 'error');
      return false;
    }
    
    if (selectedSize && !selectedColor) {
      console.log('Size selected but no color selected');
      showNotification('Vui lòng chọn màu!', 'error');
      return false;
    }
    
    if (!selectedColor && !selectedSize) {
      console.log('No color and size selected');
      showNotification('Sản phẩm chính đã hết hàng! Vui lòng chọn màu và size.', 'error');
      return false;
    }
  }
  
  console.log('Validation passed');
  return true;
}

/**
 * Function để cập nhật trạng thái nút
 */
function updateButtonState() {
  console.log('=== updateButtonState called ===');
  
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  console.log('Selected color:', selectedColor ? selectedColor.dataset.color : 'none');
  console.log('Selected size:', selectedSize ? selectedSize.dataset.size : 'none');
  
  // Cập nhật trạng thái size options
  updateSizeOptionsState(selectedColor);
  
  // Lấy stock hiện tại
  const currentStock = getCurrentStock();
  console.log('Current stock:', currentStock);
  
  // Cập nhật trạng thái nút
  updateAddToCartButton(currentStock);
}

/**
 * Function để lấy stock hiện tại
 */
function getCurrentStock() {
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  if (selectedColor && selectedSize) {
    // Nếu có chọn cả màu và size, lấy stock của variant cụ thể
    const colorName = selectedColor.dataset.color;
    const sizeName = selectedSize.dataset.size;
    
    const variant = productVariants.find(v => 
      v.color_name === colorName && v.size_name === sizeName
    );
    
    return variant ? (parseInt(variant.stock) || 0) : 0;
  } else if (selectedColor) {
    // Nếu chỉ chọn màu, lấy tổng stock của màu đó
    const colorName = selectedColor.dataset.color;
    const colorVariants = productVariants.filter(v => v.color_name === colorName);
    return colorVariants.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
  } else if (selectedSize) {
    // Nếu chỉ chọn size, lấy tổng stock của size đó
    const sizeName = selectedSize.dataset.size;
    const sizeVariants = productVariants.filter(v => v.size_name === sizeName);
    return sizeVariants.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
  } else {
    // Nếu không chọn gì, lấy tổng stock (main + variants)
    const totalVariantStock = productVariants.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
    return totalVariantStock + mainProductStock;
  }
}

/**
 * Function để thêm vào giỏ hàng từ trang chi tiết
 */
function addToCartFromDetail() {
  console.log('=== addToCartFromDetail called ===');
  
  // Validate selection trước
  if (!validateSelection()) {
    console.log('Validation failed');
    return;
  }
  
  const productIdValue = productId;
  const variantId = getSelectedVariantId();
  const quantity = parseInt(document.getElementById('quantity').value) || 1;
  
  console.log('Product ID:', productIdValue);
  console.log('Variant ID:', variantId);
  console.log('Quantity:', quantity);
  
  // Kiểm tra stock trước khi thêm vào giỏ
  if (!checkStockBeforeAddToCart()) {
    console.log('Stock check failed');
    return;
  }
  
  console.log('Adding to cart:', {productId: productIdValue, variantId: variantId, quantity: quantity});
  
  // Gọi API thêm vào giỏ hàng
  addToCart(productIdValue, variantId, quantity)
    .then(response => {
      console.log('Add to cart response:', response);
      console.log('Response type:', typeof response);
      console.log('Response success:', response.success);
      
      if (response && response.success) {
        console.log('Adding to cart successful, updating stock display...');
        
        // Cập nhật localStorage
        const cartKey = `cart_${productIdValue}`;
        let cartData = JSON.parse(localStorage.getItem(cartKey) || '{}');
        
        if (!cartData.main_quantity) cartData.main_quantity = 0;
        if (!cartData.variant_quantities) cartData.variant_quantities = {};
        
        if (variantId === null) {
          // Thêm vào sản phẩm chính
          cartData.main_quantity += quantity;
        } else {
          // Thêm vào variant
          cartData.variant_quantities[variantId] = (cartData.variant_quantities[variantId] || 0) + quantity;
        }
        
        localStorage.setItem(cartKey, JSON.stringify(cartData));
        
        // Cập nhật hiển thị stock
        if (window.StockManagement && window.StockManagement.updateStockDisplay) {
          window.StockManagement.updateStockDisplay();
        }
        
        // Không hiển thị thông báo thành công ở đây vì addToCart global đã hiển thị
        console.log('Cart updated successfully');
      } else {
        console.log('Add to cart failed:', response.message);
        showNotification(response.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
      }
    })
    .catch(error => {
      console.error('Error adding to cart:', error);
      showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
    });
}

/**
 * Function để mua ngay (đơn giản như trang chủ)
 */
function buyNow() {
  console.log('=== buyNow called ===');
  
  const productIdValue = productId;
  const quantity = parseInt(document.getElementById('quantity').value) || 1;
  
  console.log('Product ID:', productIdValue);
  console.log('Quantity:', quantity);
  
  // Đơn giản: chỉ gọi function buyNowFromHomepage như trang chủ
  if (typeof buyNowFromHomepage === 'function') {
    buyNowFromHomepage(productIdValue);
  } else {
    // Fallback: redirect trực tiếp
    console.log('buyNowFromHomepage not available, redirecting directly...');
    window.location.href = `/checkout?product_id=${productIdValue}&quantity=${quantity}`;
  }
}

/**
 * Function để tăng số lượng
 */
function increaseQuantity() {
  const quantityInput = document.getElementById('quantity');
  const currentQuantity = parseInt(quantityInput.value) || 1;
  const newQuantity = currentQuantity + 1;
  
  quantityInput.value = newQuantity;
  
  // Kiểm tra stock
  checkStock();
}

/**
 * Function để giảm số lượng
 */
function decreaseQuantity() {
  const quantityInput = document.getElementById('quantity');
  const currentQuantity = parseInt(quantityInput.value) || 1;
  
  if (currentQuantity > 1) {
    const newQuantity = currentQuantity - 1;
    quantityInput.value = newQuantity;
    
    // Kiểm tra stock
    checkStock();
  }
}

// Export functions for global access
window.CartValidation = {
  showNotification: showNotification,
  validateSelection: validateSelection,
  updateButtonState: updateButtonState,
  getCurrentStock: getCurrentStock,
  addToCartFromDetail: addToCartFromDetail,
  buyNow: buyNow,
  increaseQuantity: increaseQuantity,
  decreaseQuantity: decreaseQuantity
};