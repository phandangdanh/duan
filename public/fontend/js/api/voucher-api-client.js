/**
 * API Client cho voucher
 * Xử lý tất cả các API calls liên quan đến voucher
 */

class VoucherAPIClient {
    constructor() {
        this.baseUrl = '/api/vouchers';
        this.timeout = 10000;
    }

    /**
     * Lấy danh sách tất cả voucher
     */
    async getAllVouchers() {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/`);
            return response.data;
        } catch (error) {
            console.error('Error loading vouchers:', error);
            throw error;
        }
    }

    /**
     * Lấy chi tiết voucher theo ID
     */
    async getVoucherById(id) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/${id}`);
            return response.data;
        } catch (error) {
            console.error('Error loading voucher details:', error);
            throw error;
        }
    }

    /**
     * Kiểm tra voucher theo mã
     */
    async checkVoucher(voucherCode, totalAmount = 0) {
        try {
            console.log('VoucherAPIClient checking voucher:', {
                ma_voucher: voucherCode,
                total_amount: totalAmount
            });

            // Lấy danh sách voucher từ API
            const response = await this.makeRequest(`${this.baseUrl}/`);
            console.log('VoucherAPIClient API response:', response);

            if (response.status_code === 200 && response.data) {
                // Tìm voucher theo mã
                const voucher = response.data.find(v => v.ma_voucher === voucherCode);
                
                if (!voucher) {
                    return {
                        success: false,
                        message: 'Mã voucher không tồn tại!'
                    };
                }

                // Kiểm tra trạng thái voucher
                if (!voucher.trang_thai) {
                    return {
                        success: false,
                        message: 'Voucher đã bị tạm dừng!'
                    };
                }

                // Kiểm tra ngày hết hạn
                const now = new Date();
                const startDate = new Date(voucher.ngay_bat_dau);
                const endDate = new Date(voucher.ngay_ket_thuc);

                if (now < startDate) {
                    return {
                        success: false,
                        message: 'Voucher chưa đến thời gian sử dụng!'
                    };
                }

                if (now > endDate) {
                    return {
                        success: false,
                        message: 'Voucher đã hết hạn!'
                    };
                }

                // Kiểm tra số lượng còn lại
                if (voucher.so_luong <= 0) {
                    return {
                        success: false,
                        message: 'Voucher đã hết lượt sử dụng!'
                    };
                }

                // Kiểm tra giá trị tối thiểu
                if (voucher.gia_tri_toi_thieu && totalAmount < voucher.gia_tri_toi_thieu) {
                    return {
                        success: false,
                        message: `Đơn hàng phải có giá trị tối thiểu ${voucher.gia_tri_toi_thieu.toLocaleString()}₫`
                    };
                }

                // Tính toán giảm giá
                let discountAmount = 0;
                if (voucher.loai_giam_gia === 'phan_tram') {
                    discountAmount = (totalAmount * voucher.gia_tri) / 100;
                    if (voucher.gia_tri_toi_da && discountAmount > voucher.gia_tri_toi_da) {
                        discountAmount = voucher.gia_tri_toi_da;
                    }
                } else {
                    discountAmount = voucher.gia_tri;
                }

                const finalAmount = Math.max(0, totalAmount - discountAmount);

                return {
                    success: true,
                    voucher: voucher,
                    discount: discountAmount,
                    final_total: finalAmount
                };
            } else {
                return {
                    success: false,
                    message: 'Không thể tải danh sách voucher!'
                };
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
     * Lấy voucher có thể sử dụng
     */
    async getUsableVouchers() {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/?usable=true`);
            return response.data;
        } catch (error) {
            console.error('Error loading usable vouchers:', error);
            throw error;
        }
    }

    /**
     * Tìm kiếm voucher theo mã
     */
    async searchVouchers(keyword) {
        try {
            const response = await this.makeRequest(`${this.baseUrl}/?search=${encodeURIComponent(keyword)}`);
            return response.data;
        } catch (error) {
            console.error('Error searching vouchers:', error);
            throw error;
        }
    }

    /**
     * Thực hiện HTTP request
     */
    async makeRequest(url, options = {}) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.timeout);

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                signal: controller.signal,
                ...options
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            clearTimeout(timeoutId);
            
            if (error.name === 'AbortError') {
                throw new Error('Request timeout');
            }
            
            throw error;
        }
    }
}

// Export để sử dụng trong các file khác
window.VoucherAPIClient = VoucherAPIClient;
