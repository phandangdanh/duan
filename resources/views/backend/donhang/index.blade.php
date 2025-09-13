@extends('backend.layout')
@section('title', 'Quản lý đơn hàng')
@section('content')
<link rel="stylesheet" href="{{ asset('backend/css/sanpham-admin.css') }}">
<script src="{{ asset('backend/js/donhang-admin.js') }}" defer></script>
<script>
window.DONHANG_ENDPOINTS = {
  updateTrangThai: "{{ url('/ajax/donhang/update-trangthai') }}",
  updateTrangThaiBulk: "{{ url('/ajax/donhang/update-trangthai-bulk') }}",
  destroy: "{{ url('/ajax/donhang/destroy') }}",
  getInfo: "{{ url('/ajax/donhang/get-info') }}",
  getStats: "{{ url('/ajax/donhang/get-stats') }}"
};
</script>

<div class="wrapper wrapper-content">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Quản lý đơn hàng</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('dashboard.index') }}">Dashboard</a>
                </li>
                <li class="active">
                    <a href="{{ route('admin.donhang.index') }}">
                        <strong>Quản lý đơn hàng</strong>
                    </a>
                </li>
            </ol>
        </div>
    </div>

    <!-- Thống kê đơn hàng -->
    <div class="row mt10">
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Tổng đơn hàng</h5>
                    <h2 class="no-margins text-primary">{{ number_format($stats['total_orders']) }}</h2>
                    <small>Tất cả đơn hàng</small>
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
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Tổng doanh thu</h5>
                    <h2 class="no-margins text-success">{{ number_format($stats['total_revenue'], 0, ',', '.') }} VNĐ</h2>
                    <small>Tất cả thời gian</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Doanh thu hôm nay</h5>
                    <h2 class="no-margins text-info">{{ number_format($stats['today_revenue'], 0, ',', '.') }} VNĐ</h2>
                    <small>{{ date('d/m/Y') }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Doanh thu tháng này</h5>
                    <h2 class="no-margins text-primary">{{ number_format($stats['month_revenue'], 0, ',', '.') }} VNĐ</h2>
                    <small>Tháng {{ date('m/Y') }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5 class="m-b-none">Sử dụng voucher</h5>
                    <h2 class="no-margins text-warning">{{ $stats['voucher_usage_rate'] }}%</h2>
                    <small>{{ $stats['orders_with_voucher'] }}/{{ $stats['total_orders'] }} đơn hàng</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt10">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách đơn hàng</h5>
                    @include('backend.donhang.component.toolbox')
                </div>
                <div class="ibox-content">
                    @include('backend.donhang.component.filter')
                    @include('backend.donhang.component.table')
                                            @if(method_exists($donhangs, 'links'))
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">Tổng đơn hàng: <strong>{{ $donhangs->total() }}</strong></div>
                                <div class="pagination-wrapper">
                                    {{ $donhangs->appends(request()->query())->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
                                </div>
                            </div>
                        @endif
                </div>
            </div>
        </div>
            </div>
    </div>
</div>

<!-- Modal xác nhận cập nhật trạng thái -->
<div class="modal fade" id="updateTrangThaiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cập nhật trạng thái đơn hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateTrangThaiForm">
                    <input type="hidden" id="donhang_id" name="id">
                    <div class="form-group">
                        <label for="modal_trangthai">Trạng thái mới</label>
                        <select class="form-control" id="modal_trangthai" name="trangthai" required>
                            <option value="">-- Chọn trạng thái --</option>
                            @foreach($trangThaiOptions as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nhanvien">Nhân viên xử lý</label>
                        <input type="text" class="form-control" id="nhanvien" name="nhanvien" 
                               value="{{ auth()->user()->name ?? '' }}" placeholder="Tên nhân viên">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmUpdateTrangThai">Cập nhật</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Order list tweaks */
.pagination-wrapper .pagination{margin-bottom:0}
.pagination-wrapper .page-link{color:#1ab394}
.pagination-wrapper .page-item.active .page-link{background:#1ab394;border-color:#1ab394}
</style>
@endpush
