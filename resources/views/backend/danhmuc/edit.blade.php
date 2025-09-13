@extends('backend.layout')
@section('title', 'Chỉnh sửa danh mục')
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>{{ config('apps.danhmuc.title', 'Danh mục') }}</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard.index') }}">Dashboard</a>
                    </li>
                    <li>
                        <a href="{{ route('danhmuc.index') }}">Danh mục</a>
                    </li>
                    <li class="active">
                        <strong>Chỉnh sửa</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2"></div>
        </div>
        
        <div class="row mt10">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Chỉnh sửa danh mục: {{ $category->name }}</h5>
                    </div>
                    <div class="ibox-content">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('danhmuc.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label class="col-sm-2 control-label">Tên danh mục <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                                           value="{{ old('name', $category->name) }}" required placeholder="Nhập tên danh mục...">
                                    @error('name')
                                        <span class="help-block text-danger">
                                            <i class="fa fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                <label class="col-sm-2 control-label">Danh mục cha</label>
                                <div class="col-sm-10">
                                    <select name="parent_id" class="form-control {{ $errors->has('parent_id') ? 'is-invalid' : '' }}">
                                        <option value="0" {{ old('parent_id', $category->parent_id) == 0 ? 'selected' : '' }}>Danh mục gốc</option>
                                        @php
                                            $currentId = $category->id;
                                            $selected = old('parent_id', $category->parent_id);
                                            $renderOptions = function($items, $level = 0) use (&$renderOptions, $currentId, $selected) {
                                                foreach ($items as $item) {
                                                    if ($item->id == $currentId) { continue; }
                                                    $prefix = str_repeat('— ', $level);
                                                    echo '<option value="' . $item->id . '"' . ($selected == $item->id ? ' selected' : '') . '>' . $prefix . e($item->name) . '</option>';
                                                    if ($item->relationLoaded('allChildren')) {
                                                        $children = $item->allChildren;
                                                    } else {
                                                        $children = $item->children;
                                                    }
                                                    if ($children && $children->count()) {
                                                        $renderOptions($children, $level + 1);
                                                    }
                                                }
                                            };
                                            $renderOptions($categories, 0);
                                        @endphp
                                    </select>
                                    @error('parent_id')
                                        <span class="help-block text-danger">
                                            <i class="fa fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                                <label class="col-sm-2 control-label">Mô tả</label>
                                <div class="col-sm-10">
                                    <textarea name="description" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" 
                                              rows="3" placeholder="Nhập mô tả danh mục...">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <span class="help-block text-danger">
                                            <i class="fa fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                                <label class="col-sm-2 control-label">Ảnh hiện tại</label>
                                <div class="col-sm-10">
                                    @if($category->image)
                                        <img src="{{ asset('uploads/' . $category->image) }}" 
                                            alt="{{ $category->name }}" 
                                            style="width: 100px; height: 100px; object-fit: cover; margin-bottom: 10px; border: 2px solid #ddd; border-radius: 5px;">
                                        <br>
                                        <small class="text-muted">
                                            <i class="fa fa-image"></i> Ảnh hiện tại: {{ basename($category->image) }}
                                        </small>
                                    @else
                                        <p class="text-muted">
                                            <i class="fa fa-image"></i> Chưa có ảnh
                                        </p>
                                    @endif
                                    
                                    <div style="margin-top: 10px;">
                                        <input type="file" name="image" class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}" 
                                               accept="image/*">
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> Chấp nhận: JPG, PNG, GIF. Tối đa: 2MB. Để trống nếu không muốn thay đổi ảnh.
                                        </small>
                                    </div>
                                    
                                    @error('image')
                                        <span class="help-block text-danger">
                                            <i class="fa fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('sort_order') ? 'has-error' : '' }}">
                                <label class="col-sm-2 control-label">Ưu tiên hiển thị</label>
                                <div class="col-sm-10">
                                    <input type="number" name="sort_order" class="form-control {{ $errors->has('sort_order') ? 'is-invalid' : '' }}" 
                                           value="{{ old('sort_order', $category->sort_order) }}" min="0" placeholder="0" title="Ưu tiên hiển thị (0 là mặc định)">
                                    <small class="text-muted">Số nhỏ đứng trước. 0 = mặc định.</small>
                                    @error('sort_order')
                                        <span class="help-block text-danger">
                                            <i class="fa fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                                <label class="col-sm-2 control-label">Trạng thái <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="status" value="active" {{ old('status', $category->status) == 'active' ? 'checked' : '' }}>
                                            <span class="text-success"><i class="fa fa-check-circle"></i> Hoạt động</span>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="status" value="inactive" {{ old('status', $category->status) == 'inactive' ? 'checked' : '' }}>
                                            <span class="text-warning"><i class="fa fa-ban"></i> Không hoạt động</span>
                                        </label>
                                    </div>
                                    @error('status')
                                        <span class="help-block text-danger">
                                            <i class="fa fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Cập nhật
                                    </button>
                                    <a href="{{ route('danhmuc.index') }}" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .has-error .form-control {
        border-color: #a94442;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    }
    
    .has-error .form-control:focus {
        border-color: #843534;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 6px #ce8483;
    }
    
    .help-block {
        margin-top: 5px;
        font-size: 13px;
    }
    
    .form-group.has-error label {
        color: #a94442;
    }
    
    .alert-danger {
        border-color: #ebccd1;
        color: #a94442;
        background-color: #f2dede;
    }
    
    .alert-danger ul {
        margin-bottom: 0;
    }
    
    .text-muted i {
        margin-right: 5px;
    }
</style>
@endsection
