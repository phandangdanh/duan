<form id="user-filter-form" method="GET" action="{{ route('user.index') }}">
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
                <select name="user_catalogue_id" class="form-control mr10"  id="user-catalogue-select" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                    <option value="0" {{ request('user_catalogue_id') == 0 ? 'selected' : '' }}>Chọn Nhóm Thành Viên</option>
                    <option value="1" {{ request('user_catalogue_id') == 1 ? 'selected' : '' }}>Quản trị viên</option>
                    <option value="2" {{ request('user_catalogue_id') == 2 ? 'selected' : '' }}>Cộng tác viên</option>
                </select>
                <div class="uk-search uk-flex uk-flex-middle mr10">
                    <div class="input-group">
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="Nhập Từ khóa bạn muốn tìm kiếm..." class="form-control" style="height: 45px;">
                        <span class="input-group-btn">
                            <button type="submit" name="search" value="search" class="btn btn-primary mb0 btn-sm">Tìm Kiếm</button>
                            <a href="{{ route('user.index') }}" class="btn btn-danger mb0 btn-sm" style="margin-left: 5px;">Xóa bộ lọc tìm kiếm</a>
                        </span>
                    </div>
                </div>
                <a href="{{ route('user.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i> Thêm mới</a>
            </div>
        </div>
    </div>
</div>
</form>
<script>
    document.getElementById('user-catalogue-select').addEventListener('change', function() {
        document.getElementById('user-filter-form').submit();
    });
    document.getElementById('perpage-select').addEventListener('change', function() {
        document.getElementById('user-filter-form').submit();
    });
</script>
