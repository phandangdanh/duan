/**
 * Stock Management Functions
 * Handles stock calculations, display updates, and inventory management
 */

/**
 * Function để cập nhật hiển thị size theo màu đã chọn
 */
function updateSizeOptions() {
  const selectedColor = document.querySelector('[data-color].active');
  const sizeOptions = document.querySelectorAll('[data-size]');
  
  if (!selectedColor) {
    // Nếu không chọn màu, hiển thị tất cả size nhưng disable chúng
    sizeOptions.forEach(option => {
      option.style.display = 'inline-block';
      option.classList.remove('active'); // Bỏ chọn size
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
        option.disabled = false;
        option.style.opacity = '1';
        option.style.cursor = 'pointer';
        option.style.pointerEvents = 'auto';
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

/**
 * Function tự động chọn size còn hàng
 */
function autoSelectAvailableSize() {
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  const sizeOptions = document.querySelectorAll('[data-size]');
  
  // Nếu đã chọn size và size đó còn hàng, không cần thay đổi
  if (selectedSize) {
    const sizeName = selectedSize.dataset.size;
    const colorName = selectedColor ? selectedColor.dataset.color : null;
    
    const variant = productVariants.find(v => 
      v.color_name === colorName && v.size_name === sizeName
    );
    
    if (variant && (parseInt(variant.stock) || 0) > 0) {
      return; // Size hiện tại còn hàng
    }
  }
  
  // Tìm size còn hàng đầu tiên
  const availableSizes = sizeOptions.filter(option => {
    const sizeName = option.dataset.size;
    const colorName = selectedColor ? selectedColor.dataset.color : null;
    
    const variant = productVariants.find(v => 
      v.color_name === colorName && v.size_name === sizeName
    );
    
    return variant && (parseInt(variant.stock) || 0) > 0;
  });
  
  if (availableSizes.length > 0) {
    // Bỏ chọn size hiện tại
    sizeOptions.forEach(opt => opt.classList.remove('active'));
    
    // Chọn size còn hàng đầu tiên
    availableSizes[0].classList.add('active');
    
    // Cập nhật giá
    updatePrice();
  }
}

/**
 * Function để lấy stock của variant cụ thể
 */
function getVariantStock(colorName, sizeName) {
  console.log(`=== getVariantStock called ===`);
  console.log('Looking for:', { colorName, sizeName });
  console.log('Available variants:', productVariants);
  
  const variant = productVariants.find(v => 
    v.color_name === colorName && v.size_name === sizeName
  );
  
  if (variant) {
    const stock = parseInt(variant.stock) || 0;
    console.log('Found variant:', variant);
    console.log('Stock:', stock);
    return stock;
  }
  
  console.log('No variant found');
  return 0;
}

/**
 * Function để lấy tổng stock
 */
function getTotalStock() {
  const mainStock = mainProductStock;
  const variantStock = productVariants.reduce((sum, variant) => {
    const stock = parseInt(variant.stock) || 0;
    return sum + stock;
  }, 0);
  
  return mainStock + variantStock;
}

/**
 * Function để hiển thị thông báo hết hàng
 */
function showOutOfStockMessage() {
  const stockInfo = document.getElementById('stock-info');
  const stockError = document.getElementById('stock-error');
  const addToCartBtn = document.querySelector('.btn-add-cart');
  const buyNowBtn = document.querySelector('.btn-buy-now');
  
  // Ẩn thông tin stock
  if (stockInfo) {
    stockInfo.style.display = 'none';
  }
  
  // Hiển thị thông báo lỗi
  if (stockError) {
    stockError.style.display = 'block';
    stockError.innerHTML = '<small class="text-danger">Sản phẩm đã hết hàng!</small>';
  }
  
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

/**
 * Function để hiển thị thông báo sản phẩm chính hết hàng
 */
function showMainProductOutOfStockMessage() {
  const stockInfo = document.getElementById('stock-info');
  const stockError = document.getElementById('stock-error');
  
  // Ẩn thông tin stock
  if (stockInfo) {
    stockInfo.style.display = 'none';
  }
  
  // Hiển thị thông báo lỗi
  if (stockError) {
    stockError.style.display = 'block';
    stockError.innerHTML = '<small class="text-danger">Sản phẩm chính đã hết hàng! Vui lòng chọn màu và size.</small>';
  }
  
  // Cập nhật số lượng hiển thị
  if (stockInfo) {
    stockInfo.textContent = '0';
  }
  
  // Disable nút thêm vào giỏ
  updateAddToCartButton(0);
}

/**
 * Function để cập nhật hiển thị stock chi tiết
 */
function updateDetailedStockDisplay(data) {
  console.log('=== updateDetailedStockDisplay Debug ===');
  console.log('Data received:', data);
  
  // Cập nhật các element stock
  const mainStockElement = document.getElementById('main-stock');
  const variantStockElement = document.getElementById('variant-stock');
  const availableStockElement = document.getElementById('available-stock');
  
  console.log('Elements found:', {
    mainStockElement: !!mainStockElement,
    variantStockElement: !!variantStockElement,
    availableStockElement: !!availableStockElement
  });
  
  if (mainStockElement) {
    mainStockElement.textContent = data.mainStock || 0;
    console.log('Updated main-stock to:', data.mainStock || 0);
  }
  
  if (variantStockElement) {
    variantStockElement.textContent = data.variantStock || 0;
    console.log('Updated variant-stock to:', data.variantStock || 0);
  }
  
  if (availableStockElement) {
    availableStockElement.textContent = data.totalStock || 0;
    console.log('Updated available-stock to:', data.totalStock || 0);
  }
  
  console.log('=== End Debug ===');
}

/**
 * Function để cập nhật hiển thị stock
 */
function updateStockDisplay(stock = null) {
  console.log('=== updateStockDisplay called ===');
  
  if (stock !== null) {
    // Sử dụng stock được truyền vào
    console.log('Using provided stock:', stock);
    
    const stockInfo = document.getElementById('stock-info');
    if (stockInfo) {
      stockInfo.style.display = 'block';
    }
    
    const stockError = document.getElementById('stock-error');
    if (stockError) {
      stockError.style.display = 'none';
    }
    
    const availableStockElement = document.getElementById('available-stock');
    if (availableStockElement) {
      availableStockElement.textContent = stock;
    }
    
    updateAddToCartButton(stock);
    return;
  }
  
  // Gọi API để lấy số lượng hiển thị từ CartService
  console.log('=== updateStockDisplay: Calling API ===');
  fetch(`/api/cart/display-stock/${productId}`, {
    method: 'GET',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    console.log('=== API Response data ===');
    console.log('Response:', data);
    console.log('=== End API Response ===');
    
    if (data.success) {
      // Hiển thị số lượng có sẵn (đã trừ giỏ hàng)
      document.getElementById('available-stock').textContent = data.totalStock;
      
      // Hiển thị chi tiết số lượng đã trừ giỏ hàng
      updateDetailedStockDisplay(data);
      
      // Cập nhật trạng thái nút "Thêm vào giỏ" dựa trên stock
      updateAddToCartButton(data.totalStock);
      
      // Kiểm tra nếu hết hàng
      if (data.totalStock <= 0) {
        // Kiểm tra xem có variant nào còn hàng không
        const hasAvailableVariants = productVariants.some(v => v.stock > 0);
        
        if (hasAvailableVariants) {
          // Có variant còn hàng - tự động chuyển sang
          showNotification('Sản phẩm chính đã hết hàng! Đang chuyển sang chọn màu và size...', 'warning');
          setTimeout(() => {
            autoSelectAvailableVariant();
          }, 1000);
        } else {
          // Tất cả đều hết hàng
          showOutOfStockMessage();
        }
      }
    }
  })
  .catch(error => {
    console.error('Error:', error);
    // Fallback về tính toán cũ
    const mainProductStockValue = mainProductStock;
    const variantStock = productVariants.reduce((sum, variant) => {
      const variantStock = parseInt(variant.stock) || 0;
      return sum + variantStock;
    }, 0);
    
    const totalStock = mainProductStockValue + variantStock;
    
    document.getElementById('available-stock').textContent = totalStock;
    document.getElementById('main-stock').textContent = mainProductStockValue;
    document.getElementById('variant-stock').textContent = variantStock;
    
    updateAddToCartButton(totalStock);
  });
}

/**
 * Function để cập nhật trạng thái nút "Thêm vào giỏ"
 */
function updateAddToCartButton(stock) {
  const addToCartBtn = document.querySelector('.btn-add-cart');
  const buyNowBtn = document.querySelector('.btn-buy-now');
  
  if (stock <= 0) {
    // Hết hàng - disable nút
    if (addToCartBtn) {
      addToCartBtn.disabled = true;
      addToCartBtn.style.opacity = '0.5';
      addToCartBtn.style.cursor = 'not-allowed';
      addToCartBtn.innerHTML = '<i class="fas fa-times me-2"></i>HẾT HÀNG';
      addToCartBtn.classList.remove('btn-warning');
      addToCartBtn.classList.add('btn-secondary');
    }
    
    if (buyNowBtn) {
      buyNowBtn.disabled = true;
      buyNowBtn.style.opacity = '0.5';
      buyNowBtn.style.cursor = 'not-allowed';
      buyNowBtn.innerHTML = '<i class="fas fa-times me-2"></i>HẾT HÀNG';
      buyNowBtn.classList.remove('btn-success');
      buyNowBtn.classList.add('btn-secondary');
    }
  } else {
    // Còn hàng - enable nút
    if (addToCartBtn) {
      addToCartBtn.disabled = false;
      addToCartBtn.style.opacity = '1';
      addToCartBtn.style.cursor = 'pointer';
      addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ';
      addToCartBtn.classList.remove('btn-secondary');
      addToCartBtn.classList.add('btn-warning');
    }
    
    if (buyNowBtn) {
      buyNowBtn.disabled = false;
      buyNowBtn.style.opacity = '1';
      buyNowBtn.style.cursor = 'pointer';
      buyNowBtn.innerHTML = '<i class="fas fa-bolt me-2"></i>Mua ngay';
      buyNowBtn.classList.remove('btn-secondary');
      buyNowBtn.classList.add('btn-success');
    }
  }
}

/**
 * Function để kiểm tra tồn kho khi thay đổi số lượng
 */
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
    availableStock = productVariants.reduce((sum, variant) => {
      const stock = parseInt(variant.stock) || 0;
      return sum + stock;
    }, 0);
    
    // Cộng thêm stock sản phẩm chính
    availableStock += mainProductStock;
  }
  
  if (quantity > availableStock) {
    // Hiển thị thông báo lỗi
    if (stockError) {
      stockError.style.display = 'block';
      stockError.innerHTML = `<small class="text-danger">Số lượng vượt quá tồn kho! Chỉ còn ${availableStock} sản phẩm.</small>`;
    }
    
    // Ẩn thông tin stock
    const stockInfo = document.getElementById('stock-info');
    if (stockInfo) {
      stockInfo.style.display = 'none';
    }
    
    // Disable nút thêm vào giỏ
    updateAddToCartButton(0);
    
    return;
  }
  
  // Hiển thị thông tin tồn kho
  const stockInfo = document.getElementById('stock-info');
  if (stockInfo) {
    stockInfo.style.display = 'block';
  }
  
  // Ẩn thông báo lỗi
  if (stockError) {
    stockError.style.display = 'none';
  }
  
  // Cập nhật hiển thị stock
  updateStockDisplay(availableStock);
}

/**
 * Function để cập nhật trạng thái size options
 */
function updateSizeOptionsState(selectedColor) {
  const sizeOptions = document.querySelectorAll('[data-size]');
  
  sizeOptions.forEach(option => {
    if (!selectedColor) {
      // Nếu không chọn màu, hiển thị tất cả size nhưng disable chúng
      option.style.display = 'inline-block';
      option.disabled = true;
      option.style.opacity = '0.5';
      option.style.cursor = 'not-allowed';
      option.style.pointerEvents = 'none';
      option.classList.add('disabled');
    } else {
      // Nếu đã chọn màu, hiển thị size options có sẵn cho màu đó
      const colorName = selectedColor.dataset.color;
      const availableSizes = productVariants
        .filter(variant => variant.color_name === colorName)
        .map(variant => variant.size_name);
      
      const sizeName = option.dataset.size;
      if (availableSizes.includes(sizeName)) {
        option.style.display = 'inline-block';
        option.disabled = false;
        option.style.opacity = '1';
        option.style.cursor = 'pointer';
        option.style.pointerEvents = 'auto';
        option.classList.remove('disabled');
      } else {
        option.style.display = 'none';
        option.disabled = true;
        option.style.opacity = '0.5';
        option.style.cursor = 'not-allowed';
        option.style.pointerEvents = 'none';
        option.classList.add('disabled');
      }
    }
  });
}

// Export functions for global access
window.StockManagement = {
  updateSizeOptions: updateSizeOptions,
  autoSelectAvailableSize: autoSelectAvailableSize,
  getVariantStock: getVariantStock,
  getTotalStock: getTotalStock,
  showOutOfStockMessage: showOutOfStockMessage,
  showMainProductOutOfStockMessage: showMainProductOutOfStockMessage,
  updateDetailedStockDisplay: updateDetailedStockDisplay,
  updateStockDisplay: updateStockDisplay,
  updateAddToCartButton: updateAddToCartButton,
  checkStock: checkStock,
  updateSizeOptionsState: updateSizeOptionsState
};
