/**
 * Price and Variant Management Functions
 * Handles price calculations, variant selection, and UI updates
 */

/**
 * Function để cập nhật giá khi chọn màu/size
 */
function updatePrice() {
  console.log('=== updatePrice called ===');
  
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  const priceElement = document.getElementById('product-price');
  
  if (!priceElement) {
    console.log('Price element not found');
    return;
  }
  
  // Tìm variant phù hợp
  let matchingVariants = productVariants;
  
  if (selectedColor) {
    const colorName = selectedColor.dataset.color;
    matchingVariants = matchingVariants.filter(v => v.color_name === colorName);
    console.log('Filtered by color:', colorName, 'variants:', matchingVariants);
  }
  
  if (selectedSize) {
    const sizeName = selectedSize.dataset.size;
    matchingVariants = matchingVariants.filter(v => v.size_name === sizeName);
    console.log('Filtered by size:', sizeName, 'variants:', matchingVariants);
  }
  
  // Nếu không có variant phù hợp → hiển thị giá chính
  if (matchingVariants.length === 0) {
    console.log('No matching variant found, showing main product price'); // Debug
    
    // Kiểm tra sản phẩm chính có còn hàng không
    const mainStock = mainProductStock;
    if (mainStock <= 0) {
      console.log('Main product out of stock, showing message'); // Debug
      showMainProductOutOfStockMessage();
      return;
    }
    
    showDefaultPrice();
    if (window.StockManagement && window.StockManagement.updateStockDisplay) {
      window.StockManagement.updateStockDisplay();
    }
    return;
  }
  
  if (matchingVariants.length > 0) {
    const variant = matchingVariants[0];
    const stock = parseInt(variant.stock) || 0;
    
    console.log('Found matching variant:', variant); // Debug
    
    // Kiểm tra nếu hết hàng
    if (stock <= 0) {
      console.log('Variant out of stock, checking if any variant available');
      
      // Kiểm tra xem có variant nào khác còn hàng không
      const hasAnyAvailableVariant = productVariants.some(v => (parseInt(v.stock) || 0) > 0);
      console.log('Any variant available:', hasAnyAvailableVariant);
      
      if (hasAnyAvailableVariant) {
        // Có variant khác còn hàng - tự động chuyển sang
        console.log('Calling autoSelectAvailableVariant');
        if (window.PriceVariantManagement && window.PriceVariantManagement.autoSelectAvailableVariant) {
          window.PriceVariantManagement.autoSelectAvailableVariant();
        }
        return;
      } else {
        // Tất cả variants đều hết hàng - hiển thị giá sản phẩm chính
        console.log('All variants out of stock, showing main product price');
        showDefaultPrice();
        if (window.StockManagement && window.StockManagement.updateStockDisplay) {
          window.StockManagement.updateStockDisplay();
        }
        return;
      }
    }
    
    // Logic tính giá rõ ràng (giống CartService):
    // 1. Nếu variant có giá khuyến mãi > 0, dùng giá khuyến mãi
    // 2. Nếu variant có giá bán > 0, dùng giá bán
    // 3. Nếu variant không có giá, dùng giá sản phẩm chính
    let displayPrice = 0;
    if (variant.sale_price && variant.sale_price > 0) {
      displayPrice = variant.sale_price;
      console.log('Using sale price:', displayPrice);
    } else if (variant.price && variant.price > 0) {
      displayPrice = variant.price;
      console.log('Using variant price:', displayPrice);
    } else {
      // Fallback về giá sản phẩm chính
      displayPrice = 0; // Sẽ được cập nhật từ server
      console.log('Using main product price:', displayPrice);
    }
    
    // Hiển thị giá
    priceElement.textContent = formatPrice(displayPrice);
    
    // Cập nhật hiển thị stock
    if (window.StockManagement && window.StockManagement.updateStockDisplay) {
      window.StockManagement.updateStockDisplay(stock);
    }
    
    // Cập nhật trạng thái nút
    if (window.StockManagement && window.StockManagement.updateAddToCartButton) {
      window.StockManagement.updateAddToCartButton(stock);
    }
    
    console.log('Price updated to:', displayPrice, 'Stock:', stock);
  }
}

/**
 * Function để hiển thị giá mặc định (sản phẩm chính)
 */
function showDefaultPrice() {
  const priceElement = document.getElementById('product-price');
  if (priceElement) {
    const mainPrice = 0; // Sẽ được cập nhật từ server
    priceElement.textContent = formatPrice(mainPrice);
  }
  
  // Cập nhật stock hiển thị
  if (window.StockManagement && window.StockManagement.updateStockDisplay) {
    window.StockManagement.updateStockDisplay();
  }
}

/**
 * Function để format giá tiền
 */
function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price);
}

/**
 * Function để lấy variant ID đã chọn
 */
function getSelectedVariantId() {
  console.log('=== getSelectedVariantId called ===');
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  console.log('Selected color:', selectedColor ? selectedColor.dataset.color : 'none');
  console.log('Selected size:', selectedSize ? selectedSize.dataset.size : 'none');
  
  if (!selectedColor || !selectedSize) {
    console.log('No color/size selected, returning null for main product');
    return null; // Trả về null cho sản phẩm chính
  }
  
  const colorName = selectedColor.dataset.color;
  const sizeName = selectedSize.dataset.size;
  
  const variant = productVariants.find(v => 
    v.color_name === colorName && v.size_name === sizeName
  );
  
  if (variant) {
    console.log('Found variant ID:', variant.id);
    return variant.id;
  }
  
  console.log('No matching variant found, returning null');
  return null;
}

/**
 * Function để kiểm tra stock trước khi mua
 */
function checkStockBeforeBuy() {
  const quantity = parseInt(document.getElementById('quantity').value) || 1;
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  let availableStock = 0;
  let stockType = 'main';
  
  if (selectedColor && selectedSize) {
    // Nếu có chọn màu/size, kiểm tra stock của variant cụ thể
    const colorName = selectedColor.dataset.color;
    const sizeName = selectedSize.dataset.size;
    
    const variant = productVariants.find(v => 
      v.color_name === colorName && v.size_name === sizeName
    );
    
    if (variant) {
      availableStock = parseInt(variant.stock) || 0;
      stockType = 'variant';
    }
  } else {
    // Nếu không chọn màu/size, kiểm tra stock sản phẩm chính
    availableStock = mainProductStock;
    stockType = 'main';
  }
  
  if (quantity > availableStock) {
    if (availableStock <= 0) {
      if (stockType === 'main') {
        if (window.CartValidation && window.CartValidation.showNotification) {
          window.CartValidation.showNotification('Sản phẩm chính đã hết hàng! Vui lòng chọn màu và size.', 'error');
        }
      } else {
        if (window.CartValidation && window.CartValidation.showNotification) {
          window.CartValidation.showNotification('Biến thể này đã hết hàng! Vui lòng chọn màu và size khác.', 'error');
        }
      }
    } else {
      if (window.CartValidation && window.CartValidation.showNotification) {
        window.CartValidation.showNotification(`Số lượng vượt quá tồn kho! Chỉ còn ${availableStock} sản phẩm.`, 'error');
      }
    }
    return false;
  }
  
  return true;
}

/**
 * Function để kiểm tra stock trước khi thêm vào giỏ
 */
function checkStockBeforeAddToCart() {
  console.log('=== checkStockBeforeAddToCart called ===');
  
  const quantity = parseInt(document.getElementById('quantity').value) || 1;
  const selectedColor = document.querySelector('[data-color].active');
  const selectedSize = document.querySelector('[data-size].active');
  
  console.log('Input data:', {
    quantity: quantity,
    selectedColor: !!selectedColor,
    selectedSize: !!selectedSize
  });
  
  let availableStock = 0;
  let stockType = 'main';
  
  // Kiểm tra stock sản phẩm chính
  const mainStock = mainProductStock;
  console.log('Main stock:', mainStock);
  
  if (selectedColor && selectedSize) {
    // Nếu có chọn màu/size, kiểm tra stock của variant cụ thể
    const colorName = selectedColor.dataset.color;
    const sizeName = selectedSize.dataset.size;
    
    const variant = productVariants.find(v => 
      v.color_name === colorName && v.size_name === sizeName
    );
    
    if (variant) {
      availableStock = parseInt(variant.stock) || 0;
      stockType = 'variant';
      console.log('Using variant stock:', availableStock, 'from variant:', variant);
    } else {
      console.log('No matching variant found, using main stock');
      availableStock = mainStock;
      stockType = 'main';
    }
  } else {
    // Nếu không chọn màu/size, kiểm tra stock sản phẩm chính
    availableStock = mainStock;
    stockType = 'main';
    console.log('Using main product stock:', availableStock);
  }
  
  console.log('Stock check result:', {quantity, availableStock, stockType});
  
  if (quantity > availableStock) {
    console.log('Quantity exceeds available stock');
    if (availableStock <= 0) {
      console.log('Available stock is 0 or negative');
      if (stockType === 'main') {
        // Sản phẩm chính hết hàng - tự động chuyển sang variant
        console.log('Main product out of stock, switching to variant');
        if (window.CartValidation && window.CartValidation.showNotification) {
          window.CartValidation.showNotification('Sản phẩm chính đã hết hàng! Đang chuyển sang chọn màu và size...', 'warning');
        }
        if (window.PriceVariantManagement && window.PriceVariantManagement.autoSelectAvailableVariant) {
          window.PriceVariantManagement.autoSelectAvailableVariant();
        }
        return false;
      } else {
        // Variant hết hàng - kiểm tra xem có variant nào khác còn hàng không
        const hasAnyAvailableVariant = productVariants.some(v => (parseInt(v.stock) || 0) > 0);
        console.log('Variant out of stock, checking if any variant available:', hasAnyAvailableVariant);
        
        if (hasAnyAvailableVariant) {
          // Có variant khác còn hàng - thông báo chọn variant khác
          if (window.CartValidation && window.CartValidation.showNotification) {
            window.CartValidation.showNotification('Biến thể này đã hết hàng! Vui lòng chọn màu và size khác.', 'error');
          }
        } else {
          // Tất cả variants đều hết hàng - thông báo sử dụng sản phẩm chính
          if (window.CartValidation && window.CartValidation.showNotification) {
            window.CartValidation.showNotification('Tất cả biến thể đã hết hàng! Bạn có thể mua sản phẩm chính.', 'info');
          }
        }
        return false;
      }
    } else {
      console.log('Quantity exceeds available stock but stock > 0');
      if (window.CartValidation && window.CartValidation.showNotification) {
        window.CartValidation.showNotification(`Số lượng vượt quá tồn kho! Chỉ còn ${availableStock} sản phẩm.`, 'error');
      }
      return false;
    }
  }
  
  console.log('Stock check passed, allowing add to cart');
  return true;
}

/**
 * Function để tự động chuyển sang variant có sẵn
 */
function autoSelectAvailableVariant() {
  console.log('=== autoSelectAvailableVariant called ===');
  
  if (!productVariants || productVariants.length === 0) {
    console.log('No product variants found');
    if (window.CartValidation && window.CartValidation.showNotification) {
      window.CartValidation.showNotification('Không có biến thể nào có sẵn!', 'error');
    }
    return false;
  }
  
  console.log('Total variants:', productVariants.length);
  
  // Tìm variant có stock > 0
  const availableVariants = productVariants.filter(v => {
    const stock = parseInt(v.stock) || 0;
    console.log(`Variant ${v.id} (${v.color_name}, ${v.size_name}): stock = ${v.stock} -> parsed = ${stock}`);
    return stock > 0;
  });
  console.log('Available variants:', availableVariants.length);
  
  if (availableVariants.length === 0) {
    console.log('No available variants found');
    if (window.CartValidation && window.CartValidation.showNotification) {
      window.CartValidation.showNotification('Tất cả biến thể đã hết hàng!', 'error');
    }
    return false;
  }
  
  // Chọn variant đầu tiên có sẵn
  const selectedVariant = availableVariants[0];
  console.log('Selected variant:', selectedVariant);
  
  // Cập nhật UI
  updateVariantSelection(selectedVariant);
  
  return true;
}

/**
 * Function để cập nhật UI khi chọn variant
 */
function updateVariantSelection(variant) {
  console.log('=== updateVariantSelection called ===');
  console.log('Variant to select:', variant);
  
  // Cập nhật màu sắc
  if (variant.color_name) {
    console.log('Updating color to:', variant.color_name);
    const colorButtons = document.querySelectorAll('[data-color]');
    colorButtons.forEach(btn => {
      btn.classList.remove('active');
      if (btn.dataset.color === variant.color_name) {
        btn.classList.add('active');
        console.log('Activated color button:', btn);
      }
    });
  }
  
  // Cập nhật size
  if (variant.size_name) {
    console.log('Updating size to:', variant.size_name);
    const sizeButtons = document.querySelectorAll('[data-size]');
    sizeButtons.forEach(btn => {
      btn.classList.remove('active');
      if (btn.dataset.size === variant.size_name) {
        btn.classList.add('active');
        console.log('Activated size button:', btn);
      }
    });
  }
  
  // Cập nhật giá
  console.log('Updating price...');
  if (window.PriceVariantManagement && window.PriceVariantManagement.updatePrice) {
    window.PriceVariantManagement.updatePrice();
  }
  
  // Hiển thị thông báo
  console.log('Showing notification...');
  if (window.CartValidation && window.CartValidation.showNotification) {
    window.CartValidation.showNotification(`Đã chuyển sang ${variant.color_name || 'màu'} ${variant.size_name || 'size'} có sẵn (${variant.stock} sản phẩm)`, 'success');
  }
}

// Export functions for global access
window.PriceVariantManagement = {
  updatePrice: updatePrice,
  showDefaultPrice: showDefaultPrice,
  formatPrice: formatPrice,
  getSelectedVariantId: getSelectedVariantId,
  checkStockBeforeBuy: checkStockBeforeBuy,
  checkStockBeforeAddToCart: checkStockBeforeAddToCart,
  autoSelectAvailableVariant: autoSelectAvailableVariant,
  updateVariantSelection: updateVariantSelection
};