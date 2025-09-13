<div class="row">
    <div class="col-lg-12">
        <form method="GET" action="{{ route('admin.donhang.index') }}" id="filter-form">
            <div class="row">
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="filter_trangthai">Trạng thái</label>
                        <select name="trangthai" id="filter_trangthai" class="form-control" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                            <option value="">Tất cả trạng thái</option>
                            @foreach($trangThaiOptions as $key => $value)
                                <option value="{{ $key }}" {{ request('trangthai') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="user_id">Khách hàng</label>
                        <select name="user_id" id="user_id" class="form-control" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                            <option value="">Tất cả khách hàng</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="voucher_filter">Voucher</label>
                        <select name="voucher_filter" id="voucher_filter" class="form-control" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                            <option value="">Tất cả</option>
                            <option value="has_voucher" {{ request('voucher_filter') == 'has_voucher' ? 'selected' : '' }}>Có voucher</option>
                            <option value="no_voucher" {{ request('voucher_filter') == 'no_voucher' ? 'selected' : '' }}>Không có voucher</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="from_date">Từ ngày</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" 
                               value="{{ request('from_date') }}" style="height: 45px;">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="to_date">Đến ngày</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" 
                               value="{{ request('to_date') }}" style="height: 45px;">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="search">Tìm kiếm</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Mã đơn hàng, tên khách hàng..." 
                               value="{{ request('search') }}" style="height: 45px;">
                    </div>
                </div>
                <div class="col-lg-1">
                    <div class="form-group">
                        <label for="per_page">Hiển thị</label>
                        <select name="per_page" id="per_page" class="form-control" style="height:45px;">
                            <option value="all" {{ ((string)($filters['per_page'] ?? request('per_page')) === 'all') ? 'selected' : '' }}>Tất cả</option>
                            <option value="10" {{ ((string)($filters['per_page'] ?? request('per_page', 10)) === '10') ? 'selected' : '' }}>10</option>
                            <option value="15" {{ ((string)($filters['per_page'] ?? request('per_page', 10)) === '15') ? 'selected' : '' }}>15</option>
                            <option value="25" {{ ((string)($filters['per_page'] ?? request('per_page', 10)) === '25') ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ((string)($filters['per_page'] ?? request('per_page', 10)) === '50') ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ((string)($filters['per_page'] ?? request('per_page', 10)) === '100') ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="btn-group-vertical d-block">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.donhang.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto submit form when filter changes
    $('#filter_trangthai, #user_id, #voucher_filter, #from_date, #to_date, #per_page').on('change', function() {
        $('#filter-form').submit();
    });
    
    // Search on Enter key
    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            $('#filter-form').submit();
        }
    });
});
</script>
