@php
    // Đếm số lượng còn hàng / hết hàng TRONG TRANG HIỆN TẠI
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
<div class="d-flex justify-content-end mb-2" style="gap:12px">
    <span class="badge badge-success">Còn hàng (trang này): {{ $pageInStock }}</span>
    <span class="badge badge-danger">Hết hàng (trang này): {{ $pageOutOfStock }}</span>
</div>
<div class="table-responsive">
    <table class="table table-striped table-hover" id="sanpham-table">
            <thead>
                <tr>
                <th width="5%">
                        <input type="checkbox" id="select-all" class="form-check-input">
                    </th>
                <th width="5%">ID</th>
                <th width="15%">Mã SP</th>
                <th width="20%">Tên sản phẩm</th>
                <th width="15%">Danh mục</th>
                <th width="10%">Trạng thái</th>
                <th width="10%">Giá</th>
                <th width="10%">Tồn kho</th>
                <th width="10%">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sanphams as $sanpham)
            <tr data-id="{{ $sanpham->id }}">
                        <td>
                    <input type="checkbox" class="form-check-input product-checkbox" value="{{ $sanpham->id }}">
                        </td>
                <td>{{ $sanpham->id }}</td>
                <td>{{ $sanpham->maSP ?? 'N/A' }}</td>
                <td>
                    <div class="product-info">
                        @php
                            $defaultImage = optional($sanpham->hinhanh->where('is_default', 1)->first())->url;
                            $imageExists = $defaultImage ? file_exists(public_path($defaultImage)) : false;
                        @endphp
                        @if($defaultImage && $imageExists)
                            <img src="{{ asset($defaultImage) }}"
                                 alt="{{ $sanpham->tenSP }}"
                                 class="product-thumbnail"
                                 width="40" height="40">
                        @endif
                        <div class="product-details">
                            <strong>{{ $sanpham->tenSP }}</strong>
                            @if($sanpham->moTa)
                                <small class="text-muted d-block">{{ Str::limit($sanpham->moTa, 50) }}</small>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    @if($sanpham->danhmuc)
                        <span class="badge badge-info">{{ $sanpham->danhmuc->name }}</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                <td>
                    @if($sanpham->trangthai)
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
                    @php
                        $basePrice = $sanpham->base_price ?? null;
                        $variantPrices = $sanpham->chitietsanpham
                            ->map(function($d){
                                $price = $d->gia_khuyenmai && $d->gia_khuyenmai > 0 ? $d->gia_khuyenmai : $d->gia;
                                return is_null($price) ? null : (float)$price;
                            })
                            ->filter(function($v){ return !is_null($v) && $v >= 0; });
                        $minVariant = $variantPrices->min();
                        $maxVariant = $variantPrices->max();
                    @endphp
                    @if($basePrice)
                        Giá gốc: {{ number_format($basePrice, 0, ',', '.') }} đ
                    @elseif($variantPrices->count() > 0)
                        @if($minVariant === $maxVariant)
                            Theo biến thể: {{ number_format($minVariant, 0, ',', '.') }} đ
                        @else
                            Theo biến thể: {{ number_format($minVariant, 0, ',', '.') }} - {{ number_format($maxVariant, 0, ',', '.') }} đ
                        @endif
                    @else
                        <span class="text-muted">Chưa có giá</span>
                    @endif
                </td>
                        <td>
                            @php
                                $totalStock = $sanpham->chitietsanpham->sum('soLuong');
                            @endphp
                            @if($totalStock > 0)
                                <span class="badge badge-success">Còn hàng ({{ $totalStock }})</span>
                            @else
                                <span class="badge badge-danger">Hết hàng</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                        <a href="{{ route('sanpham.show', $sanpham->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="Xem chi tiết">
                                    <i class="fa fa-eye"></i>
                                </a>
                        <a href="{{ route('sanpham.edit', $sanpham->id) }}" 
                           class="btn btn-sm btn-primary" 
                           title="Sửa">
                                    <i class="fa fa-edit"></i>
                                </a>
                        <button type="button" 
                                class="btn btn-sm {{ $sanpham->trangthai ? 'btn-warning' : 'btn-success' }} toggle-status-btn" 
                                data-id="{{ $sanpham->id }}" 
                                data-status="{{ $sanpham->trangthai }}"
                                title="{{ $sanpham->trangthai ? 'Ngừng kinh doanh' : 'Kinh doanh' }}">
                            <i class="fa {{ $sanpham->trangthai ? 'fa-pause' : 'fa-check' }}"></i>
                        </button>
                                <button type="button" 
                                        class="btn btn-sm btn-danger delete-btn" 
                                data-id="{{ $sanpham->id }}"
                                data-name="{{ $sanpham->tenSP }}"
                                data-url="{{ route('sanpham.destroy.post', $sanpham->id) }}"
                                        title="Xóa">
                                    <i class="fa fa-trash"></i>
                                </button>
                                
                                <!-- Soft delete management buttons -->
                                @if($sanpham->trashed())
                                    <button type="button" 
                                            class="btn btn-sm btn-warning restore-btn" 
                                            data-id="{{ $sanpham->id }}"
                                            data-name="{{ $sanpham->tenSP }}"
                                            title="Phục hồi sản phẩm">
                                        <i class="fa fa-undo"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger force-delete-btn" 
                                            data-id="{{ $sanpham->id }}"
                                            data-name="{{ $sanpham->tenSP }}"
                                            title="Xóa vĩnh viễn">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">
                    <div class="empty-state">
                        <i class="fa fa-box fa-3x text-muted mb-3"></i>
                        <h5>Không có sản phẩm nào</h5>
                        <p class="text-muted">Hãy thêm sản phẩm đầu tiên để bắt đầu.</p>
                        <a href="{{ route('sanpham.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Thêm sản phẩm
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
                </div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-exclamation-triangle text-danger"></i> Xác nhận xóa
                </h5>
                <button type="button" class="btn-close close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sản phẩm <strong id="delete-product-name"></strong>?</p>
                <p class="text-danger"><small>Hành động này không thể hoàn tác!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Hủy</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-exclamation-triangle text-danger"></i> Xác nhận xóa nhiều sản phẩm
                </h5>
                <button type="button" class="btn-close close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa <strong id="bulk-delete-count"></strong> sản phẩm đã chọn?</p>
                <p class="text-danger"><small>Hành động này không thể hoàn tác!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-bulk-delete">
                    <i class="fa fa-trash"></i> Xóa tất cả
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reusable Confirm Modal (for bulk actions etc.) -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle me-2 text-danger"></i> Xác nhận</h5>
                <button type="button" class="btn-close close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-1 confirm-message">Bạn có chắc chắn?</p>
                <small class="text-danger">Hành động này không thể hoàn tác!</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmModalAgree">Đồng ý</button>
            </div>
        </div>
    </div>
</div>
