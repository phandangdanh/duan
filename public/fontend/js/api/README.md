# API JavaScript Structure

Cấu trúc API JavaScript được tổ chức trong thư mục `/public/fontend/js/api/` để phân biệt và quản lý các chức năng API một cách có hệ thống.

## 📁 Cấu trúc thư mục

```
public/fontend/js/api/
├── index.js                    # File chính - load tất cả API clients
├── api-client.js              # API Client chính - tổng hợp tất cả
├── home-api-client.js         # API Client cho trang chủ
├── product-api-client.js      # API Client cho sản phẩm
├── cart-api-client.js         # API Client cho giỏ hàng
└── category-api-client.js     # API Client cho danh mục
```

## 🔧 Cách sử dụng

### 1. Load API system trong layout chính

```html
<!-- Trong layout chính -->
<script src="/fontend/js/api/index.js"></script>
```

### 2. Sử dụng API trong các trang

```javascript
// Kiểm tra API đã sẵn sàng chưa
if (isAPIReady()) {
    const api = getAPI();
    
    // Sử dụng các API clients
    const products = await api.product.getProducts();
    const categories = await api.category.getAllCategories();
    const cartInfo = await api.cart.getCartInfo();
}
```

### 3. Sử dụng trong trang chủ

```javascript
// Trong home-api.js
class HomePageManager {
    constructor() {
        this.api = window.API; // Sử dụng API Client chính
    }
    
    async loadFeaturedProducts() {
        const products = await this.api.home.getFeaturedProducts(8);
        this.renderFeaturedProducts(products);
    }
}
```

## 📋 Các API Clients

### HomeAPIClient
- `getAllData()` - Lấy tất cả dữ liệu trang chủ
- `getFeaturedProducts(limit)` - Sản phẩm nổi bật
- `getSaleProducts(limit)` - Sản phẩm khuyến mãi
- `getBestSellingProducts(limit)` - Sản phẩm bán chạy
- `getFeaturedCategories(limit)` - Danh mục nổi bật
- `getStatistics()` - Thống kê
- `searchProducts(keyword, limit)` - Tìm kiếm

### ProductAPIClient
- `getProducts(filters)` - Danh sách sản phẩm với bộ lọc
- `getProductById(id)` - Chi tiết sản phẩm
- `getProductStock(id)` - Thông tin stock
- `getDetailedStock(id)` - Stock chi tiết
- `getDisplayStock(id)` - Stock hiển thị
- `searchProducts(keyword, options)` - Tìm kiếm sản phẩm
- `getProductsByCategory(categoryId)` - Sản phẩm theo danh mục
- `getSaleProducts(options)` - Sản phẩm khuyến mãi
- `getLatestProducts(limit)` - Sản phẩm mới nhất

### CartAPIClient
- `addToCart(productId, variantId, quantity)` - Thêm vào giỏ
- `updateCartItem(productId, variantId, quantity)` - Cập nhật giỏ hàng
- `removeFromCart(productId, variantId)` - Xóa khỏi giỏ
- `clearCart()` - Xóa toàn bộ giỏ hàng
- `getCartInfo()` - Thông tin giỏ hàng
- `getDisplayStock(productId)` - Stock hiển thị

### CategoryAPIClient
- `getAllCategories()` - Tất cả danh mục
- `getCategoryById(id)` - Chi tiết danh mục
- `getRootCategories()` - Danh mục gốc
- `getChildCategories(parentId)` - Danh mục con
- `getActiveCategories()` - Danh mục active
- `searchCategories(keyword)` - Tìm kiếm danh mục
- `getCategoryTree()` - Cây danh mục

## 🚀 Tính năng nâng cao

### Error Handling
```javascript
try {
    const products = await api.product.getProducts();
} catch (error) {
    const errorResult = api.handleError(error, 'loading products');
    api.showError(errorResult.message);
}
```

### Retry Mechanism
```javascript
const products = await api.retryRequest(
    () => api.product.getProducts(),
    3, // max retries
    1000 // delay
);
```

### Cache Management
```javascript
// Set cache
api.setCache('featured_products', products, 300000); // 5 minutes

// Get cache
const cachedProducts = api.getCache('featured_products');

// Clear cache
api.clearCache('featured_products');
```

### Loading States
```javascript
// Show loading
api.showLoading();

// Hide loading
api.hideLoading();

// Show section loading
api.showLoading(document.getElementById('products-container'));
```

## 🔄 Event System

```javascript
// Listen for API ready event
document.addEventListener('apiReady', function(event) {
    const api = event.detail.api;
    console.log('API is ready:', api);
});

// Use API after it's ready
document.addEventListener('apiReady', async function(event) {
    const api = event.detail.api;
    const products = await api.product.getProducts();
    // Render products...
});
```

## 📝 Lưu ý

1. **Thứ tự load**: API clients phải được load theo đúng thứ tự dependency
2. **Error handling**: Luôn sử dụng try-catch khi gọi API
3. **Loading states**: Sử dụng loading indicators để cải thiện UX
4. **Cache**: Sử dụng cache cho các dữ liệu ít thay đổi
5. **Retry**: Sử dụng retry mechanism cho các request quan trọng

## 🎯 Lợi ích

- **Tổ chức code tốt**: Phân chia rõ ràng các chức năng API
- **Tái sử dụng**: Có thể sử dụng ở nhiều trang khác nhau
- **Dễ bảo trì**: Code được tổ chức theo module
- **Error handling**: Xử lý lỗi thống nhất
- **Performance**: Cache và retry mechanism
- **UX**: Loading states và notifications
