<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'ThriftZone')</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('fontend/style.css') }}">
  <link rel="stylesheet" href="{{ asset('fontend/components.css') }}">
  <link rel="stylesheet" href="{{ asset('fontend/pagination.css') }}">
  @yield('css')
  @stack('styles')
</head>

<body>
  @include('fontend.layouts.partials.navbar')
  @yield('content')
  @include('fontend.layouts.partials.footer')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
  <!-- Cart Count Update Script -->
  <script>
    // Cập nhật số lượng giỏ hàng khi trang load
    document.addEventListener('DOMContentLoaded', function() {
      updateCartCount();
    });

    // Function để cập nhật số lượng giỏ hàng
    function updateCartCount() {
      fetch('/cart/info')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
              cartCountElement.textContent = data.cart_count;
              // Ẩn badge nếu giỏ hàng trống
              if (data.cart_count === 0) {
                cartCountElement.style.display = 'none';
              } else {
                cartCountElement.style.display = 'inline-block';
              }
            }
          }
        })
        .catch(error => {
          console.error('Error updating cart count:', error);
        });
    }

    // Function để thêm vào giỏ hàng (có thể gọi từ các trang khác)
    function addToCart(productId, variantId = null, quantity = 1) {
      return fetch('/cart/add', {
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
        if (data.success) {
          // Cập nhật số lượng giỏ hàng
          updateCartCount();
          
          // Hiển thị thông báo
          showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
          return data;
        } else {
          showNotification(data.message || 'Có lỗi xảy ra!', 'error');
          throw new Error(data.message || 'Có lỗi xảy ra!');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Không hiển thị thông báo lỗi ở đây vì đã hiển thị ở trên
        throw error;
      });
    }

    // Function hiển thị thông báo
    function showNotification(message, type = 'success') {
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
      
      // Tự động xóa sau 3 giây
      setTimeout(() => {
        if (toast.parentElement) {
          toast.remove();
        }
      }, 3000);
    }
  </script>
  
  @yield('js')
  @stack('scripts')
</body>

</html>


