<form id="danhmuc-filter-form" method="GET" action="{{ route('danhmuc.index') }}">
<div class="filter">
    <div class="uk-flex uk-flex-middle uk-flex-space-between">
        <div class="perpage">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <select name="perpage" class="form-control input-sm perpage filter" id="perpage-select" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                    @for ($i = 5; $i <= 50; $i += 5)
                        <option value="{{ $i }}" {{ request('perpage', 10) == $i ? 'selected' : '' }}>{{ $i }} bản ghi</option>
                    @endfor
                    <option value="all" {{ request('perpage') == 'all' ? 'selected' : '' }}>Tất cả</option>
                </select>
            </div>
        </div>
        
        <div class="action">
            <div class="uk-flex uk-flex-middle">
                <select name="status" class="form-control mr10" id="status-select" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả trạng thái</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
                <select name="sort" class="form-control mr10" id="sort-select" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                    <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                    <option value="sort_order" {{ request('sort') == 'sort_order' ? 'selected' : '' }}>Thứ tự</option>
                </select>
                
                <div class="uk-search uk-flex uk-flex-middle mr10">
                    <div class="input-group">
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="Nhập từ khóa tìm kiếm..." class="form-control" style="height: 45px;">
                        <span class="input-group-btn">
                            <button type="submit" name="search" value="search" class="btn btn-primary mb0 btn-sm " style="font-size: 13px;">Tìm Kiếm</button>
                            <a href="{{ route('danhmuc.index') }}" class="btn btn-danger mb0 btn-sm" style="margin-left: 5px; font-size: 13px;">Xóa bộ lọc tìm kiếm</a>
                        </span>
                    </div>
                </div>
                <a href="{{ route('danhmuc.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i> Thêm mới</a>
            </div>
        </div>
    </div>
</div>
</form>

{{-- Bulk Actions - Đảm bảo ở bên trái --}}
<div class="bulk-actions-container" style="display: flex; justify-content: end; align-items: center;">
    <div class="bulk-actions">
        <div class="btn-group">
            <form id="bulk-delete-form" method="POST" action="{{ route('danhmuc.bulkAction') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="action_type" value="delete">
                <button type="submit" class="btn btn-danger btn-sm" id="bulk-delete-btn" disabled>
                    <i class="fa fa-trash"></i> Xóa nhiều đã chọn
                </button>
            </form>

            <form id="bulk-activate-form" method="POST" action="{{ route('danhmuc.bulkAction') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="action_type" value="activate">
                <button type="submit" class="btn btn-success btn-sm" id="bulk-activate-btn" disabled>
                    <i class="fa fa-check"></i> Kích hoạt đã chọn
                </button>
            </form>

            <form id="bulk-deactivate-form" method="POST" action="{{ route('danhmuc.bulkAction') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="action_type" value="deactivate">
                <button type="submit" class="btn btn-warning btn-sm" id="bulk-deactivate-btn" disabled>
                    <i class="fa fa-ban"></i> Vô hiệu hóa đã chọn
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('status-select').addEventListener('change', function() {
        document.getElementById('danhmuc-filter-form').submit();
    });
    document.getElementById('sort-select').addEventListener('change', function() {
        document.getElementById('danhmuc-filter-form').submit();
    });
    document.getElementById('perpage-select').addEventListener('change', function() {
        document.getElementById('danhmuc-filter-form').submit();
    });
</script>