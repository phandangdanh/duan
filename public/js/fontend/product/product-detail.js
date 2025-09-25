/**
 * Product Detail Page JavaScript
 * Handles product variant selection, stock management, and cart operations
 */

// Global variables
let productVariants = [];
let productId = null;
let mainProductStock = 0;

/**
 * Initialize product detail page
 */
function initProductDetail(variants, productIdValue, mainStock) {
  productVariants = variants;
  productId = productIdValue;
  mainProductStock = mainStock;
  
  console.log('=== INITIALIZING PRODUCT DETAIL ===');
  console.log('Product ID:', productId);
  console.log('Main Product Stock:', mainProductStock);
  console.log('Product Variants:', productVariants);
  
  // Initialize UI
  initializeUI();
  
  // Setup event listeners
  setupEventListeners();
  
  // Update initial state
  updateInitialState();
}

/**
 * Initialize UI elements
 */
function initializeUI() {
  // Check if there are variants with stock > 0
  const availableVariants = productVariants.filter(v => (parseInt(v.stock) || 0) > 0);
  console.log(`Found ${availableVariants.length} variants with stock > 0:`, availableVariants);
  
  // Setup debug functions
  setupDebugFunctions();
}

/**
 * Setup debug functions for testing
 */
function setupDebugFunctions() {
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
        const variant = testFilter(color, size);
        if (variant) {
          console.log(`✅ ${color} + ${size}: Stock = ${variant.stock}`);
        } else {
          console.log(`❌ ${color} + ${size}: Not available`);
        }
      });
    });
  };
  
  // Function để test trường hợp sản phẩm chính hết hàng (debug)
  window.testMainProductOutOfStock = function() {
    console.log('=== TESTING: Main Product Out of Stock ===');
    
    // Simulate sản phẩm chính hết hàng
    const mainStockElement = document.getElementById('main-stock');
    if (mainStockElement) {
      mainStockElement.textContent = '0';
    }
    
    // Cập nhật size options state
    updateSizeOptionsState(null);
    
    // Test validation
    const validationResult = validateSelection();
    console.log('Validation result:', validationResult);
    
    // Test auto select variant
    autoSelectAvailableVariant();
    
    console.log('=== END TEST ===');
  };
  
  // Function để test trường hợp sản phẩm chính còn hàng (debug)
  window.testMainProductInStock = function() {
    console.log('=== TESTING: Main Product In Stock ===');
    
    // Restore sản phẩm chính còn hàng
    const mainStockElement = document.getElementById('main-stock');
    if (mainStockElement) {
      mainStockElement.textContent = mainProductStock;
    }
    
    // Cập nhật size options state
    updateSizeOptionsState(null);
    
    // Test validation
    const validationResult = validateSelection();
    console.log('Validation result:', validationResult);
    
    console.log('=== END TEST ===');
  };
  
  // Function để clear localStorage (debug)
  window.clearCartData = function() {
    const cartKey = `cart_${productId}`;
    localStorage.removeItem(cartKey);
    console.log('Cleared localStorage for product:', productId);
    displayDetailedVariantInfo();
  };
}

/**
 * Setup all event listeners
 */
function setupEventListeners() {
  setupColorOptions();
  setupSizeOptions();
  setupQuantityControls();
  setupActionButtons();
}

/**
 * Setup color options event listeners
 */
function setupColorOptions() {
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
        updateButtonState(); // Cập nhật trạng thái nút (sẽ disable size options)
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
      
      // Cập nhật trạng thái nút
      updateButtonState();
    });
  });
}

/**
 * Setup size options event listeners
 */
function setupSizeOptions() {
  const sizeOptions = document.querySelectorAll('[data-size]');
  sizeOptions.forEach(option => {
    option.addEventListener('click', function() {
      // Không cho phép chọn size bị disabled
      if (this.classList.contains('disabled') || this.style.display === 'none') {
        const mainProductStock = mainProductStock;
        if (mainProductStock <= 0) {
          showNotification('Vui lòng chọn màu trước!', 'warning');
        }
        return;
      }
      
      // Nếu đã active thì bỏ chọn
      if (this.classList.contains('active')) {
        this.classList.remove('active');
        updatePrice();
        updateButtonState(); // Cập nhật trạng thái nút
        return;
      }
      
      // Bỏ chọn tất cả và chọn cái này
      sizeOptions.forEach(opt => opt.classList.remove('active'));
      this.classList.add('active');
      
      // Cập nhật giá và stock
      updatePrice();
      updateButtonState(); // Cập nhật trạng thái nút
    });
  });
}

/**
 * Setup quantity controls event listeners
 */
function setupQuantityControls() {
  // Quantity controls are handled by onclick attributes in HTML
  // This function can be extended if needed
}

/**
 * Setup action buttons event listeners
 */
function setupActionButtons() {
  // Action buttons are handled by onclick attributes in HTML
  // This function can be extended if needed
}

/**
 * Update initial state
 */
function updateInitialState() {
  // Khởi tạo hiển thị - chỉ dùng 1 API
  displayDetailedVariantInfo();
  
  // Cập nhật trạng thái nút ban đầu
  updateButtonState();
  
  // Cập nhật size options dựa trên stock sản phẩm chính
  updateSizeOptionsState(null);
}

// Export functions for global access
window.ProductDetail = {
  init: initProductDetail,
  updatePrice: updatePrice,
  updateButtonState: updateButtonState,
  validateSelection: validateSelection,
  addToCartFromDetail: addToCartFromDetail,
  buyNow: buyNow,
  increaseQuantity: increaseQuantity,
  decreaseQuantity: decreaseQuantity,
  checkStock: checkStock,
  showNotification: showNotification
};
