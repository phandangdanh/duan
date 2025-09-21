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
            
            <div class="col-md-6 mb-3">
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
            </div>
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
                      onclick="checkVoucher()">
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
          
          <div class="cart-items">
            @foreach($cartItems as $item)
              <div class="cart-item">
                <div class="d-flex">
                  @php
                    // Sử dụng cùng logic như giỏ hàng
                    $imageUrl = $item['product']->hinhanh->first()->url ?? 'backend/img/p1.jpg';
                  @endphp
                  <img src="{{ asset($imageUrl) }}" 
                       class="me-3" width="60" height="60" style="object-fit: cover; border-radius: 5px;"
                       onerror="this.src='{{ asset('backend/images/no-image.png') }}'">
                  <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $item['product']->tensp }}</h6>
                    @if($item['variant_name'])
                      <small class="text-muted">{{ $item['variant_name'] }}</small>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mt-2">
                      <span class="text-muted">x{{ $item['quantity'] }}</span>
                      <span class="fw-bold">{{ number_format($item['total_price'], 0, ',', '.') }}₫</span>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          
          <hr>
          
          <div class="d-flex justify-content-between mb-2">
            <span>Tạm tính:</span>
            <span id="subtotal">{{ number_format($total, 0, ',', '.') }}₫</span>
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
            <strong class="text-danger" id="final-total">{{ number_format($total, 0, ',', '.') }}₫</strong>
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
<script>
// Xử lý chọn phương thức thanh toán
document.querySelectorAll('.payment-method').forEach(method => {
  method.addEventListener('click', function() {
    // Bỏ chọn tất cả
    document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
    // Chọn phương thức được click
    this.classList.add('selected');
    // Chọn radio button
    this.querySelector('input[type="radio"]').checked = true;
  });
});

// Xử lý chọn tỉnh/thành phố
document.getElementById('province').addEventListener('change', function() {
  const provinceId = this.value;
  const districtSelect = document.getElementById('district');
  const wardSelect = document.getElementById('ward');
  
  // Reset districts và wards
  districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
  wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
  
  if (provinceId) {
    // Show loading
    districtSelect.innerHTML = '<option value="">Đang tải...</option>';
    wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
    
    fetch(`/api/locations/districts?province_code=${provinceId}`)
      .then(response => response.json())
      .then(data => {
        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        if (data.data) {
          data.data.forEach(district => {
            const option = document.createElement('option');
            option.value = district.code;
            option.textContent = district.name;
            districtSelect.appendChild(option);
          });
        }
      })
      .catch(error => {
        console.error('Error loading districts:', error);
        districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
      });
  }
});

// Xử lý chọn quận/huyện
document.getElementById('district').addEventListener('change', function() {
  const districtId = this.value;
  const wardSelect = document.getElementById('ward');
  
  // Reset wards
  wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
  
  if (districtId) {
    // Show loading
    wardSelect.innerHTML = '<option value="">Đang tải...</option>';
    
    fetch(`/api/locations/wards?district_code=${districtId}`)
      .then(response => response.json())
      .then(data => {
        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        if (data.data) {
          data.data.forEach(ward => {
            const option = document.createElement('option');
            option.value = ward.code;
            option.textContent = ward.name;
            wardSelect.appendChild(option);
          });
        }
      })
      .catch(error => {
        console.error('Error loading wards:', error);
        wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
      });
  }
});

// Kiểm tra voucher
function checkVoucher() {
  const voucherCode = document.getElementById('voucher_code').value;
  const totalAmount = {{ $totalPrice ?? 0 }};
  
  if (!voucherCode) {
    showNotification('Vui lòng nhập mã voucher!', 'error');
    return;
  }
  
  // Show loading
  const applyBtn = document.querySelector('button[onclick="checkVoucher()"]');
  const originalText = applyBtn.innerHTML;
  applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra...';
  applyBtn.disabled = true;
  
  fetch('/api/vouchers/check', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      ma_voucher: voucherCode,
      total_amount: totalAmount
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.status_code === 200) {
      // Voucher hợp lệ
      showVoucherResult(data.data, true);
      showNotification('Áp dụng voucher thành công!', 'success');
    } else {
      // Voucher không hợp lệ
      showVoucherResult(null, false, data.message);
      showNotification(data.message || 'Mã voucher không hợp lệ', 'error');
    }
  })
  .catch(error => {
    console.error('Error checking voucher:', error);
    showNotification('Có lỗi xảy ra khi kiểm tra voucher', 'error');
  })
  .finally(() => {
    // Reset button
    applyBtn.innerHTML = originalText;
    applyBtn.disabled = false;
  });
}

// Hiển thị kết quả voucher
function showVoucherResult(voucherData, isValid, errorMessage = '') {
  const resultDiv = document.getElementById('voucher-result');
  
  if (isValid && voucherData) {
    resultDiv.innerHTML = `
      <div class="alert alert-success">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <strong>✓ ${voucherData.ten_voucher}</strong><br>
            <small>Mã: ${voucherData.ma_voucher}</small>
          </div>
          <div class="text-end">
            <div class="text-success fw-bold">-${formatPrice(voucherData.discount_amount)}₫</div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVoucher()">
              <i class="fas fa-times"></i> Xóa
            </button>
          </div>
        </div>
      </div>
    `;
    
    // Cập nhật tổng tiền
    updateTotalWithVoucher(voucherData.final_amount);
    
    // Lưu voucher vào session
    sessionStorage.setItem('applied_voucher', JSON.stringify(voucherData));
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
  
  // Cập nhật lại tổng tiền gốc
  updateTotalWithVoucher({{ $totalPrice ?? 0 }});
  showNotification('Đã xóa voucher', 'info');
}

// Cập nhật tổng tiền với voucher
function updateTotalWithVoucher(finalAmount) {
  const totalElement = document.querySelector('.total-amount');
  if (totalElement) {
    totalElement.textContent = formatPrice(finalAmount) + '₫';
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

// Xử lý đặt hàng
function processOrder() {
  // Validate form
  const form = document.getElementById('checkout-form');
  const formData = new FormData(form);
  
  // Thêm thông tin địa chỉ
  const province = document.getElementById('province').selectedOptions[0]?.text;
  const district = document.getElementById('district').selectedOptions[0]?.text;
  const ward = document.getElementById('ward').selectedOptions[0]?.text;
  const addressDetail = document.getElementById('diachigiaohang').value;
  
  const fullAddress = `${addressDetail}, ${ward}, ${district}, ${province}`;
  formData.append('diachigiaohang', fullAddress);
  
  // Thêm phương thức thanh toán
  const paymentMethod = document.querySelector('input[name="phuongthucthanhtoan"]:checked').value;
  formData.append('phuongthucthanhtoan', paymentMethod);
  
  // Thêm voucher
  const voucherCode = document.getElementById('voucher_code').value;
  if (voucherCode) {
    formData.append('voucher_code', voucherCode);
  }
  
  // Gửi request
  fetch('/checkout/process', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification(data.message, 'success');
      
      if (data.payment_url) {
        // Chuyển hướng đến cổng thanh toán
        window.location.href = data.payment_url;
      } else {
        // Chuyển hướng đến trang thành công
        window.location.href = data.redirect_url;
      }
    } else {
      showNotification(data.message, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Có lỗi xảy ra khi đặt hàng!', 'error');
  });
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
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
  
  document.body.appendChild(toast);
  
  // Tự động xóa sau 5 giây
  setTimeout(() => {
    if (toast.parentElement) {
      toast.remove();
    }
  }, 5000);
}
</script>
@endsection
