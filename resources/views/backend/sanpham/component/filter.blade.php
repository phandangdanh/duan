<div class="filter-section mb-3">
    <form method="GET" action="{{ route('sanpham.index') }}" id="filter-form">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="search">Tìm kiếm:</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Tên sản phẩm, mã SP..." style="height: 45px;">
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <label for="category">Danh mục:</label>
                    <select class="form-control" id="category" name="category" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                        <option value="">Tất cả danh mục</option>
                        @foreach($danhmucs as $danhmuc)
                            <option value="{{ $danhmuc->id }}" {{ request('category') == $danhmuc->id ? 'selected' : '' }}>
                                {{ $danhmuc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <label for="status">Trạng thái:</label>
                    <select class="form-control" id="status" name="status" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Kinh doanh</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <label for="stock">Tồn kho:</label>
                    <select class="form-control" id="stock" name="stock" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                        <option value="">Tất cả</option>
                        <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                        <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                    </select>
                </div>
        </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <label for="perpage">Hiển thị:</label>
                    <select class="form-control" id="perpage" name="perpage" style="padding-right: 25px; background-position: right 8px center; height: 45px;">
                        <option value="10" {{ request('perpage') == '10' ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('perpage') == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('perpage') == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('perpage') == '100' ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('perpage') == 'all' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>
                </div>
                
            <div class="col-md-1">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm ml-1" id="clear-filter">
                            <i class="fa fa-times"></i>
                        </button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('filter-form');
    if(!form) return;
    form.addEventListener('submit', function(){
        var q = document.getElementById('search');
        if(q){ q.value = (q.value || '').trim(); }
        // Khi có từ khóa, bỏ các filter khác để tránh loại nhầm
        if(q && q.value){
            ['category','status','stock'].forEach(function(name){
                var el = form.querySelector('[name="'+name+'"]');
                if(el){ el.value = ''; }
            });
            try{
                console.log('[ProductSearch] keyword=', q.value);
            }catch(e){}
        }
    });
    // Log truy vấn trả về đã bật ở backend: xem trong storage/logs/laravel.log
});
</script>
@endpush

<!-- Statistics Cards -->
@php
    // Đếm tồn kho theo TRANG HIỆN TẠI để đồng bộ với bảng
    $pageInStock = 0;
    $pageOutOfStock = 0;
    if(isset($sanphams)){
        $iterable = method_exists($sanphams, 'items') ? collect($sanphams->items()) : collect($sanphams);
        foreach($iterable as $p){
            $sum = $p->chitietsanpham->sum('soLuong');
            if($sum > 0) { $pageInStock++; } else { $pageOutOfStock++; }
        }
    }
@endphp
<div class="row mb-1">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ $stats['total'] ?? 0 }}</h4>
                <small>Tổng sản phẩm</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $stats['active'] ?? 0 }}</h4>
                <small>Đang kinh doanh</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ $stats['inactive'] ?? 0 }}</h4>
                <small>Ngừng kinh doanh</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($stats['avg_price'] ?? 0) }}</h4>
                <small>Giá trung bình</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white" style="background-color: #6c757d;">
            <div class="card-body text-center">
                <h4>{{ number_format($stats['in_stock'] ?? 0) }}</h4>
                <small>Tổng sản phẩm > 0</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h4>{{ $stats['out_of_stock'] ?? 0 }}</h4>
                <small>Tổng sản phẩm = 0</small>
            </div>
        </div>
    </div>
    </div>
<div class="text-muted" style="font-size:12px;margin-left:8px;margin-bottom:12px">Số liệu theo toàn bộ kết quả đã lọc</div>
