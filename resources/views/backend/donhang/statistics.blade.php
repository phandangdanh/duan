@extends('backend.layout')
@section('title', 'Thống kê đơn hàng')
@section('content')

<div class="wrapper wrapper-content">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Thống kê đơn hàng</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('dashboard.index') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('admin.donhang.index') }}">Quản lý đơn hàng</a>
                </li>
                <li class="active">
                    <strong>Thống kê đơn hàng</strong>
                </li>
            </ol>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mt10">
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Tổng đơn hàng</h5>
                    <h2 class="no-margins text-primary">{{ number_format($stats['total_orders']) }}</h2>
                    <small>Tất cả thời gian</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Chờ xác nhận</h5>
                    <h2 class="no-margins text-warning">{{ number_format($stats['pending_orders']) }}</h2>
                    <small>Đơn hàng mới</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Đang giao</h5>
                    <h2 class="no-margins text-info">{{ number_format($stats['shipping_orders']) }}</h2>
                    <small>Đang vận chuyển</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Đã giao</h5>
                    <h2 class="no-margins text-success">{{ number_format($stats['delivered_orders']) }}</h2>
                    <small>Hoàn thành</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu -->
    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Tổng doanh thu</h5>
                    <h2 class="no-margins text-success">{{ number_format($stats['total_revenue'], 0, ',', '.') }} VNĐ</h2>
                    <small>Tất cả thời gian</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Doanh thu hôm nay</h5>
                    <h2 class="no-margins text-info">{{ number_format($stats['today_revenue'], 0, ',', '.') }} VNĐ</h2>
                    <small>{{ date('d/m/Y') }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Doanh thu tháng này</h5>
                    <h2 class="no-margins text-primary">{{ number_format($stats['month_revenue'], 0, ',', '.') }} VNĐ</h2>
                    <small>Tháng {{ date('m/Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Biểu đồ doanh thu</h5>
                    <div class="ibox-tools">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default chart-period-btn" data-period="week">Tuần</button>
                            <button type="button" class="btn btn-sm btn-primary chart-period-btn" data-period="month">Tháng</button>
                            <button type="button" class="btn btn-sm btn-default chart-period-btn" data-period="year">Năm</button>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <canvas id="revenueChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top khách hàng và sản phẩm -->
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Top khách hàng</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên khách hàng</th>
                                    <th>Số đơn hàng</th>
                                    <th>Tổng chi tiêu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $index => $customer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->total_orders }}</td>
                                        <td class="text-success">{{ number_format($customer->total_spent, 0, ',', '.') }} VNĐ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
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
                                    <th>#</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Số lượng bán</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $product->tensanpham }}</td>
                                        <td>{{ $product->total_quantity }}</td>
                                        <td class="text-success">{{ number_format($product->total_revenue, 0, ',', '.') }} VNĐ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Nút Quay lại -->
    <div class="row mt20">
        <div class="col-lg-12 text-center">
            <a href="{{ route('admin.donhang.index') }}" class="btn btn-default btn-lg">
                <i class="fa fa-arrow-left"></i> Quay lại danh sách đơn hàng
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let revenueChart;
    let currentPeriod = 'month';
    
    // Khởi tạo biểu đồ
    initChart();
    
    // Xử lý thay đổi period
    $('.chart-period-btn').on('click', function() {
        $('.chart-period-btn').removeClass('btn-primary').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
        
        currentPeriod = $(this).data('period');
        loadChartData(currentPeriod);
    });
    
    function initChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' VNĐ';
                            }
                        }
                    }
                }
            }
        });
        
        loadChartData(currentPeriod);
    }
    
    function loadChartData(period) {
        $.ajax({
            url: '{{ route("admin.donhang.api.chart.data") }}',
            method: 'GET',
            data: { period: period },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const labels = data.map(item => {
                        const date = new Date(item.date);
                        if (period === 'week') {
                            return date.toLocaleDateString('vi-VN', { weekday: 'short', day: '2-digit', month: '2-digit' });
                        } else if (period === 'month') {
                            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                        } else {
                            return date.toLocaleDateString('vi-VN', { month: '2-digit', year: 'numeric' });
                        }
                    });
                    const revenues = data.map(item => parseFloat(item.total_revenue));
                    
                    revenueChart.data.labels = labels;
                    revenueChart.data.datasets[0].data = revenues;
                    revenueChart.update();
                }
            },
            error: function() {
                console.error('Lỗi khi tải dữ liệu biểu đồ');
            }
        });
    }
});
</script>

@endsection
