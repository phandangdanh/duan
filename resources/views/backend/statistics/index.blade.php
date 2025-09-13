@extends('backend.layout')

@section('title', 'Thống kê tổng quan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Bảng điều khiển thống kê</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Form chọn khoảng thời gian -->
                <div class="ibox-content" style="border-bottom: 1px solid #e7eaec;">
                    <form id="dateFilterForm" class="form-inline">
                        <div class="form-group">
                            <label for="startDate" class="control-label">Từ ngày:</label>
                            <input type="text" class="form-control" id="startDate" name="start_date" placeholder="dd/mm/yyyy"
                                   value="{{ date('d/m/Y', strtotime(date('Y-m-01'))) }}" style="margin-left: 10px;">
                        </div>
                        <div class="form-group" style="margin-left: 20px;">
                            <label for="endDate" class="control-label">Đến ngày:</label>
                            <input type="text" class="form-control" id="endDate" name="end_date" placeholder="dd/mm/yyyy"
                                   value="{{ date('d/m/Y') }}" style="margin-left: 10px;">
                        </div>
                        <div class="form-group" style="margin-left: 20px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Lọc dữ liệu
                            </button>
                            <button type="button" class="btn btn-default" id="resetFilter" style="margin-left: 10px;">
                                <i class="fa fa-refresh"></i> Đặt lại
                            </button>
                        </div>
                        <div class="form-group" style="margin-left: 20px;">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-days="7">7 ngày</button>
                                <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-days="30">30 ngày</button>
                                <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-days="90">3 tháng</button>
                                <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-days="365">1 năm</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="ibox-content" id="statistics-container">
                    <!-- Thống kê tổng quan -->
                    <div class="row">
                        <!-- Thống kê đơn hàng -->
                        <div class="col-lg-3">
                            <div class="widget style1 navy-bg">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <i class="fa fa-shopping-cart fa-5x"></i>
                                    </div>
                                    <div class="col-xs-8 text-right">
                                        <span> Tổng đơn hàng</span>
                                        <h2 id="stat-total-orders" class="font-bold">{{ number_format($stats['orders']['total_orders']) }}</h2>
                                        <small>
                                            <span class="text-success">
                                                <i class="fa fa-arrow-up"></i> {{ $stats['growth']['order_growth'] }}%
                                            </span>
                                            so với tháng trước
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thống kê doanh thu -->
                        <div class="col-lg-3">
                            <div class="widget style1 lazur-bg">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <i class="fa fa-money fa-5x"></i>
                                    </div>
                                    <div class="col-xs-8 text-right">
                                        <span> Tổng doanh thu</span>
                                        <h2 id="stat-total-revenue" class="font-bold">{{ number_format($stats['revenue']['total_revenue']) }} VNĐ</h2>
                                        <small>
                                            <span class="text-success">
                                                <i class="fa fa-arrow-up"></i> {{ $stats['growth']['revenue_growth'] }}%
                                            </span>
                                            so với tháng trước
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thống kê người dùng -->
                        <div class="col-lg-3">
                            <div class="widget style1 yellow-bg">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <i class="fa fa-users fa-5x"></i>
                                    </div>
                                    <div class="col-xs-8 text-right">
                                        <span> Tổng người dùng</span>
                                        <h2 id="stat-total-users" class="font-bold">{{ number_format($stats['users']['total_users']) }}</h2>
                                        <small>
                                            <span class="text-success">
                                                <i class="fa fa-arrow-up"></i> {{ $stats['growth']['user_growth'] }}%
                                            </span>
                                            so với tháng trước
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thống kê sản phẩm -->
                        <div class="col-lg-3">
                            <div class="widget style1 red-bg">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <i class="fa fa-shopping-bag fa-5x"></i>
                                    </div>
                                    <div class="col-xs-8 text-right">
                                        <span> Tổng sản phẩm</span>
                                        <h2 id="stat-total-products" class="font-bold">{{ number_format($stats['products']['total_products']) }}</h2>
                                        <small>
                                            <span class="text-success">
                                                <i class="fa fa-check"></i> {{ $stats['products']['active_products'] }}
                                            </span>
                                            đang hoạt động
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê chi tiết -->
                    <div class="row">
                        <!-- Thống kê hôm nay -->
                        <div class="col-lg-6">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Thống kê hôm nay</h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 id="stat-today-orders" class="no-margins">{{ number_format($stats['orders']['today_orders']) }}</h3>
                                            <div class="font-bold text-navy">Đơn hàng mới</div>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 id="stat-today-revenue" class="no-margins">{{ number_format($stats['revenue']['today_revenue']) }} VNĐ</h3>
                                            <div class="font-bold text-navy">Doanh thu hôm nay</div>
                                        </div>
                                    </div>
                                    <div class="row m-t-sm">
                                        <div class="col-md-6">
                                            <h3 id="stat-new-users-today" class="no-margins">{{ number_format($stats['users']['new_users_today']) }}</h3>
                                            <div class="font-bold text-navy">Người dùng mới</div>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 id="stat-pending-orders" class="no-margins">{{ number_format($stats['orders']['pending_orders']) }}</h3>
                                            <div class="font-bold text-navy">Đơn hàng chờ xử lý</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thống kê tháng này -->
                        <div class="col-lg-6">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Thống kê tháng này</h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 id="stat-this-month-orders" class="no-margins">{{ number_format($stats['orders']['this_month_orders']) }}</h3>
                                            <div class="font-bold text-navy">Đơn hàng tháng này</div>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 id="stat-this-month-revenue" class="no-margins">{{ number_format($stats['revenue']['this_month_revenue']) }} VNĐ</h3>
                                            <div class="font-bold text-navy">Doanh thu tháng này</div>
                                        </div>
                                    </div>
                                    <div class="row m-t-sm">
                                        <div class="col-md-6">
                                            <h3 class="no-margins">{{ number_format($stats['users']['new_users_this_month']) }}</h3>
                                            <div class="font-bold text-navy">Người dùng mới</div>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 id="stat-delivered-orders" class="no-margins">{{ number_format($stats['orders']['delivered_orders']) }}</h3>
                                            <div class="font-bold text-navy">Đơn hàng đã giao</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu đồ -->
                    <div class="row">
                        <!-- Biểu đồ doanh thu -->
                        <div class="col-lg-8">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Biểu đồ doanh thu 12 tháng gần nhất</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div>
                                        <canvas id="revenueChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thống kê trạng thái đơn hàng -->
                        <div class="col-lg-4">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Trạng thái đơn hàng</h5>
                                </div>
                                <div class="ibox-content">
                                    <div>
                                        <canvas id="orderStatusChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top sản phẩm và khách hàng -->
                    <div class="row">
                        <!-- Top sản phẩm bán chạy -->
                        <div class="col-lg-6">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Top sản phẩm bán chạy</h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sản phẩm</th>
                                                    <th>Số lượng bán</th>
                                                    <th>Doanh thu</th>
                                                </tr>
                                            </thead>
                                            <tbody id="top-products-body">
                                                @forelse($topProducts as $product)
                                                <tr>
                                                    <td>{{ $product->tensanpham }}</td>
                                                    <td><span class="badge badge-primary">{{ number_format($product->total_sold) }}</span></td>
                                                    <td>{{ number_format($product->total_revenue) }} VNĐ</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Chưa có dữ liệu</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top khách hàng -->
                        <div class="col-lg-6">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Top khách hàng mua nhiều nhất</h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Khách hàng</th>
                                                    <th>Số đơn</th>
                                                    <th>Tổng chi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="top-customers-body">
                                                @forelse($topCustomers as $customer)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $customer->hoten ?? 'Khách hàng #' . $customer->id_user }}</strong>
                                                            @if($customer->email)
                                                            <br><small class="text-muted">{{ $customer->email }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td><span class="badge badge-info">{{ $customer->total_orders }}</span></td>
                                                    <td>{{ number_format($customer->total_spent) }} VNĐ</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Chưa có dữ liệu</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let revenueChart, orderStatusChart;
    
    // Khởi tạo biểu đồ
    function initCharts() {
        // Biểu đồ doanh thu
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueChart['labels']),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: @json($revenueChart['data']),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                            }
                        }
                    }
                }
            }
        });

        // Biểu đồ trạng thái đơn hàng
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const orderStatusData = @json($orderStatusStats);
        
        orderStatusChart = new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(orderStatusData).map(key => {
                    const statusMap = {
                        'cho_xac_nhan': 'Chờ xác nhận',
                        'da_xac_nhan': 'Đã xác nhận',
                        'dang_giao': 'Đang giao',
                        'da_giao': 'Đã giao',
                        'da_huy': 'Đã hủy'
                    };
                    return statusMap[key] || key;
                }),
                datasets: [{
                    data: Object.values(orderStatusData),
                    backgroundColor: [
                        '#f39c12', // Chờ xác nhận - cam
                        '#3498db', // Đã xác nhận - xanh dương
                        '#9b59b6', // Đang giao - tím
                        '#27ae60', // Đã giao - xanh lá
                        '#e74c3c'  // Đã hủy - đỏ
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Khởi tạo biểu đồ lần đầu
    initCharts();
    
    // Xử lý form filter
    $('#dateFilterForm').on('submit', function(e) {
        e.preventDefault();
        filterData();
    });
    
    // Helpers cho định dạng ngày dd/mm/yyyy <-> yyyy-mm-dd
    function formatDateToDMY(dateObj) {
        const d = dateObj.getDate().toString().padStart(2, '0');
        const m = (dateObj.getMonth() + 1).toString().padStart(2, '0');
        const y = dateObj.getFullYear();
        return `${d}/${m}/${y}`;
    }

    function parseDMYToYMD(dmy) {
        // expects dd/mm/yyyy
        const parts = (dmy || '').split('/');
        if (parts.length !== 3) return null;
        const [d, m, y] = parts;
        if (!d || !m || !y) return null;
        return `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
    }
    
    // Xử lý nút đặt lại
    $('#resetFilter').on('click', function() {
        $('#startDate').val('{{ date('d/m/Y', strtotime(date('Y-m-01'))) }}');
        $('#endDate').val('{{ date('d/m/Y') }}');
        filterData();
    });
    
    // Xử lý nút quick filter
    $('.quick-filter').on('click', function() {
        const days = parseInt($(this).data('days'));
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(endDate.getDate() - days);
        
        $('#startDate').val(formatDateToDMY(startDate));
        $('#endDate').val(formatDateToDMY(endDate));
        filterData();
    });
    
    // Hàm filter dữ liệu
    function filterData() {
        const startDateDMY = $('#startDate').val();
        const endDateDMY = $('#endDate').val();
        const startDate = parseDMYToYMD(startDateDMY);
        const endDate = parseDMYToYMD(endDateDMY);
        
        if (!startDate || !endDate) {
            alert('Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc!');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            alert('Ngày bắt đầu không được lớn hơn ngày kết thúc!');
            return;
        }
        
        // Hiển thị loading
        showLoading();
        
        // Gọi API để lấy dữ liệu mới
        $.ajax({
            url: '{{ route("admin.statistics.filtered") }}',
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.success) {
                    updateStats(response.stats);
                    updateRevenueChart(response.revenueChart);
                    if (response.topProducts) updateTopProducts(response.topProducts);
                    if (response.topCustomers) updateTopCustomers(response.topCustomers);
                } else {
                    alert('Có lỗi xảy ra khi tải dữ liệu: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi tải dữ liệu!');
            },
            complete: function() {
                hideLoading();
            }
        });
    }
    
    // Cập nhật thống kê
    function updateStats(stats) {
        $('#stat-total-orders').text(Number(stats.orders.total_orders || 0).toLocaleString());
        $('#stat-total-revenue').text(Number(stats.revenue.total_revenue || 0).toLocaleString() + ' VNĐ');
        $('#stat-total-users').text(Number(stats.users.total_users || 0).toLocaleString());
        $('#stat-total-products').text(Number(stats.products.total_products || 0).toLocaleString());

        $('#stat-today-orders').text(Number(stats.orders.today_orders || 0).toLocaleString());
        $('#stat-today-revenue').text(Number(stats.revenue.today_revenue || 0).toLocaleString() + ' VNĐ');
        $('#stat-new-users-today').text(Number(stats.users.new_users_today || 0).toLocaleString());
        $('#stat-pending-orders').text(Number(stats.orders.pending_orders || 0).toLocaleString());
        $('#stat-this-month-orders').text(Number(stats.orders.this_month_orders || 0).toLocaleString());
        $('#stat-this-month-revenue').text(Number(stats.revenue.this_month_revenue || 0).toLocaleString() + ' VNĐ');
        $('#stat-delivered-orders').text(Number(stats.orders.delivered_orders || 0).toLocaleString());
    }
    
    // Cập nhật biểu đồ doanh thu
    function updateRevenueChart(chartData) {
        revenueChart.data.labels = chartData.labels;
        revenueChart.data.datasets[0].data = chartData.data;
        revenueChart.update();
    }

    // Cập nhật top sản phẩm
    function updateTopProducts(products) {
        const $tbody = $('#top-products-body');
        if (!Array.isArray(products) || products.length === 0) {
            $tbody.html('<tr><td colspan="3" class="text-center">Chưa có dữ liệu</td></tr>');
            return;
        }
        const rows = products.map(p => `
            <tr>
                <td>${p.tensanpham}</td>
                <td><span class="badge badge-primary">${Number(p.total_sold || 0).toLocaleString()}</span></td>
                <td>${Number(p.total_revenue || 0).toLocaleString()} VNĐ</td>
            </tr>
        `).join('');
        $tbody.html(rows);
    }

    // Cập nhật top khách hàng
    function updateTopCustomers(customers) {
        const $tbody = $('#top-customers-body');
        if (!Array.isArray(customers) || customers.length === 0) {
            $tbody.html('<tr><td colspan="3" class="text-center">Chưa có dữ liệu</td></tr>');
            return;
        }
        const rows = customers.map(c => `
            <tr>
                <td>
                    <div>
                        <strong>${c.hoten || ('Khách hàng #' + c.id_user)}</strong>
                        ${c.email ? ('<br><small class="text-muted">' + c.email + '</small>') : ''}
                    </div>
                </td>
                <td><span class="badge badge-info">${Number(c.total_orders || 0).toLocaleString()}</span></td>
                <td>${Number(c.total_spent || 0).toLocaleString()} VNĐ</td>
            </tr>
        `).join('');
        $tbody.html(rows);
    }
    
    // Hiển thị loading
    function showLoading() {
        $('#statistics-container').addClass('sk-loading');
        $('#dateFilterForm button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang tải...');
    }
    
    // Ẩn loading
    function hideLoading() {
        $('#statistics-container').removeClass('sk-loading');
        $('#dateFilterForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-search"></i> Lọc dữ liệu');
    }
});
</script>
@endsection
