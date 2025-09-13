@extends('backend.layout')

@section('title', 'Sản phẩm đã xóa')

@section('content')
<div class="container-fluid trashed-page">
    <!-- Page Header -->
    <div class="page-header bg-danger text-white">
        <div class="row align-items-center" style="margin: 16px 0px;">
            <div class="col">
                <h3 class="page-title mb-0" style="    margin-left: 18px;margin-top: 10px;">
                    <i class="fa fa-trash"></i> Sản phẩm đã xóa
                </h3>
                <ul class="breadcrumb mb-0" style="width: 98%;margin: 0px auto;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('sanpham.index') }}" class="text-white">Sản phẩm</a>
                    </li>
                    <li class="breadcrumb-item active text-white">Đã xóa</li>
                </ul>
            </div>
            <div class="col-auto" style="margin:10px">
                <a href="{{ route('sanpham.index') }}" class="btn btn-light" style="color: aliceblue">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" style="margin: 10px 0px;">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-primary">
                        <i class="fa fa-box"></i>
                    </div>
                    <div class="stat-details">
                        <h4>{{ $stats['total_active'] }}</h4>
                        <span>Sản phẩm đang hoạt động</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-warning">
                        <i class="fa fa-trash"></i>
                    </div>
                    <div class="stat-details">
                        <h4>{{ $stats['total_trashed'] }}</h4>
                        <span>Sản phẩm đã xóa</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-info">
                        <i class="fa fa-database"></i>
                    </div>
                    <div class="stat-details">
                        <h4>{{ $stats['total_all'] }}</h4>
                        <span>Tổng số sản phẩm</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-success">
                        <i class="fa fa-percentage"></i>
                    </div>
                    <div class="stat-details">
                        <h4>{{ $stats['total_all'] > 0 ? round(($stats['total_trashed'] / $stats['total_all']) * 100, 1) : 0 }}%</h4>
                        <span>Tỷ lệ đã xóa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Toolbar -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col" style="margin-left: 30px;">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-list"></i> Danh sách sản phẩm đã xóa
                    </h5>
                </div>
                <div class="col-auto" style="margin:20px">
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" id="restore-all-btn">
                            <i class="fa fa-undo"></i> Phục hồi tất cả
                        </button>
                        <button type="button" class="btn btn-danger" id="force-delete-all-btn">
                            <i class="fa fa-trash-o"></i> Xóa vĩnh viễn tất cả
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" style="margin: 16px">
            @if($trashedProducts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Mã SP</th>
                                <th width="35%">Tên sản phẩm</th>
                                <th width="15%">Danh mục</th>
                                <th width="10%">Trạng thái</th>
                                <th width="10%">Ngày xóa</th>
                                <th width="20%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedProducts as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->maSP ?? 'N/A' }}</td>
                                <td>
                                    <div class="product-info">
                                        @php
                                            // Lấy ảnh bao gồm cả đã xóa mềm
                                            $defaultImg = optional($product->hinhanh()->withTrashed()->where('is_default', 1)->first())->url
                                                ?? optional($product->hinhanh()->withTrashed()->first())->url;
                                            $imageExists = $defaultImg ? file_exists(public_path($defaultImg)) : false;
                                        @endphp
                                        @if($defaultImg && $imageExists)
                                            <img src="{{ asset($defaultImg) }}" alt="{{ $product->tenSP }}" class="product-thumbnail" width="40" height="40">
                                        @else
                                            <img src="{{ asset('backend/images/no-image.png') }}" alt="No image" class="product-thumbnail" width="40" height="40">
                                        @endif
                                        <div class="product-details">
                                            <strong>{{ $product->tenSP }}</strong>
                                            @if($product->moTa)
                                                <small class="text-muted d-block">{{ Str::limit($product->moTa, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($product->danhmuc)
                                        <span class="badge badge-info">{{ $product->danhmuc->name }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->trangthai)
                                        <span class="badge badge-success">
                                            <i class="fa fa-check"></i> Kinh doanh
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fa fa-pause"></i> Ngừng kinh doanh
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $product->deleted_at ? $product->deleted_at->format('d/m/Y H:i') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-info show-btn" 
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->tenSP }}"
                                                title="Xem chi tiết sản phẩm">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger restore-btn" 
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->tenSP }}"
                                                title="Phục hồi sản phẩm">
                                            <i class="fa fa-undo"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger force-delete-btn" 
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->tenSP }}"
                                                data-url-post="{{ route('sanpham.forceDelete.post', $product->id) }}"
                                                title="Xóa vĩnh viễn">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Phân trang sản phẩm đã xóa">
                        {{ $trashedProducts->appends(request()->query())->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
                    </nav>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="fa fa-trash fa-3x text-muted mb-3"></i>
                    <h5>Không có sản phẩm nào đã xóa</h5>
                    <p class="text-muted">Tất cả sản phẩm đang hoạt động bình thường.</p>
                    <a href="{{ route('sanpham.index') }}" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirmation Modals -->
@include('backend.sanpham.component.modals.restore-confirm')
@include('backend.sanpham.component.modals.force-delete-confirm')

<!-- Product Detail Modal -->
<div class="modal fade" id="productDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-eye text-info"></i> Chi tiết sản phẩm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <img id="modal-product-image" src="" alt="Product Image" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td id="modal-product-id"></td>
                            </tr>
                            <tr>
                                <td><strong>Mã SP:</strong></td>
                                <td id="modal-product-code"></td>
                            </tr>
                            <tr>
                                <td><strong>Tên sản phẩm:</strong></td>
                                <td id="modal-product-name"></td>
                            </tr>
                            <tr>
                                <td><strong>Danh mục:</strong></td>
                                <td id="modal-product-category"></td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td id="modal-product-status"></td>
                            </tr>
                            <tr>
                                <td><strong>Mô tả:</strong></td>
                                <td id="modal-product-description"></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày xóa:</strong></td>
                                <td id="modal-product-deleted-at"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('backend/js/sanpham-admin-fixed.js') }}"></script>
@endpush

@push('styles')
<style>
.trashed-page {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
    min-height: 100vh;
}

.page-header.bg-danger {
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%) !important;
    box-shadow: 0 4px 20px rgba(211, 47, 47, 0.3);
    border-radius: 0 0 20px 20px;
    margin-bottom: 30px;
}

.stat-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    background: white;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.stat-icon i {
    font-size: 24px;
    color: white;
}

.stat-details h4 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    color: #333;
}

.stat-details span {
    color: #666;
    font-size: 14px;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-thumbnail {
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #ffcdd2;
}

#modal-product-image {
    border-radius: 10px;
    object-fit: cover;
    border: 3px solid #e3f2fd;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.product-details strong {
    color: #333;
    font-weight: 600;
}

.empty-state {
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.empty-state i {
    color: #ffcdd2;
}

.btn-group .btn {
    margin-right: 5px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Pagination styling */
.pagination {
    margin: 0;
}

.page-link {
    color: #d32f2f;
    border-color: #ffcdd2;
}

.page-link:hover {
    color: #b71c1c;
    background-color: #ffebee;
    border-color: #d32f2f;
}

.page-item.active .page-link {
    background-color: #d32f2f;
    border-color: #d32f2f;
}

.page-item.disabled .page-link {
    color: #ccc;
    border-color: #ffcdd2;
}

/* Table styling */
.table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.table thead th {
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
    color: white;
    border: none;
    font-weight: 600;
}

.table tbody tr:hover {
    background-color: #ffebee;
}

/* Card styling */
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    background: white;
}

.card-header {
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
    color: white;
    border: none;
    border-radius: 15px 15px 0 0;
}

.card-title {
    color: white !important;
}
</style>
@endpush
