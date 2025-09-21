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
      @if(!empty($cartItems))
        <button class="btn-clear-all" onclick="clearCart()">
          <i class="fas fa-trash me-2"></i>Xóa tất cả
        </button>
      @endif
    </div>

    @if(empty($cartItems))
      <div class="cart-empty">
        <i class="fas fa-shopping-cart"></i>
        <h4>Giỏ hàng trống</h4>
        <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
        <a href="{{ route('products') }}" class="btn btn-primary">
          <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
        </a>
      </div>
    @else
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
            <tr>
              <th>Sản phẩm</th>
              <th>Giá</th>
              <th>Số lượng</th>
              <th>Tạm tính</th>
              <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cartItems as $item)
              <tr data-product-id="{{ $item['product']->id }}" data-variant-id="{{ $item['variant_id'] }}">
                <td>
                  <div class="d-flex align-items-center">
                    <img src="{{ asset($item['product']->hinhanh->first()->url ?? 'backend/img/p1.jpg') }}" 
                         class="cart-item-image me-3" 
                         alt="{{ $item['product']->tenSP }}" 
                         onerror="this.src='{{ asset('backend/img/p1.jpg') }}'" />
                    <div>
                      <h6 class="mb-1">{{ $item['product']->tenSP }}</h6>
                      @if($item['variant_name'])
                        <small class="text-muted">{{ $item['variant_name'] }}</small>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  <span class="price">{{ number_format($item['price'], 0, ',', '.') }}₫</span>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary btn-sm" 
                            onclick="updateQuantity({{ $item['product']->id }}, {{ $item['variant_id'] }}, {{ $item['quantity'] - 1 }})">
                      <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" 
                           value="{{ $item['quantity'] }}" 
                           min="1" 
                           max="{{ $item['max_stock'] }}"
                           class="form-control quantity-input mx-2 text-center"
                           onchange="updateQuantity({{ $item['product']->id }}, {{ $item['variant_id'] }}, this.value)" />
                    <button class="btn btn-outline-secondary btn-sm" 
                            onclick="updateQuantity({{ $item['product']->id }}, {{ $item['variant_id'] }}, {{ $item['quantity'] + 1 }})">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </td>
                <td>
                  <span class="item-total">{{ number_format($item['total_price'], 0, ',', '.') }}₫</span>
                </td>
                <td>
                  <button class="btn btn-danger btn-sm"
                          onclick="removeFromCart({{ $item['product']->id }}, {{ $item['variant_id'] }})">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
          </tr>
            @endforeach
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
                <span id="cart-total">{{ number_format($total, 0, ',', '.') }}₫</span>
              </div>
              <hr>
              <div class="d-flex justify-content-between">
                <strong>Tổng cộng:</strong>
                <strong class="text-danger" id="cart-final-total">{{ number_format($total, 0, ',', '.') }}₫</strong>
              </div>
              <a href="{{ route('checkout') }}" class="btn btn-success w-100 mt-3">
                <i class="fas fa-credit-card me-2"></i>Thanh toán
              </a>
            </div>
          </div>
    </div>
    </div>
    @endif
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
<script>
// Cập nhật số lượng sản phẩm
function updateQuantity(productId, variantId, quantity) {
  console.log('updateQuantity called:', {productId, variantId, quantity});
  
  if (quantity < 0) return;
  
  // Nếu quantity = 0, xóa sản phẩm ngay lập tức
  if (quantity === 0) {
    removeFromCart(productId, variantId);
    return;
  }
  
  fetch('/cart/update', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      product_id: productId,
      variant_id: variantId,
      quantity: quantity
    })
  })
  .then(response => response.json())
  .then(data => {
    console.log('updateQuantity response:', data);
    if (data.success) {
      // Cập nhật giao diện
      updateCartUI(data);
      updateQuantityButtons(productId, variantId, quantity);
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

// Biến lưu thông tin sản phẩm cần xóa
let deleteProductId = null;
let deleteVariantId = null;

// Xóa sản phẩm khỏi giỏ hàng
function removeFromCart(productId, variantId) {
  deleteProductId = productId;
  deleteVariantId = variantId;
  document.getElementById('deleteModalMessage').textContent = 'Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?';
  showDeleteModal();
}

// Xóa toàn bộ giỏ hàng
function clearCart() {
  deleteProductId = null;
  deleteVariantId = null;
  document.getElementById('deleteModalMessage').textContent = 'Bạn có chắc muốn xóa toàn bộ giỏ hàng?';
  showDeleteModal();
}

// Cập nhật trạng thái nút số lượng
function updateQuantityButtons(productId, variantId, quantity) {
  console.log('updateQuantityButtons called:', {productId, variantId, quantity});
  
  const row = document.querySelector(`tr[data-product-id="${productId}"][data-variant-id="${variantId}"]`);
  if (!row) return;
  
  const minusBtn = row.querySelector('button[onclick*="updateQuantity"][onclick*="quantity - 1"]');
  const plusBtn = row.querySelector('button[onclick*="updateQuantity"][onclick*="quantity + 1"]');
  const quantityInput = row.querySelector('input[type="number"]');
  
  if (minusBtn) {
    // Cho phép trừ xuống 0 để xóa sản phẩm
    minusBtn.disabled = false;
    minusBtn.classList.remove('disabled');
    minusBtn.style.opacity = '1';
  }
  
  if (plusBtn) {
    // Enable plus button (có thể disable nếu hết stock)
    plusBtn.disabled = false;
    plusBtn.classList.remove('disabled');
    plusBtn.style.opacity = '1';
  }
  
  if (quantityInput) {
    quantityInput.value = quantity;
  }
}

// Cập nhật giao diện giỏ hàng
function updateCartUI(data) {
  console.log('updateCartUI called with:', data);
  
  try {
    // Cập nhật tổng tiền
    if (data.cart_total !== undefined) {
      const cartTotalElement = document.getElementById('cart-total');
      const cartFinalTotalElement = document.getElementById('cart-final-total');
      
      if (cartTotalElement) {
        cartTotalElement.textContent = formatPrice(data.cart_total);
      }
      if (cartFinalTotalElement) {
        cartFinalTotalElement.textContent = formatPrice(data.cart_total);
      }
    }
    
    // Cập nhật số lượng giỏ hàng trong header (nếu có)
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement && data.cart_count !== undefined) {
      cartCountElement.textContent = data.cart_count;
    }
  } catch (error) {
    console.error('Error in updateCartUI:', error);
  }
}

// Format giá tiền
function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price) + '₫';
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
    // Xóa sản phẩm cụ thể
    performDelete();
  } else {
    // Xóa toàn bộ giỏ hàng
    performClearCart();
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
  const quantityRows = document.querySelectorAll('tr[data-product-id]');
  quantityRows.forEach(row => {
    const productId = row.getAttribute('data-product-id');
    const variantId = row.getAttribute('data-variant-id');
    const quantityInput = row.querySelector('input[type="number"]');
    if (quantityInput) {
      const quantity = parseInt(quantityInput.value);
      updateQuantityButtons(productId, variantId, quantity);
    }
  });
});

// Function hiển thị thông báo
function showNotification(message, type = 'info') {
  // Tạo toast notification
  const toast = document.createElement('div');
  toast.className = `toast-notification toast-${type}`;
  toast.innerHTML = `
    <div class="toast-content">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
      <span>${message}</span>
  </div>
  `;
  
  // Thêm CSS cho toast
  toast.style.cssText = `
    position: fixed;
    top: 100px;
    right: 20px;
    background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
    color: white;
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
  
  // Tự động xóa sau 3 giây
  setTimeout(() => {
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => {
      if (toast.parentElement) {
        toast.remove();
      }
    }, 300);
  }, 3000);
}
</script>
@endsection


