@extends('backend.layout')
@section('title', 'Trang San Pham')
@section('content')
<link rel="stylesheet" href="{{ asset('backend/css/sanpham-admin.css') }}">

<script src="{{ asset('backend/js/sanpham-admin-fixed.js') }}" defer></script>
<script>
window.SANPHAM_ENDPOINTS = {
  bulkStatus: "{{ url('/admin/sanpham/bulk-status') }}",
  bulkDelete: "{{ url('/admin/sanpham/bulk-delete') }}",
  toggleStatus: "{{ url('/ajax/sanpham/toggle-status/:id') }}"
};
</script>

    <div class="wrapper wrapper-content">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>{{ config('apps.sanpham.title') }}</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard.index') }}">Dashboard</a>
                    </li>
                    <li class="active">
                        <a href="{{ route('sanpham.index') }}">
                            <strong>{{ config('apps.sanpham.title') }}</strong>
                        </a>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">
                <div class="title-action"></div>
            </div>
        </div>
        <div class="row mt10">

            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>
                            <a href="{{ route('sanpham.index') }}" style="color: inherit; text-decoration: none;">
                                {{ config('apps.sanpham.table') }}
                            </a>
                        </h5>
                        @include('backend.sanpham.component.toolbox')
                    </div>
                    <div class="ibox-content">
                        @include('backend.sanpham.component.filter')
                        @include('backend.sanpham.component.table')
                        @if(method_exists($sanphams, 'links'))
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">Tổng sản phẩm: <strong>{{ $sanphams->total() }}</strong></div>
                                <div class="pagination-wrapper">
                                    {{ $sanphams->appends(request()->query())->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('scripts')
    <!-- Existing scripts -->
    <script>
    // Confirm helpers reuse Bootstrap modal from table partial if needed
    function showConfirm(message){
        return new Promise(function(resolve){
            const modalEl = document.getElementById('confirmModal');
            if(!modalEl){ resolve(window.confirm(message||'Bạn có chắc chắn?')); return; }
            document.querySelector('#confirmModal .confirm-message').textContent = message || 'Bạn có chắc chắn?';
            const agreeBtn = document.getElementById('confirmModalAgree');
            function onAgree(){ cleanup(); resolve(true); }
            function onCancel(){ cleanup(); resolve(false); }
            function cleanup(){ if(window.bootstrap&&bootstrap.Modal.getInstance(modalEl)){bootstrap.Modal.getInstance(modalEl).hide();} else { $('#confirmModal').modal('hide'); } agreeBtn.removeEventListener('click', onAgree); modalEl.removeEventListener('hidden.bs.modal', onCancel); }
            if(window.bootstrap&&bootstrap.Modal){ const modal=new bootstrap.Modal(modalEl,{backdrop:'static',keyboard:false}); agreeBtn.addEventListener('click', onAgree); modalEl.addEventListener('hidden.bs.modal', onCancel, {once:true}); modal.show(); }
            else if(typeof $!=='undefined' && typeof $('#confirmModal').modal === 'function'){ agreeBtn.addEventListener('click', onAgree); $('#confirmModal').one('hidden.bs.modal', onCancel).modal({backdrop:'static', keyboard:false, show:true}); }
            else { resolve(window.confirm(message||'Bạn có chắc chắn?')); }
        });
    }
    </script>
@endsection

@push('styles')
<style>
/* Product list tweaks */
.product-info{display:flex;align-items:center;gap:10px}
.product-thumbnail{width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid #eee}
.pagination-wrapper .pagination{margin-bottom:0}
.pagination-wrapper .page-link{color:#1ab394}
.pagination-wrapper .page-item.active .page-link{background:#1ab394;border-color:#1ab394}
</style>
@endpush