@extends('backend.layout')
@section('title', 'Trang Cập nhật người dùng')
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2></h2>
                <h2>{{ config('apps.user.update.title') }}</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard.index') }}">Dashboard</a>
                    </li>
                    <li class="active">
                        <strong>{{ $config['seo']['title'] }}</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="row mt10">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form enctype="multipart/form-data" action="{{ route('user.update', $user->id) }}" method="post" class="box" >
                @method('PUT')
                @csrf
                <div class="wrapper wrapper-content animated fadeInRight">
                    <div class="row ">
                        <div class="col-lg-4">
                            <div class="head">
                                <div class="title">
                                    <h1 style="font-weight: bold;">Thông tin chung</h1>
                                </div>
                                <div class="description">Nhập thông tin chung của người dùng</div>
                                <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="ibox">
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="form-row">
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Email</label>
                                                <span class="text-danger">(*)</span>
                                                <input type="text" name="email" value="{{ old('email', $user->email ?? '') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Ho và tên</label>
                                                <span class="text-danger">(*)</span>
                                                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <div class="form-row">
                                                    <label for="" class="control-label text-left ">Nhóm Thành viên
                                                        <span class="text-danger">*</span></label>
                                                        <select name="user_catalogue_id" class="form-control setupSelect2">
                                                           
                                                            @foreach($userCatalogue as $key => $item)
                                                                <option value="{{ $key }}"
                                                                    {{ old('user_catalogue_id', $user->user_catalogue_id ?? '') == $key ? 'selected' : '' }}>
                                                                    {{ $item }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-right">Ngày sinh</label>
                                                <input type="date" name="birthday"
                                                    value="{{ old('birthday', isset($user->birthday) ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left">Ảnh đại diện</label>
                                                <div style="margin-bottom: 10px;">
                                                    @if (!empty($user->image))
                                                        <img src="{{ asset('uploads/' . $user->image) }}" 
                                                            alt="Avatar" 
                                                            style="width:100px; height:100px; object-fit:cover; border:1px solid #ddd; border-radius:5px;">
                                                    @else
                                                        <span>Chưa có ảnh</span>
                                                    @endif
                                                </div>
                                                <input type="file" name="image" class="form-control input-image" accept="image/*" onchange="previewImage(event)">
                                                @if ($errors->has('image'))
                                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                                                @endif
                                                <img id="image-preview" src="#" alt="Ảnh xem trước" style="display:none; max-width: 200px; margin-top: 10px;">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <div class="form-row">
                                                    <label for="" class="control-label text-left">Thành phố</label>
                                                    <select name="province_id" class="form-control setupSelect2 province location" data-target="district">
                                                        <option value="0">[Chọn Thành Phố]</option>
                                                        @if (!empty($locations['province']))
                                                            @foreach ($locations['province'] as $province)
                                                                <option value="{{ $province->code }}" {{ old('province_id', $user->province_id ?? '') == $province->code ? 'selected' : '' }}>{{ $province->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <div class="form-row">
                                                    <label for="" class="control-label text-left">Quận/Huyện</label>
                                                    <select name="district_id" class="form-control setupSelect2 district location" data-target="wards">
                                                        <option value="0">[Chọn Quận/Huyện]</option>
                                                        @if (!empty($locations['district']))
                                                            @foreach ($locations['district'] as $district)
                                                            <option value="{{ $district->code }}" {{ old('district_id', $user->district_id ?? '') == $district->code ? 'selected' : '' }}>{{ $district->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <div class="form-row">
                                                    <label for="" class="control-label text-left">Phường / Xã</label>
                                                    <select name="ward_id" class="form-control setupSelect2 wards" data-target="wards">
                                                        <option value="0">[Chọn Phường / Xã]</option>
                                                        @if (!empty($locations['ward']))
                                                            @foreach ($locations['ward'] as $ward)
                                                                <option value="{{ $ward->code }}" {{ old('ward_id', $user->ward_id ?? '') == $ward->code ? 'selected' : '' }}>{{ $ward->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Địa chỉ</label>
                                                <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Số điện
                                                    thoai</label>

                                                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Ghi chú</label>

                                                <input type="text" name="description"
                                                    value="{{ old('description', $user->description ?? '') }}" class="form-control" placeholder=""
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left">Tình trạng hoạt động</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ old('status', $user->status ?? 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                    <option value="0" {{ old('status', $user->status ?? 1) == 0 ? 'selected' : '' }}>Khóa</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left">Quyền</label>
                                                <select name="role" class="form-control">
                                                    <option value="user" {{ old('role', $user->role ?? 'user') == 'user' ? 'selected' : '' }}>User</option>
                                                    <option value="admin" {{ old('role', $user->role ?? 'user') == 'admin' ? 'selected' : '' }}>Admin</option>
                                                   
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mr-2">
                        <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
                    </div>
                </div>
            </form>

        </div>
        <!-- USER UPDATE -->
        <script>

            function previewImage(event) {
                var reader = new FileReader();
                reader.onload = function(){
                    var output = document.getElementById('image-preview');
                    output.src = reader.result;
                    output.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>
    @endsection

    