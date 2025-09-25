/**
 * Checkout Page Manager
 * Handles checkout functionality using localStorage
 */
class CheckoutManager {
    constructor() {
        this.cartItems = [];
        this.totalAmount = 0;
        this.originalAmount = 0; // Lưu tổng tiền gốc trước khi áp dụng voucher
    }

    /**
     * Initialize checkout manager
     */
    init() {
        console.log('=== INITIALIZING CHECKOUT ===');
        this.loadCartFromLocalStorage();
        this.renderCheckoutCartItems();
        this.loadUserLocationData();
        this.setupEventListeners();
        console.log('Checkout initialized successfully');
    }

    /**
     * Load user location data from localStorage
     */
    loadUserLocationData() {
        const locationData = localStorage.getItem('user_location_data');
        
        if (locationData) {
            try {
                const data = JSON.parse(locationData);
                console.log('Loading user location data from localStorage:', data);
                
                // Fill form fields with saved data
                this.fillLocationForm(data);
            } catch (error) {
                console.error('Error parsing location data:', error);
            }
        } else {
            console.log('No location data found in localStorage, will use API');
        }
    }

    /**
     * Fill location form with data
     */
    fillLocationForm(data) {
        // Fill province
        if (data.province) {
            const provinceSelect = document.getElementById('province');
            if (provinceSelect) {
                // Find and select the province
                const options = provinceSelect.querySelectorAll('option');
                for (let option of options) {
                    if (option.textContent.includes(data.province) || option.value === data.province_code) {
                        option.selected = true;
                        provinceSelect.dispatchEvent(new Event('change'));
                        break;
                    }
                }
            }
        }
        
        // Fill district
        if (data.district) {
            setTimeout(() => {
                const districtSelect = document.getElementById('district');
                if (districtSelect) {
                    const options = districtSelect.querySelectorAll('option');
                    for (let option of options) {
                        if (option.textContent.includes(data.district) || option.value === data.district_code) {
                            option.selected = true;
                            districtSelect.dispatchEvent(new Event('change'));
                            break;
                        }
                    }
                }
            }, 500); // Wait for districts to load
        }
        
        // Fill ward
        if (data.ward) {
            setTimeout(() => {
                const wardSelect = document.getElementById('ward');
                if (wardSelect) {
                    const options = wardSelect.querySelectorAll('option');
                    for (let option of options) {
                        if (option.textContent.includes(data.ward) || option.value === data.ward_code) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            }, 1000); // Wait for wards to load
        }
        
        // Fill address detail
        if (data.address_detail) {
            const addressInput = document.getElementById('diachigiaohang');
            if (addressInput) {
                addressInput.value = data.address_detail;
            }
        }
    }

    /**
     * Save user location data to localStorage
     */
    saveUserLocationData() {
        const province = document.getElementById('province').selectedOptions[0]?.text;
        const provinceCode = document.getElementById('province').value;
        const district = document.getElementById('district').selectedOptions[0]?.text;
        const districtCode = document.getElementById('district').value;
        const ward = document.getElementById('ward').selectedOptions[0]?.text;
        const wardCode = document.getElementById('ward').value;
        const addressDetail = document.getElementById('diachigiaohang').value;
        
        const locationData = {
            province: province,
            province_code: provinceCode,
            district: district,
            district_code: districtCode,
            ward: ward,
            ward_code: wardCode,
            address_detail: addressDetail,
            timestamp: Date.now()
        };
        
        localStorage.setItem('user_location_data', JSON.stringify(locationData));
        console.log('User location data saved:', locationData);
    }

    /**
     * Load cart from localStorage
     */
    loadCartFromLocalStorage() {
        const cartData = localStorage.getItem('cart_data');
        let cartItems = [];
        
        if (cartData) {
            try {
                cartItems = JSON.parse(cartData);
            } catch (error) {
                console.error('Error parsing cart data:', error);
            }
        }
        
        this.cartItems = cartItems;
        console.log('Cart items loaded:', cartItems);
        return cartItems;
    }

    /**
     * Render cart items in checkout
     */
    renderCheckoutCartItems() {
        const cartContainer = document.getElementById('checkout-cart-items');
        
        if (!this.cartItems || this.cartItems.length === 0) {
            cartContainer.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart text-muted"></i>
                    <p class="text-muted mt-2">Giỏ hàng trống</p>
                    <a href="/sanpham" class="btn btn-primary">Tiếp tục mua sắm</a>
                </div>
            `;
            this.updateTotals(0);
            return;
        }
        
        let totalAmount = 0;
        const cartItemsHtml = this.cartItems.map(item => {
            const itemTotal = item.price * item.quantity;
            totalAmount += itemTotal;
            
            const variantInfo = item.variant_id && item.variant_id !== 0 
                ? `<small class="text-muted">${item.color_name || ''} - ${item.size_name || ''}</small>`
                : '<small class="text-muted">Sản phẩm chính</small>';
            
            return `
                <div class="cart-item">
                    <div class="d-flex">
                        <img src="${item.image || '/backend/img/p1.jpg'}" 
                             class="me-3" width="60" height="60" style="object-fit: cover; border-radius: 5px;"
                             onerror="this.src='/backend/images/no-image.png'">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.name || 'Sản phẩm'}</h6>
                            ${variantInfo}
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-muted">x${item.quantity}</span>
                                <span class="fw-bold">${new Intl.NumberFormat('vi-VN').format(itemTotal)}₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        cartContainer.innerHTML = cartItemsHtml;
        this.updateTotals(totalAmount);
        
        console.log('Checkout cart rendered, total:', totalAmount);
    }

    /**
     * Update totals display
     */
    updateTotals(totalAmount) {
        this.totalAmount = totalAmount;
        this.originalAmount = totalAmount; // Lưu giá trị gốc
        
        const subtotalElement = document.getElementById('subtotal');
        const finalTotalElement = document.getElementById('final-total');
        
        if (subtotalElement) {
            subtotalElement.textContent = new Intl.NumberFormat('vi-VN').format(totalAmount) + '₫';
        }
        
        if (finalTotalElement) {
            finalTotalElement.textContent = new Intl.NumberFormat('vi-VN').format(totalAmount) + '₫';
        }
    }

    /**
     * Process order
     */
    async processOrder() {
        try {
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
            
            // Thêm cart data từ localStorage
            formData.append('cart_data', JSON.stringify(this.cartItems));
            
            console.log('Processing order with cart items:', this.cartItems);
            
            // Gửi request
            const response = await fetch('/checkout/process', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const data = await response.json();
            console.log('Order response:', data);
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                
                // Clear cart after successful order
                localStorage.removeItem('cart_data');
                
                // Update cart count
                if (typeof updateCartCount === 'function') {
                    updateCartCount();
                }
                
                if (data.payment_url) {
                    // Chuyển hướng đến cổng thanh toán
                    window.location.href = data.payment_url;
                } else {
                    // Chuyển hướng đến trang thành công
                    window.location.href = data.redirect_url;
                }
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error processing order:', error);
            this.showNotification('Có lỗi xảy ra khi đặt hàng!', 'error');
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Payment method selection
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

        // Province selection
        const provinceSelect = document.getElementById('province');
        if (provinceSelect) {
            provinceSelect.addEventListener('change', this.handleProvinceChange.bind(this));
        }

        // District selection
        const districtSelect = document.getElementById('district');
        if (districtSelect) {
            districtSelect.addEventListener('change', this.handleDistrictChange.bind(this));
        }

        // Ward selection
        const wardSelect = document.getElementById('ward');
        if (wardSelect) {
            wardSelect.addEventListener('change', this.handleWardChange.bind(this));
        }

        // Address detail input
        const addressInput = document.getElementById('diachigiaohang');
        if (addressInput) {
            addressInput.addEventListener('input', this.handleAddressChange.bind(this));
        }
    }

    /**
     * Handle province change
     */
    handleProvinceChange(event) {
        const provinceId = event.target.value;
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
                    
                    // Save location data after loading districts
                    this.saveUserLocationData();
                })
                .catch(error => {
                    console.error('Error loading districts:', error);
                    districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                });
        } else {
            // Save location data when province is cleared
            this.saveUserLocationData();
        }
    }

    /**
     * Handle district change
     */
    handleDistrictChange(event) {
        const districtId = event.target.value;
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
                    
                    // Save location data after loading wards
                    this.saveUserLocationData();
                })
                .catch(error => {
                    console.error('Error loading wards:', error);
                    wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                });
        } else {
            // Save location data when district is cleared
            this.saveUserLocationData();
        }
    }

    /**
     * Handle ward change
     */
    handleWardChange(event) {
        // Save location data when ward changes
        this.saveUserLocationData();
    }

    /**
     * Handle address change
     */
    handleAddressChange(event) {
        // Save location data when address changes
        this.saveUserLocationData();
    }

    /**
     * Check voucher
     */
    async checkVoucher(voucherCode) {
        try {
            console.log('CheckoutManager checking voucher:', {
                voucherCode: voucherCode,
                totalAmount: this.totalAmount
            });
            
            // Use VoucherAPIClient if available
            if (window.API && window.API.voucher) {
                console.log('Using VoucherAPIClient');
                return await window.API.voucher.checkVoucher(voucherCode, this.totalAmount);
            } else {
                // Fallback to direct API call
                console.log('VoucherAPIClient not available, using direct API call');
                const response = await fetch('/api/vouchers/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ma_voucher: voucherCode,
                        total_amount: this.totalAmount
                    })
                });
                
                const apiResponse = await response.json();
                
                // Convert API response format to expected format
                if (apiResponse.status_code === 200) {
                    return {
                        success: true,
                        voucher: apiResponse.data,
                        discount: apiResponse.data.discount_amount,
                        final_total: apiResponse.data.final_amount
                    };
                } else {
                    return {
                        success: false,
                        message: apiResponse.message
                    };
                }
            }
        } catch (error) {
            console.error('Error checking voucher:', error);
            return {
                success: false,
                message: 'Có lỗi xảy ra khi kiểm tra voucher!'
            };
        }
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Remove existing notifications first
        const existingNotifications = document.querySelectorAll('.toast-notification');
        existingNotifications.forEach(notification => {
            if (notification.parentElement) {
                notification.remove();
            }
        });
        
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
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
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing checkout...');
    
    // Prevent duplicate initialization
    if (window.checkoutManager) {
        console.log('CheckoutManager already initialized, skipping...');
        return;
    }
    
    window.checkoutManager = new CheckoutManager();
    window.checkoutManager.init();
    
    // Expose functions globally
    window.processOrder = () => {
        if (window.checkoutManager) {
            window.checkoutManager.processOrder();
        }
    };
    
    window.checkVoucher = async (voucherCode) => {
        console.log('window.checkVoucher called with:', voucherCode);
        
        // Nếu voucherCode undefined, lấy từ input
        if (!voucherCode) {
            const voucherInput = document.getElementById('voucher_code');
            if (voucherInput) {
                voucherCode = voucherInput.value;
                console.log('Voucher code from input:', voucherCode);
            }
        }
        
        if (!voucherCode) {
            return { success: false, message: 'Vui lòng nhập mã voucher!' };
        }
        
        if (window.checkoutManager) {
            return await window.checkoutManager.checkVoucher(voucherCode);
        }
        return { success: false, message: 'Checkout manager not initialized' };
    };
});
