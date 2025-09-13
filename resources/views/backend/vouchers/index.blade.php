@extends('backend.layout')

@section('title', 'Quản lý Voucher')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/css/css-voucher.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @include('backend.component.breadcrum', [
        'title' => 'Quản lý Voucher',
        'items' => [
            ['text' => 'Dashboard', 'url' => route('dashboard.index')],
            ['text' => 'Voucher', 'active' => true]
        ]
    ])

    <!-- Statistics Cards -->
    <div class="row mb-4 voucher-stats">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng Voucher</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gift fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đang Hoạt Động</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Có Thể Sử Dụng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['usable'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Hết Hạn</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['expired'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Voucher</h6>
            <div class="action-buttons">
                <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm Voucher
                </a>
                <a href="{{ route('admin.vouchers.statistics') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-chart-bar"></i> Thống Kê
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter -->
            @include('backend.vouchers.component.filter', ['filters' => $filters])

            <!-- Table -->
            @include('backend.vouchers.component.table', ['vouchers' => $vouchers])

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $vouchers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Toggle status
    $('.toggle-status').click(function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const url = $(this).attr('href');
        
        Swal.fire({
            title: 'Thay đổi trạng thái? ',
            text: 'Bạn có chắc chắn muốn thay đổi trạng thái voucher này?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Hủy',
            customClass: { confirmButton: 'swal2-confirm-rounded', cancelButton: 'swal2-cancel-rounded' }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json'
                })
                .done(function(response) {
                    if (response && response.success) {
                        const $row = $(e.currentTarget).closest('tr');
                        const $btn = $(e.currentTarget);
                        const isActive = response.data && (response.data.trang_thai === 1 || response.data.trang_thai === true);
                        const $statusTd = $row.find('td').eq(6);
                        if (isActive) {
                            $statusTd.html('<span class="badge badge-success">Hoạt động</span>');
                            $btn.removeClass('btn-activate').addClass('btn-deactivate').attr('title', 'Tạm dừng');
                            $btn.find('i').removeClass('fa-play').addClass('fa-pause');
                        } else {
                            $statusTd.html('<span class="badge badge-secondary">Tạm dừng</span>');
                            $btn.removeClass('btn-deactivate').addClass('btn-activate').attr('title', 'Kích hoạt');
                            $btn.find('i').removeClass('fa-pause').addClass('fa-play');
                        }
                        return;
                    }
                    Swal.fire('Lỗi', (response && response.message) ? response.message : 'Không xác định', 'error');
                })
                .fail(function() {
                    Swal.fire('Lỗi', 'Có lỗi xảy ra!', 'error');
                });
            }
        });
    });

    // Delete confirmation
    $('.delete-voucher').click(function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        
        Swal.fire({
            title: 'Xóa voucher?',
            text: 'Hành động này không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy',
            customClass: { confirmButton: 'swal2-confirm-danger swal2-confirm-rounded', cancelButton: 'swal2-cancel-rounded' }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                })
                .done(function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        Swal.fire('Lỗi', response.message || 'Không xác định', 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Lỗi', 'Có lỗi xảy ra!', 'error');
                });
            }
        });
    });

    // Tự động lọc khi thay đổi select/input trong bộ lọc
    (function() {
        let debounceTimer = null;
        const submitFilter = function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                $('.voucher-filter').trigger('submit');
            }, 200);
        };

        $('.voucher-filter select').on('change', submitFilter);
        $('.voucher-filter input[type="text"]').on('keyup', submitFilter);
    })();
});
</script>
@endpush