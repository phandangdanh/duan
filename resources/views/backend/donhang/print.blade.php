<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #{{ $donhang->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .invoice {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background: #f9f9f9;
        }
        .invoice-info .left, .invoice-info .right {
            width: 48%;
        }
        .invoice-info h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 16px;
        }
        .invoice-info p {
            margin: 5px 0;
            color: #666;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background: #f5f5f5;
            font-weight: bold;
            color: #333;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .total-section {
            padding: 20px;
            background: #f9f9f9;
            text-align: right;
        }
        .total-section .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            padding: 5px 0;
        }
        .total-section .total-row.final {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #f0ad4e; color: white; }
        .status-confirmed { background: #5bc0de; color: white; }
        .status-shipping { background: #337ab7; color: white; }
        .status-delivered { background: #5cb85c; color: white; }
        .status-cancelled { background: #d9534f; color: white; }
        .status-returned { background: #6c757d; color: white; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice">
        <!-- Header -->
        <div class="header">
            <?php
                $defaults = [
                    'name' => 'CỬA HÀNG THỜI TRANG',
                    'address' => '123 Đường ABC, Quận XYZ, TP.HCM',
                    'phone' => '0123 456 789',
                    'email' => 'info@cuahang.com',
                ];
                $storeInfoPath = storage_path('app/store_info.json');
                $store = $defaults;
                if (file_exists($storeInfoPath)) {
                    $json = json_decode(file_get_contents($storeInfoPath), true);
                    if (is_array($json)) { $store = array_merge($defaults, $json); }
                }
            ?>
            <h1>{{ $store['name'] }}</h1>
            <p>Địa chỉ: {{ $store['address'] }}</p>
            <p>Điện thoại: {{ $store['phone'] }} | Email: {{ $store['email'] }}</p>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="left">
                <h3>Thông tin đơn hàng</h3>
                <p><strong>Mã đơn hàng:</strong> #{{ $donhang->id }}</p>
                <p><strong>Ngày tạo:</strong> {{ $donhang->ngaytao ? $donhang->ngaytao->format('d/m/Y H:i:s') : 'N/A' }}</p>
                <p><strong>Trạng thái:</strong> 
                    <span class="status-badge status-{{ $donhang->trangthai }}">
                        {{ $donhang->trang_thai_text }}
                    </span>
                </p>
                @if($donhang->nhanvien)
                    <p><strong>Nhân viên xử lý:</strong> {{ $donhang->nhanvien }}</p>
                @endif
            </div>
            <div class="right">
                <h3>Thông tin khách hàng</h3>
                @if($donhang->user)
                    <p><strong>Tên:</strong> {{ $donhang->user->name }}</p>
                    <p><strong>Email:</strong> {{ $donhang->user->email }}</p>
                    @if($donhang->user->phone)
                        <p><strong>Điện thoại:</strong> {{ $donhang->user->phone }}</p>
                    @endif
                    @if($donhang->user->address)
                        <p><strong>Địa chỉ:</strong> {{ $donhang->user->address }}</p>
                    @endif
                @else
                    <p>Không có thông tin khách hàng</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">STT</th>
                    <th width="45%">Tên sản phẩm</th>
                    <th width="15%" class="text-center">Đơn giá</th>
                    <th width="10%" class="text-center">Số lượng</th>
                    <th width="15%" class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donhang->chiTietDonHang as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->tensanpham }}</td>
                        <td class="text-right">{{ $item->dongia_formatted }}</td>
                        <td class="text-center">{{ $item->soluong }}</td>
                        <td class="text-right">{{ $item->thanhtien_formatted }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Không có sản phẩm nào</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f5f5f5; font-weight: bold;">
                    <td colspan="4" class="text-right">TỔNG CỘNG:</td>
                    <td class="text-right">{{ $donhang->tong_tien_formatted }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row final">
                <span>TỔNG TIỀN:</span>
                <span>{{ $donhang->tong_tien_formatted }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Cảm ơn quý khách đã mua hàng!</p>
            <p>Hóa đơn được tạo lúc: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            <i class="fa fa-print"></i> In hóa đơn
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            <i class="fa fa-times"></i> Đóng
        </button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
