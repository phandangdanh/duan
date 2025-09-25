<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .order-info h3 {
            margin: 0 0 15px 0;
            color: #667eea;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
        .order-details {
            margin: 25px 0;
        }
        .order-details h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .product-name {
            font-weight: 500;
            color: #212529;
        }
        .product-quantity {
            color: #6c757d;
            font-size: 14px;
        }
        .product-price {
            font-weight: 600;
            color: #28a745;
        }
        .total-section {
            background-color: #e8f5e8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: #28a745;
            margin: 10px 0;
        }
        .payment-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-info h3 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .payment-info p {
            margin: 5px 0;
            color: #856404;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #667eea;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .contact-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #e3f2fd;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
        }
        .contact-info h4 {
            color: #1976d2;
            margin: 0 0 10px 0;
        }
        .contact-info p {
            margin: 5px 0;
            color: #1976d2;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 20px;
            }
            .btn {
                display: block;
                margin: 10px 0;
                width: 100%;
            }
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-value {
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Đơn hàng đã được xác nhận!</h1>
            <p>Cảm ơn bạn đã mua sắm tại ThriftZone</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Xin chào <strong>{{ $order->hoten }}</strong>,</p>
            
            <p>Cảm ơn bạn đã đặt hàng tại <strong>ThriftZone</strong>! Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>

            <!-- Order Information -->
            <div class="order-info">
                <h3>📋 Thông tin đơn hàng</h3>
                <div class="info-row">
                    <span class="info-label">Mã đơn hàng:</span>
                    <span class="info-value"><strong>#{{ $order->id }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày đặt hàng:</span>
                    <span class="info-value">{{ $orderDate }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên khách hàng:</span>
                    <span class="info-value">{{ $order->hoten }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số điện thoại:</span>
                    <span class="info-value">{{ $order->sodienthoai }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $order->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Địa chỉ giao hàng:</span>
                    <span class="info-value">{{ $order->diachigiaohang }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phương thức thanh toán:</span>
                    <span class="info-value">
                        @if($order->phuongthucthanhtoan === 'cod')
                            💰 Thanh toán khi nhận hàng (COD)
                        @elseif($order->phuongthucthanhtoan === 'bank_transfer')
                            🏦 Chuyển khoản ngân hàng
                        @else
                            {{ ucfirst($order->phuongthucthanhtoan) }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Order Details -->
            @if(!empty($orderDetails))
            <div class="order-details">
                <h3>🛍️ Chi tiết đơn hàng</h3>
                @foreach($orderDetails as $detail)
                <div class="product-item">
                    <div>
                        <div class="product-name">{{ $detail['tensanpham'] }}</div>
                        <div class="product-quantity">Số lượng: {{ $detail['soluong'] }}</div>
                    </div>
                    <div class="product-price">{{ number_format($detail['thanhtien'], 0, ',', '.') }} ₫</div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Total Amount -->
            <div class="total-section">
                <h3>Tổng tiền đơn hàng</h3>
                <div class="total-amount">{{ $totalAmount }}</div>
            </div>

            <!-- Payment Information -->
            @if($order->phuongthucthanhtoan === 'bank_transfer')
            <div class="payment-info">
                <h3>💳 Thông tin thanh toán</h3>
                <p><strong>Phương thức:</strong> Chuyển khoản ngân hàng</p>
                <p><strong>Trạng thái:</strong> Chưa thanh toán</p>
                <p>Vui lòng thực hiện chuyển khoản theo thông tin được cung cấp để hoàn tất đơn hàng.</p>
            </div>
            @elseif($order->phuongthucthanhtoan === 'cod')
            <div class="payment-info">
                <h3>💰 Thông tin thanh toán</h3>
                <p><strong>Phương thức:</strong> Thanh toán khi nhận hàng (COD)</p>
                <p><strong>Trạng thái:</strong> Chờ xác nhận</p>
                <p>Bạn sẽ thanh toán khi nhận được hàng từ nhân viên giao hàng.</p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ $orderUrl }}" class="btn btn-primary">📋 Xem chi tiết đơn hàng</a>
                @if($order->phuongthucthanhtoan === 'bank_transfer')
                <a href="{{ $paymentUrl }}" class="btn btn-success">💳 Thanh toán ngay</a>
                @endif
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                <h4>📞 Hỗ trợ khách hàng</h4>
                <p><strong>Hotline:</strong> 1900 1234</p>
                <p><strong>Email:</strong> support@thriftzone.com</p>
                <p><strong>Thời gian hỗ trợ:</strong> 8:00 - 22:00 (Tất cả các ngày trong tuần)</p>
            </div>

            <p>Nếu bạn có bất kỳ câu hỏi nào về đơn hàng, vui lòng liên hệ với chúng tôi qua thông tin trên.</p>
            
            <p>Trân trọng,<br><strong>Đội ngũ ThriftZone</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>ThriftZone</strong> - Cửa hàng thời trang uy tín</p>
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>© {{ date('Y') }} ThriftZone. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
