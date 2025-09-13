<form method="GET" action="{{ route('admin.vouchers.index') }}" class="mb-4 voucher-filter">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="search">Tìm kiếm</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ $filters['search'] ?? '' }}" 
                       placeholder="Mã, tên hoặc mô tả voucher" style="height: 45px;">
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="trang_thai">Trạng thái</label>
                <select class="form-control" id="trang_thai" name="trang_thai" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                    <option value="" {{ ($filters['trang_thai'] ?? '') === '' ? 'selected' : '' }}>Tất cả</option>
                    <option value="1" {{ ($filters['trang_thai'] ?? '') == '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ ($filters['trang_thai'] ?? '') == '0' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="loai_giam_gia">Loại giảm giá</label>
                <select class="form-control" id="loai_giam_gia" name="loai_giam_gia" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                    <option value="" {{ ($filters['loai_giam_gia'] ?? '') === '' ? 'selected' : '' }}>Tất cả</option>
                    <option value="phan_tram" {{ ($filters['loai_giam_gia'] ?? '') == 'phan_tram' ? 'selected' : '' }}>Phần trăm</option>
                    <option value="tien_mat" {{ ($filters['loai_giam_gia'] ?? '') == 'tien_mat' ? 'selected' : '' }}>Tiền mặt</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="trang_thai_hoat_dong">Trạng thái hoạt động</label>
                <select class="form-control" id="trang_thai_hoat_dong" name="trang_thai_hoat_dong" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                    <option value="" {{ ($filters['trang_thai_hoat_dong'] ?? '') === '' ? 'selected' : '' }}>Tất cả</option>
                    <option value="dang_hoat_dong" {{ ($filters['trang_thai_hoat_dong'] ?? '') == 'dang_hoat_dong' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="chua_bat_dau" {{ ($filters['trang_thai_hoat_dong'] ?? '') == 'chua_bat_dau' ? 'selected' : '' }}>Chưa bắt đầu</option>
                    <option value="da_het_han" {{ ($filters['trang_thai_hoat_dong'] ?? '') == 'da_het_han' ? 'selected' : '' }}>Đã hết hạn</option>
                    <option value="het_so_luong" {{ ($filters['trang_thai_hoat_dong'] ?? '') == 'het_so_luong' ? 'selected' : '' }}>Hết số lượng</option>
                    <option value="tam_dung" {{ ($filters['trang_thai_hoat_dong'] ?? '') == 'tam_dung' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="per_page">Hiển thị</label>
                <select class="form-control" id="per_page" name="per_page" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                    <option value="all" {{ (string)request('per_page') === 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="10" {{ ((string)request('per_page', 10) === '10') ? 'selected' : '' }}>10</option>
                    <option value="15" {{ ((string)request('per_page') === '15') ? 'selected' : '' }}>15</option>
                    <option value="25" {{ ((string)request('per_page') === '25') ? 'selected' : '' }}>25</option>
                    <option value="50" {{ ((string)request('per_page') === '50') ? 'selected' : '' }}>50</option>
                    <option value="100" {{ ((string)request('per_page') === '100') ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-1">
            <div class="form-group">
                <label>&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
