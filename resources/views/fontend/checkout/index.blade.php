@extends('fontend.layouts.app')

@section('title', 'Thanh toán')

@section('css')
  <link rel="stylesheet" href="{{ asset('fontend/trangchu.css') }}">
  <style>
    .checkout-container {
      max-width: 1200px;
      margin: 0 auto;
    }
    .checkout-step {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
    }
    .checkout-step.active {
      background: #e3f2fd;
      border: 2px solid #2196f3;
    }
    .checkout-step.completed {
      background: #e8f5e8;
      border: 2px solid #4caf50;
    }
    .form-control:focus {
      border-color: #2196f3;
      box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
    }
    .payment-method {
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 10px;
      cursor: pointer;
      transition: all 0.3s;
    }
    .payment-method:hover {
      border-color: #2196f3;
    }
    .payment-method.selected {
      border-color: #2196f3;
      background: #e3f2fd;
    }
    .order-summary {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
      position: sticky;
      top: 20px;
    }
    .cart-item {
      border-bottom: 1px solid #e0e0e0;
      padding: 10px 0;
    }
    .cart-item:last-child {
      border-bottom: none;
    }
  </style>
@endsection

@section('content')
<div class="checkout-container py-5">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <!-- Thông tin giao hàng -->
        <div class="checkout-step active" id="shipping-info">
          <h4 class="mb-4">
            <i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng
          </h4>
          
          <form id="checkout-form">
            @csrf
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="hoten" class="form-label">Họ và tên *</label>
                <input type="text" class="form-control" id="hoten" name="hoten" 
                       value="{{ $userInfo['name'] ?? '' }}" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="{{ $userInfo['email'] ?? '' }}" required>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="sodienthoai" class="form-label">Số điện thoại *</label>
                <input type="tel" class="form-control" id="sodienthoai" name="sodienthoai" 
                       value="{{ $userInfo['phone'] ?? '' }}" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="province" class="form-label">Tỉnh/Thành phố *</label>
                <select class="form-control" id="province" name="province" required>
                  <option value="">Chọn tỉnh/thành phố</option>
                  @foreach($provinces as $province)
                    <option value="{{ $province->code }}">{{ $province->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="district" class="form-label">Quận/Huyện *</label>
                <select class="form-control" id="district" name="district" required>
                  <option value="">Chọn quận/huyện</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="ward" class="form-label">Phường/Xã *</label>
                <select class="form-control" id="ward" name="ward" required>
                  <option value="">Chọn phường/xã</option>
                </select>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="diachigiaohang" class="form-label">Địa chỉ chi tiết *</label>
              <textarea class="form-control" id="diachigiaohang" name="diachigiaohang" 
                        rows="3" placeholder="Số nhà, tên đường, tòa nhà..." required>{{ $userInfo['address'] ?? '' }}</textarea>
            </div>
            
            <div class="mb-3">
              <label for="ghichu" class="form-label">Ghi chú</label>
              <textarea class="form-control" id="ghichu" name="ghichu" 
                        rows="2" placeholder="Ghi chú thêm cho đơn hàng..."></textarea>
            </div>
          </form>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="checkout-step" id="payment-method">
          <h4 class="mb-4">
            <i class="fas fa-credit-card me-2"></i>Phương thức thanh toán
          </h4>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="payment-method" data-method="cod">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="phuongthucthanhtoan" 
                         id="cod" value="cod" checked>
                  <label class="form-check-label" for="cod">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                    <br><small class="text-muted">Bạn sẽ thanh toán cho shipper khi nhận hàng</small>
                  </label>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 mb-3">
              <div class="payment-method" data-method="banking">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="phuongthucthanhtoan" 
                         id="banking" value="banking">
                  <label class="form-check-label" for="banking">
                    <i class="fas fa-university me-2"></i>
                    <strong>Chuyển khoản ngân hàng</strong>
                    <br><small class="text-muted">Thanh toán qua chuyển khoản</small>
                  </label>
                </div>
              </div>
            </div>
            
            {{-- <div class="col-md-6 mb-3">
              <div class="payment-method" data-method="momo">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="phuongthucthanhtoan" 
                         id="momo" value="momo">
                  <label class="form-check-label" for="momo">
                    <i class="fas fa-mobile-alt me-2"></i>
                    <strong>Ví MoMo</strong>
                    <br><small class="text-muted">Thanh toán qua ví MoMo</small>
                  </label>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 mb-3">
              <div class="payment-method" data-method="zalopay">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="phuongthucthanhtoan" 
                         id="zalopay" value="zalopay">
                  <label class="form-check-label" for="zalopay">
                    <i class="fas fa-qrcode me-2"></i>
                    <strong>ZaloPay</strong>
                    <br><small class="text-muted">Thanh toán qua ZaloPay</small>
                  </label>
                </div>
              </div>
            </div> --}}
          </div>
        </div>

        <!-- Mã giảm giá -->
        <div class="checkout-step" id="voucher">
          <h4 class="mb-4">
            <i class="fas fa-ticket-alt me-2"></i>Mã giảm giá
          </h4>
          
          <div class="row">
            <div class="col-md-8">
              <input type="text" class="form-control" id="voucher_code" 
                     placeholder="Nhập mã giảm giá...">
            </div>
            <div class="col-md-4">
              <button type="button" class="btn btn-outline-primary w-100" 
                      onclick="checkVoucherFromPage()">
                Áp dụng
              </button>
            </div>
          </div>
          
          <div id="voucher-result" class="mt-3" style="display: none;">
            <!-- Kết quả voucher sẽ hiển thị ở đây -->
          </div>
        </div>
      </div>

      <!-- Tóm tắt đơn hàng -->
      <div class="col-md-4">
        <div class="order-summary">
          <h4 class="mb-4">
            <i class="fas fa-shopping-cart me-2"></i>Tóm tắt đơn hàng
          </h4>
          
          <div class="cart-items" id="checkout-cart-items">
            <!-- Cart items will be loaded from localStorage -->
            <div class="text-center py-4">
              <i class="fas fa-spinner fa-spin"></i> Đang tải giỏ hàng...
            </div>
          </div>
          
          <hr>
          
          <div class="d-flex justify-content-between mb-2">
            <span>Tạm tính:</span>
            <span id="subtotal">0₫</span>
          </div>
          
          <div class="d-flex justify-content-between mb-2" id="voucher-discount" style="display: none;">
            <span>Giảm giá:</span>
            <span class="text-success" id="discount-amount">0₫</span>
          </div>
          
          <div class="d-flex justify-content-between mb-2">
            <span>Phí vận chuyển:</span>
            <span class="text-success">Miễn phí</span>
          </div>
          
          <hr>
          
          <div class="d-flex justify-content-between mb-4">
            <strong>Tổng cộng:</strong>
            <strong class="text-danger" id="final-total">0₫</strong>
          </div>
          
          <button type="button" class="btn btn-success w-100 btn-lg" onclick="processOrder()">
            <i class="fas fa-credit-card me-2"></i>Đặt hàng
          </button>
          
          <div class="text-center mt-3">
            <small class="text-muted">
              Bằng cách đặt hàng, bạn đồng ý với 
              <a href="#" class="text-primary">Điều khoản sử dụng</a>
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="{{ asset('fontend/js/checkout/checkout-api.js') }}"></script>
<script>

// Kiểm tra voucher
async function checkVoucherFromPage() {
  console.log('=== CHECK VOUCHER FUNCTION CALLED ===');
  console.log('Function checkVoucherFromPage is being executed');
  
  const voucherInput = document.getElementById('voucher_code');
  console.log('Voucher input element:', voucherInput);
  
  if (!voucherInput) {
    console.log('ERROR: Voucher input element not found');
    showNotification('Không tìm thấy ô nhập mã voucher!', 'error');
    return;
  }
  
  const voucherCode = voucherInput.value;
  console.log('Voucher code from input:', voucherCode);
  
  if (!voucherCode) {
    console.log('ERROR: No voucher code entered');
    showNotification('Vui lòng nhập mã voucher!', 'error');
    return;
  }
  
  console.log('Proceeding with voucher check for:', voucherCode);
  
  // Show loading - tìm button bằng cách khác
  let applyBtn = document.querySelector('button[onclick="checkVoucherFromPage()"]');
  console.log('Apply button found by onclick:', applyBtn);
  
  if (!applyBtn) {
    // Thử tìm button bằng text content
    const buttons = document.querySelectorAll('button');
    applyBtn = Array.from(buttons).find(btn => btn.textContent.includes('Áp dụng'));
    console.log('Apply button found by text:', applyBtn);
  }
  
  if (!applyBtn) {
    console.log('ERROR: Apply button not found');
    showNotification('Không tìm thấy nút áp dụng!', 'error');
    return;
  }
  
  const originalText = applyBtn.innerHTML;
  applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra...';
  applyBtn.disabled = true;
  
  try {
    let data;
    
    // Check if checkoutManager is available
    if (window.checkoutManager) {
      // Use checkoutManager to check voucher directly
      data = await window.checkoutManager.checkVoucher(voucherCode);
    } else {
      // Fallback: call API directly
      console.log('CheckoutManager not ready, using direct API call');
      const totalAmount = 0; // Default total
      
      const response = await fetch('/api/vouchers/', {
        method: 'GET',
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const apiResponse = await response.json();
      console.log('Direct API response:', apiResponse);
      
      if (apiResponse.status_code === 200 && apiResponse.data) {
        // Tìm voucher theo mã
        const voucher = apiResponse.data.find(v => v.ma_voucher === voucherCode);
        
        if (!voucher) {
          data = {
            success: false,
            message: 'Mã voucher không tồn tại!'
          };
        } else {
          // Kiểm tra các điều kiện voucher
          if (!voucher.trang_thai) {
            data = {
              success: false,
              message: 'Voucher đã bị tạm dừng!'
            };
          } else {
            // Tính toán giảm giá
            let discountAmount = 0;
            if (voucher.loai_giam_gia === 'phan_tram') {
              discountAmount = (totalAmount * voucher.gia_tri) / 100;
            } else {
              discountAmount = voucher.gia_tri;
            }
            
            data = {
              success: true,
              voucher: voucher,
              discount: discountAmount,
              final_total: Math.max(0, totalAmount - discountAmount)
            };
          }
        }
      } else {
        data = {
          success: false,
          message: 'Không thể tải danh sách voucher!'
        };
      }
    }
    
    console.log('Voucher check result:', data);
    
    if (data.success) {
      console.log('SUCCESS: Voucher is valid');
      console.log('Calling showVoucherResult with:', data.voucher);
      console.log('Calling showNotification with success message');
      // Voucher hợp lệ
      showVoucherResult(data.voucher, true);
      showNotification('Áp dụng voucher thành công!', 'success');
    } else {
      console.log('ERROR: Voucher is invalid:', data.message);
      console.log('Calling showVoucherResult with error message:', data.message);
      console.log('Calling showNotification with error message');
      // Voucher không hợp lệ
      showVoucherResult(null, false, data.message);
      showNotification(data.message || 'Mã voucher không hợp lệ', 'error');
    }
  } catch (error) {
    console.error('Error checking voucher:', error);
    showNotification('Có lỗi xảy ra khi kiểm tra voucher: ' + error.message, 'error');
  } finally {
    // Reset button
    applyBtn.innerHTML = originalText;
    applyBtn.disabled = false;
  }
}

// Hiển thị kết quả voucher
function showVoucherResult(voucherData, isValid, errorMessage = '') {
  const resultDiv = document.getElementById('voucher-result');
  
  if (isValid && voucherData) {
    // Lấy dữ liệu từ checkoutManager
    const checkoutManager = window.checkoutManager;
    const originalAmount = checkoutManager ? checkoutManager.totalAmount : 0;
    
    // Tính discount amount từ voucher data
    let discountAmount = 0;
    if (voucherData.loai_giam_gia === 'phan_tram') {
      discountAmount = (originalAmount * voucherData.gia_tri) / 100;
      if (voucherData.gia_tri_toi_da && discountAmount > voucherData.gia_tri_toi_da) {
        discountAmount = voucherData.gia_tri_toi_da;
      }
    } else {
      discountAmount = voucherData.gia_tri;
    }
    
    const finalAmount = Math.max(0, originalAmount - discountAmount);
    
    console.log('Voucher calculation:', {
      originalAmount,
      discountAmount,
      finalAmount,
      voucherData
    });
    
    resultDiv.innerHTML = `
      <div class="alert alert-success">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <strong>✓ ${voucherData.ten_voucher || voucherData.name || 'Voucher'}</strong><br>
            <small>Mã: ${voucherData.ma_voucher || voucherData.code}</small>
          </div>
          <div class="text-end">
            <div class="text-success fw-bold">-${formatPrice(discountAmount)}₫</div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVoucher()">
              <i class="fas fa-times"></i> Xóa
            </button>
          </div>
        </div>
      </div>
    `;
    
    // Cập nhật tổng tiền
    updateTotalWithVoucher(finalAmount);
    
    // Cập nhật checkout manager total
    if (window.checkoutManager) {
      window.checkoutManager.totalAmount = finalAmount;
    }
    
    // Lưu voucher vào session
    sessionStorage.setItem('applied_voucher', JSON.stringify({
      ...voucherData,
      discount_amount: discountAmount,
      final_amount: finalAmount
    }));
  } else {
    resultDiv.innerHTML = `
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${errorMessage || 'Mã voucher không hợp lệ'}
      </div>
    `;
  }
  
  resultDiv.style.display = 'block';
}

// Xóa voucher
function removeVoucher() {
  document.getElementById('voucher_code').value = '';
  document.getElementById('voucher-result').style.display = 'none';
  sessionStorage.removeItem('applied_voucher');
  
  // Reset về tổng tiền gốc (không có discount)
  const originalAmount = window.checkoutManager ? window.checkoutManager.originalAmount : 0;
  
  // Ẩn discount section
  const voucherDiscountDiv = document.getElementById('voucher-discount');
  if (voucherDiscountDiv) {
    voucherDiscountDiv.style.display = 'none';
  }
  
  // Cập nhật tổng tiền về giá trị gốc
  const finalTotalElement = document.getElementById('final-total');
  if (finalTotalElement) {
    finalTotalElement.textContent = formatPrice(originalAmount) + '₫';
  }
  
  // Reset checkout manager total
  if (window.checkoutManager) {
    window.checkoutManager.totalAmount = originalAmount;
  }
  
  showNotification('Đã xóa voucher', 'info');
}

// Cập nhật tổng tiền với voucher
function updateTotalWithVoucher(finalAmount) {
  const subtotalElement = document.getElementById('subtotal');
  const finalTotalElement = document.getElementById('final-total');
  const discountElement = document.getElementById('discount-amount');
  const voucherDiscountDiv = document.getElementById('voucher-discount');
  
  if (subtotalElement && finalTotalElement) {
    // Lấy original amount từ checkoutManager hoặc từ subtotal element
    let originalAmount = 0;
    if (window.checkoutManager && window.checkoutManager.originalAmount) {
      originalAmount = window.checkoutManager.originalAmount;
    } else {
      // Fallback: lấy từ subtotal element
      const subtotalText = subtotalElement.textContent.replace(/[^\d]/g, '');
      originalAmount = parseInt(subtotalText) || 0;
    }
    
    const discountAmount = originalAmount - finalAmount;
    
    console.log('Updating total with voucher:', {
      originalAmount,
      finalAmount,
      discountAmount,
      subtotalElement: subtotalElement.textContent
    });
    
    // Hiển thị discount
    if (discountAmount > 0) {
      if (voucherDiscountDiv) {
        voucherDiscountDiv.style.display = 'block';
      }
      if (discountElement) {
        discountElement.textContent = '-' + formatPrice(discountAmount) + '₫';
      }
    } else {
      if (voucherDiscountDiv) {
        voucherDiscountDiv.style.display = 'none';
      }
    }
    
    // Cập nhật tổng tiền cuối cùng
    finalTotalElement.textContent = formatPrice(finalAmount) + '₫';
    
    console.log('Voucher applied successfully:', {
      originalAmount,
      finalAmount,
      discountAmount
    });
  } else {
    console.error('Required elements not found:', {
      subtotalElement: !!subtotalElement,
      finalTotalElement: !!finalTotalElement
    });
  }
}

// Format giá tiền
function formatPrice(amount) {
  return new Intl.NumberFormat('vi-VN').format(amount);
}

// Function cũ để tương thích
function oldCheckVoucher() {
  fetch('/checkout/check-voucher', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      voucher_code: voucherCode
    })
  })
  .then(response => response.json())
  .then(data => {
    const resultDiv = document.getElementById('voucher-result');
    if (data.success) {
      resultDiv.innerHTML = `
        <div class="alert alert-success">
          <i class="fas fa-check-circle me-2"></i>
          Áp dụng voucher thành công! Giảm ${data.discount.toLocaleString()}₫
        </div>
      `;
      resultDiv.style.display = 'block';
      
      // Cập nhật tổng tiền
      updateTotal(data.final_total);
    } else {
      resultDiv.innerHTML = `
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle me-2"></i>
          ${data.message}
        </div>
      `;
      resultDiv.style.display = 'block';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Có lỗi xảy ra khi kiểm tra voucher!', 'error');
  });
}

// Cập nhật tổng tiền
function updateTotal(finalTotal) {
  document.getElementById('final-total').textContent = finalTotal.toLocaleString() + '₫';
}

// Function xử lý đặt hàng
function processOrder() {
  console.log('processOrder called');
  
  // Lấy thông tin form
  const formData = {
    hoten: document.getElementById('hoten').value,
    email: document.getElementById('email').value,
    sodienthoai: document.getElementById('sodienthoai').value,
    diachigiaohang: document.getElementById('diachigiaohang').value,
    phuongthucthanhtoan: document.querySelector('input[name="phuongthucthanhtoan"]:checked').value,
    ghichu: document.getElementById('ghichu').value,
    vouchers: getSelectedVouchers()
  };
  
  // Validate form
  if (!validateForm(formData)) {
    return;
  }
  
  // Lấy cart data
  const cartData = localStorage.getItem('cart_data');
  if (!cartData) {
    showNotification('Giỏ hàng trống!', 'error');
    return;
  }
  
  const cartItems = JSON.parse(cartData);
  if (cartItems.length === 0) {
    showNotification('Giỏ hàng trống!', 'error');
    return;
  }
  
  // Gửi request
  fetch('{{ route("checkout.process") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      ...formData,
      cart_items: cartItems
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Xóa cart
      localStorage.removeItem('cart_data');
      
      // Redirect đến trang success
      window.location.href = `/checkout/success/${data.order_id}`;
    } else {
      showNotification(data.message || 'Có lỗi xảy ra!', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Có lỗi xảy ra khi đặt hàng!', 'error');
  });
}

// Validate form
function validateForm(data) {
  if (!data.hoten.trim()) {
    showNotification('Vui lòng nhập họ tên!', 'error');
    return false;
  }
  if (!data.email.trim()) {
    showNotification('Vui lòng nhập email!', 'error');
    return false;
  }
  if (!data.sodienthoai.trim()) {
    showNotification('Vui lòng nhập số điện thoại!', 'error');
    return false;
  }
  if (!data.diachigiaohang.trim()) {
    showNotification('Vui lòng nhập địa chỉ giao hàng!', 'error');
    return false;
  }
  if (!data.phuongthucthanhtoan) {
    showNotification('Vui lòng chọn phương thức thanh toán!', 'error');
    return false;
  }
  return true;
}

// Lấy vouchers đã chọn
function getSelectedVouchers() {
  const vouchers = [];
  document.querySelectorAll('input[name="vouchers[]"]:checked').forEach(checkbox => {
    vouchers.push({
      id: checkbox.value,
      code: checkbox.dataset.code,
      discount: parseFloat(checkbox.dataset.discount)
    });
  });
  return vouchers;
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
  console.log('showNotification called with:', message, type);
  
  const toast = document.createElement('div');
  toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
  toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
  toast.innerHTML = `
    <div class="d-flex align-items-center">
      <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
      <span>${message}</span>
      <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
    </div>
  `;
  
  console.log('Toast element created:', toast);
  document.body.appendChild(toast);
  console.log('Toast appended to body');
  
  // Tự động xóa sau 5 giây
  setTimeout(() => {
    if (toast.parentElement) {
      toast.remove();
    }
  }, 5000);
}

// Xử lý product_id parameter từ URL
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get('product_id');
  const quantity = urlParams.get('quantity') || 1;
  
  if (productId) {
    console.log('Product ID from URL:', productId);
    console.log('Quantity from URL:', quantity);
    
    // Kiểm tra xem đã có cart data chưa
    const existingCartData = localStorage.getItem('cart_data');
    if (!existingCartData || existingCartData === '[]') {
      console.log('No existing cart data, loading product...');
      
      // Lấy thông tin sản phẩm từ API
      fetch(`/api/products/${productId}`)
        .then(response => response.json())
        .then(productData => {
          console.log('Product data loaded:', productData);
          
          if (productData.status_code === 200) {
            const product = productData.data;
            
            // Lấy hình ảnh chính
            let mainImage = null;
            if (product.hinhanh && product.hinhanh.length > 0) {
              const defaultImage = product.hinhanh.find(img => img.is_default);
              mainImage = defaultImage ? defaultImage.url : product.hinhanh[0].url;
            }
            
            // Tạo cart data cho checkout page
            const checkoutCartData = [{
              product_id: parseInt(productId),
              variant_id: null,
              quantity: parseInt(quantity),
              price: product.base_sale_price || product.base_price || 0,
              product_name: product.tenSP || 'Sản phẩm',
              image: mainImage
            }];
            
            // Lưu vào localStorage
            localStorage.setItem('cart_data', JSON.stringify(checkoutCartData));
            console.log('Cart data saved for checkout:', checkoutCartData);
            
            // Reload trang để hiển thị sản phẩm
            window.location.reload();
          } else {
            console.error('Failed to load product data');
            showNotification('Không thể tải thông tin sản phẩm!', 'error');
          }
        })
        .catch(error => {
          console.error('Error loading product:', error);
          showNotification('Có lỗi xảy ra khi tải sản phẩm!', 'error');
        });
    } else {
      console.log('Cart data already exists:', existingCartData);
    }
  }
});
</script>
@endsection
