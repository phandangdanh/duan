<div class="ibox-tools">
    <div class="d-flex justify-content-between align-items-center">
        <div class="bulk-actions d-none">
            <span class="selected-count">0 sản phẩm được chọn</span>
            <div class="btn-group ml-2">
                <button type="button" class="btn btn-sm btn-success bulk-status-btn" data-status="1">
                    <i class="fa fa-check"></i> Kinh doanh đã chọn
                </button>
                <button type="button" class="btn btn-sm btn-warning bulk-status-btn" data-status="0">
                    <i class="fa fa-pause"></i> Ngừng kinh doanh đã chọn
        </button>
                <button type="button" class="btn btn-sm btn-danger bulk-delete-btn">
                    <i class="fa fa-trash"></i> Xóa đã chọn
            </button>
    </div>
</div>

        <div class="action-buttons">
            <a href="{{ route('sanpham.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Thêm sản phẩm
            </a>
            <button type="button" class="btn btn-info btn-sm" id="refresh-btn">
                <i class="fa fa-refresh"></i> Làm mới
            </button>
            
            <!-- Soft delete management buttons -->
            <div class="btn-group ml-2">
                <a href="{{ route('sanpham.trashed') }}" class="btn btn-secondary btn-sm" title="Xem sản phẩm đã xóa" style="background: red;color:white">
                    <i class="fa fa-trash"></i> Sản phẩm đã xóa
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Form -->
<form id="bulk-status-form" method="POST" action="{{ route('sanpham.bulkStatus') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulk-status-ids">
    <input type="hidden" name="status" id="bulk-status-value">
</form>

<form id="bulk-delete-form" method="POST" action="{{ route('sanpham.bulkDelete') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulk-delete-ids">
</form>
