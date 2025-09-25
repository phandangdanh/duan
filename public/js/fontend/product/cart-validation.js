/**
 * Cart and Validation Functions
 * Handles cart operations, validation, and user interactions
 */

/**
 * Function để kiểm tra validation trước khi thêm vào giỏ
 */
function validateSelection() {
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  // Kiểm tra stock của sản phẩm chính
  const mainProductStockValue = mainProductStock;
  
  // Nếu sản phẩm chính còn hàng, không cần bắt buộc chọn màu/size
  if (mainProductStockValue > 0) {
    return true; // Cho phép mua sản phẩm chính
  }
  
  // Nếu sản phẩm chính hết hàng, mới bắt buộc chọn màu/size
  const hasColorOptions = document.querySelectorAll('[data-color]').length > 0;
  const hasSizeOptions = document.querySelectorAll('[data-size]').length > 0;
  
  if (hasColorOptions && hasSizeOptions) {
    // Nếu có cả màu và size options, phải chọn cả hai
    if (selectedColor && !selectedSize) {
      showNotification('Vui lòng chọn size!', 'error');
      return false;
    }
    
    if (selectedSize && !selectedColor) {
      showNotification('Vui lòng chọn màu!', 'error');
      return false;
    }
    
    if (!selectedColor && !selectedSize) {
      showNotification('Sản phẩm chính đã hết hàng! Vui lòng chọn màu và size.', 'error');
      return false;
    }
  }
  
  return true;
}

/**
 * Function để disable/enable nút dựa trên việc chọn màu/size
 */
function updateButtonState() {
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  // Disable/enable size options based on color selection
  updateSizeOptionsState(selectedColor);
  
  // Cập nhật buttons dựa trên stock hiện tại và selection
  const currentStock = getCurrentStock();
  updateAddToCartButton(currentStock);
  
  console.log('Button state updated:', { 
    selectedColor: !!selectedColor, 
    selectedSize: !!selectedSize,
    currentStock: currentStock
  });
}

/**
 * Function để lấy stock hiện tại dựa trên selection
 */
function getCurrentStock() {
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  if (selectedColor && selectedSize) {
    // Nếu có cả màu và size, lấy stock của variant cụ thể
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
    // Nếu chưa chọn gì, lấy tổng stock của tất cả variants + main product
    const totalVariantStock = productVariants.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
    const mainProductStockValue = mainProductStock;
    return totalVariantStock + mainProductStock;
  }
}

/**
 * Function để disable/enable size options dựa trên việc chọn màu
 */
function updateSizeOptionsState(selectedColor) {
  const sizeOptions = document.querySelectorAll('[data-size]');
  const mainProductStock = mainProductStock;
  
  sizeOptions.forEach(option => {
    if (!selectedColor && mainProductStock <= 0) {
      // Nếu chưa chọn màu VÀ sản phẩm chính hết hàng, disable tất cả size
      option.disabled = true;
      option.style.opacity = '0.5';
      option.style.cursor = 'not-allowed';
      option.style.pointerEvents = 'none';
      
      // Thêm class để styling
      option.classList.add('disabled');
    } else {
      // Nếu đã chọn màu HOẶC sản phẩm chính còn hàng, enable tất cả size
      option.disabled = false;
      option.style.opacity = '1';
      option.style.cursor = 'pointer';
      option.style.pointerEvents = 'auto';
      
      // Xóa class disabled
      option.classList.remove('disabled');
    }
  });
  
  console.log('Size options state updated:', { 
    hasSelectedColor: !!selectedColor, 
    mainProductStock: mainProductStock,
    sizeOptionsCount: sizeOptions.length 
  });
}

/**
 * Function để thêm vào giỏ hàng từ trang chi tiết
 */
function addToCartFromDetail() {
  // Kiểm tra validation trước
  if (!validateSelection()) {
    return;
  }
  
  const productIdValue = productId;
  const variantId = getSelectedVariantId();
  const quantity = parseInt(document.getElementById('quantity').value) || 1;
  
  console.log('=== addToCartFromDetail called ===');
  console.log('Product ID:', productId);
  console.log('Variant ID:', variantId);
  console.log('Quantity:', quantity);
  
  // Kiểm tra stock trước khi thêm
  if (!checkStockBeforeAddToCart()) {
    return;
  }
  
  // Thêm vào giỏ hàng
  console.log('Adding to cart:', {productId, variantId, quantity});
  
  // Lấy CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  console.log('CSRF Token:', csrfToken);
  
  console.log('Starting addToCart process...');
  
  // Gọi function addToCart global (từ layout)
  addToCart(productId, variantId, quantity)
    .then(response => {
      console.log('Add to cart response:', response);
      console.log('Response type:', typeof response);
      console.log('Response success:', response && response.success);
      
      if (response && response.success) {
        console.log('Adding to cart successful, updating stock display...'); // Debug
        
        // Lưu dữ liệu vào localStorage
        console.log('=== SAVING TO LOCALSTORAGE ===');
        const cartKey = `cart_${productId}`;
        console.log('Cart key:', cartKey);
        const cartData = JSON.parse(localStorage.getItem(cartKey) || '{}');
        console.log('Current cart data:', cartData);
        
        if (!cartData.main_quantity) cartData.main_quantity = 0;
        if (!cartData.variant_quantities) cartData.variant_quantities = {};
        
        if (variantId === null) {
          // Thêm vào sản phẩm chính
          cartData.main_quantity += quantity;
          console.log('Updated main quantity in localStorage:', cartData.main_quantity);
        } else {
          // Thêm vào biến thể
          cartData.variant_quantities[variantId] = (cartData.variant_quantities[variantId] || 0) + quantity;
          console.log('Updated variant quantity in localStorage:', cartData.variant_quantities[variantId]);
        }
        
        localStorage.setItem(cartKey, JSON.stringify(cartData));
        console.log('Saved to localStorage:', cartData);
        console.log('=== END SAVING TO LOCALSTORAGE ===');
        
        displayDetailedVariantInfo(); // Only call this one
        showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
      } else {
        console.log('Add to cart failed:', response);
        showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
      }
    })
    .catch(error => {
      console.error('Error adding to cart:', error);
      showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
    });
}

/**
 * Function để mua ngay
 */
function buyNow() {
  // Kiểm tra validation trước
  if (!validateSelection()) {
    return;
  }
  
  const productIdValue = productId;
  const variantId = getSelectedVariantId();
  const quantity = parseInt(document.getElementById('quantity').value) || 1;
  
  // Kiểm tra stock trước khi mua
  if (!checkStockBeforeBuy()) {
    return;
  }
  
  // Chuyển đến trang thanh toán
  const url = `/checkout?product_id=${productId}&variant_id=${variantId || ''}&quantity=${quantity}`;
  window.location.href = url;
}

/**
 * Function để tăng số lượng
 */
function increaseQuantity() {
  const quantityInput = document.getElementById('quantity');
  const currentQuantity = parseInt(quantityInput.value) || 0;
  quantityInput.value = currentQuantity + 1;
  checkStock();
}

/**
 * Function để giảm số lượng
 */
function decreaseQuantity() {
  const quantityInput = document.getElementById('quantity');
  const currentQuantity = parseInt(quantityInput.value) || 0;
  if (currentQuantity > 1) {
    quantityInput.value = currentQuantity - 1;
    checkStock();
  }
}

/**
 * Function để hiển thị thông báo
 */
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

/**
 * Function để hiển thị thông tin variant chi tiết
 */
function displayDetailedVariantInfo() {
  console.log('=== displayDetailedVariantInfo called ===');
  displayVariantDetailsFromLocal(); // Use local data first
  const productIdValue = productId;
  fetch(`/api/product/${productId}/detailed-stock`)
    .then(response => response.json())
    .then(data => {
      console.log('=== Detailed stock data from API ===', data);
      if (data.success) {
        updateDetailedVariantDisplay(data);
      }
    })
    .catch(error => {
      console.error('Error fetching detailed stock from API:', error);
    });
}

/**
 * Function để cập nhật hiển thị variant chi tiết từ API
 */
function updateDetailedVariantDisplay(data) {
  const variantInfo = document.getElementById('variant-info');
  if (!variantInfo) return;

  let variantDetailsHtml = '';
  if (data.variant_details && data.variant_details.length > 0) {
    const variantsByColor = {};
    data.variant_details.forEach(variant => {
      if (!variantsByColor[variant.color_name]) {
        variantsByColor[variant.color_name] = [];
      }
      variantsByColor[variant.color_name].push(variant);
    });
    
    const colorDetails = Object.keys(variantsByColor).map(colorName => {
      const sizes = variantsByColor[colorName].map(variant => 
        `Size ${variant.size_name}: ${variant.stock}`
      ).join(', ');
      return `${colorName} (${sizes})`;
    }).join(' | ');
    
    variantDetailsHtml = colorDetails;
  }

  variantInfo.innerHTML = `
    <div class="variant-summary">
      <strong>Biến thể hiện có:</strong> ${data.total_variant_stock} sản phẩm<br>
      <small class="text-muted">${variantDetailsHtml}</small>
    </div>
  `;
  
  const mainStockElement = document.getElementById('main-stock');
  if (mainStockElement) {
    mainStockElement.textContent = data.main_stock;
  }
  
  const variantStockElement = document.getElementById('variant-stock');
  if (variantStockElement) {
    variantStockElement.textContent = data.total_variant_stock;
  }
  
  const availableStockElement = document.getElementById('available-stock');
  if (availableStockElement) {
    availableStockElement.textContent = data.total_stock;
  }
}

/**
 * Function để hiển thị thông tin variant chi tiết từ localStorage
 */
function displayVariantDetailsFromLocal() {
  try {
    console.log('=== displayVariantDetailsFromLocal called ===');
    console.log('Product variants:', productVariants);
    console.log('Function is running...');
    
    console.log('Starting stock calculation...');

    // Lấy dữ liệu từ localStorage
    const productIdValue = productId;
    const cartKey = `cart_${productId}`;
    const cartData = JSON.parse(localStorage.getItem(cartKey) || '{}');
    
    console.log('Cart data from localStorage:', cartData);
    
    // Nếu không có dữ liệu cart, tạo mới
    if (!cartData.main_quantity) {
      cartData.main_quantity = 0;
    }
    if (!cartData.variant_quantities) {
      cartData.variant_quantities = {};
    }

    // Tính tổng stock từ productVariants (trừ đi số lượng đã thêm vào giỏ)
    let totalVariantStock = productVariants.reduce((sum, variant) => {
      const originalStock = parseInt(variant.stock) || 0;
      const cartQuantity = cartData.variant_quantities[variant.id] || 0;
      const remainingStock = Math.max(0, originalStock - cartQuantity);
      console.log(`Variant ${variant.id}: original=${originalStock}, cart=${cartQuantity}, remaining=${remainingStock}`);
      return sum + remainingStock;
    }, 0);
    
    console.log('=== VARIANT STOCK CALCULATION ===');
    console.log('Product variants count:', productVariants.length);
    console.log('Total variant stock calculated:', totalVariantStock);

    // Tính main product stock (trừ đi số lượng đã thêm vào giỏ)
    const mainProductStockValue = mainProductStock;
    const mainCartQuantity = cartData.main_quantity || 0;
    const mainStock = Math.max(0, mainProductStock - mainCartQuantity);
    console.log(`Main stock: original=${mainProductStock}, cart=${mainCartQuantity}, remaining=${mainStock}`);

    console.log('Total variant stock:', totalVariantStock);

    // Nhóm variants theo màu
    const variantsByColor = {};
    productVariants.forEach(variant => {
      if (!variantsByColor[variant.color_name]) {
        variantsByColor[variant.color_name] = [];
      }
      variantsByColor[variant.color_name].push(variant);
    });
    
    // Tạo HTML chi tiết biến thể
    const colorDetails = Object.keys(variantsByColor).map(colorName => {
      const sizes = variantsByColor[colorName].map(variant => {
        const originalStock = parseInt(variant.stock) || 0;
        const cartQuantity = cartData.variant_quantities[variant.id] || 0;
        const remainingStock = Math.max(0, originalStock - cartQuantity);
        return `Size ${variant.size_name}: ${remainingStock}`;
      }).join(', ');
      return `${colorName} (${sizes})`;
    }).join(' | ');
    
    console.log('Variant details:', colorDetails);
    console.log('Stock calculation completed');
  
    // Cập nhật các element khác
    console.log('=== UPDATING HTML ELEMENTS ===');
    
    const mainStockElement = document.getElementById('main-stock');
    console.log('Main stock element found:', !!mainStockElement);
    if (mainStockElement) {
      mainStockElement.textContent = mainStock;
      console.log('Updated main-stock to:', mainStock);
    }
    
    const variantStockElement = document.getElementById('variant-stock');
    console.log('Variant stock element found:', !!variantStockElement);
    if (variantStockElement) {
      variantStockElement.textContent = totalVariantStock;
      console.log('Updated variant-stock to:', totalVariantStock);
    }
    
    const availableStockElement = document.getElementById('available-stock');
    console.log('Available stock element found:', !!availableStockElement);
    if (availableStockElement) {
      availableStockElement.textContent = mainStock + totalVariantStock;
      console.log('Updated available-stock to:', mainStock + totalVariantStock);
    }
    
    console.log('Updated stock display:', {
      mainStock,
      totalVariantStock,
      totalStock: mainStock + totalVariantStock
    });
    
    // Kiểm tra và thông báo hết hàng
    if (mainStock === 0 && totalVariantStock === 0) {
      showNotification('Sản phẩm đã hết hàng!', 'warning');
    } else if (mainStock === 0) {
      showNotification('Sản phẩm chính đã hết hàng! Vui lòng chọn màu và size.', 'warning');
    }
  
  } catch (error) {
    console.error('Error in displayVariantDetailsFromLocal:', error);
  }
}

// Export functions for global access
window.CartValidation = {
  validateSelection: validateSelection,
  updateButtonState: updateButtonState,
  getCurrentStock: getCurrentStock,
  updateSizeOptionsState: updateSizeOptionsState,
  addToCartFromDetail: addToCartFromDetail,
  buyNow: buyNow,
  increaseQuantity: increaseQuantity,
  decreaseQuantity: decreaseQuantity,
  showNotification: showNotification,
  displayDetailedVariantInfo: displayDetailedVariantInfo,
  updateDetailedVariantDisplay: updateDetailedVariantDisplay,
  displayVariantDetailsFromLocal: displayVariantDetailsFromLocal
};
