@extends('backend.layout')
@section('title', 'Trang Thêm người dùng')
@section('content')
    
    <div class="wrapper wrapper-content">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2></h2>
                <h2>{{ config('apps.user.title') }}</h2>
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
            <form enctype="multipart/form-data" action="{{ route('user.store') }}" method="post" class="box" >
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
                                                <input type="text" name="email" value="{{ old('email') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Ho và tên</label>
                                                <span class="text-danger">(*)</span>
                                                <input type="text" name="name" value="{{ old('name') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <div class="form-row">
                                                    <label for="" class="control-label text-left ">Nhóm Thành viên
                                                        <span class="text-danger">*</span></label>
                                                    <select name="user_catalogue_id" class="form-control setupSelect2">
                                                        <option value="0">[Chọn Nhóm Thành Viên]</option>
                                                        <option value="1">Quản trị viên</option>
                                                        <option value="2">Cộng tác viên</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-right">Ngày sinh</label>
                                                <input type="date" name="birthday" value="{{ old('birthday') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <div class="form-row">
                                                    <label for="" class="control-label text-left ">Mật khẩu <span
                                                            class="text-danger">*</span></label>
                                                    <input type="password" name="password" value="{{ old('password') }}"
                                                        class="form-control" placeholder="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <div class="form-row">
                                                    <label for="" class="control-label text-left ">Nhập Mật khẩu
                                                        <span class="text-danger">*</span></label>
                                                    <input type="password" name="rest_password" value=""
                                                        class="form-control" placeholder="" autocomplete="off">
                                                </div>
                                            </div>
                                            <label for="" class="control-label text-left">Ảnh đại diện</label>
                                                <input type="file" name="image" class="form-control input-image" accept="image/*" onchange="previewImage(event)">
                                                @if ($errors->has('image'))
                                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                                                @endif
                                                <img id="image-preview" src="#" alt="Ảnh xem trước" style="display:none; max-width: 200px; margin-top: 10px;">
                                                
                                                <div class="col-lg-6 margin">
                                                    <div class="form-row">
                                                        <label class="control-label text-left">Thành phố</label>
                                                        <select name="province_id" class="form-control setupSelect2 province location" data-target="district">
                                                            <option value="0">[Chọn Thành Phố]</option>
                                                            @if (!empty($locations['province']))
                                                                @foreach ($locations['province'] as $province)
                                                                    <option value="{{ $province->code }}" {{ old('province_id', $user->province_id ?? '') == $province->code ? 'selected' : '' }}>
                                                                        {{ $province->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6 margin">
                                                    <div class="form-row">
                                                        <label class="control-label text-left">Quận/Huyện</label>
                                                        <select name="district_id" class="form-control setupSelect2 district location" data-target="wards">
                                                            <option value="0">[Chọn Quận/Huyện]</option>
                                                            @if (!empty($locations['district']))
                                                                @foreach ($locations['district'] as $district)
                                                                    <option value="{{ $district->code }}" {{ old('district_id', $user->district_id ?? '') == $district->code ? 'selected' : '' }}>
                                                                        {{ $district->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6 margin">
                                                    <div class="form-row">
                                                        <label class="control-label text-left">Phường / Xã</label>
                                                        <select name="ward_id" class="form-control setupSelect2 wards">
                                                            <option value="0">[Chọn Phường / Xã]</option>
                                                            @if (!empty($locations['ward']))
                                                                @foreach ($locations['ward'] as $ward)
                                                                    <option value="{{ $ward->code }}" {{ old('ward_id', $user->ward_id ?? '') == $ward->code ? 'selected' : '' }}>
                                                                        {{ $ward->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Địa chỉ</label>
                                                <input type="text" name="address" value="{{ old('address') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Số điện
                                                    thoai</label>

                                                <input type="text" name="phone" value="{{ old('phone') }}"
                                                    class="form-control" placeholder="" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left ">Ghi chú</label>

                                                <input type="text" name="description"
                                                    value="{{ old('description') }}" class="form-control" placeholder=""
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left">Tình trạng hoạt động</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                    <option value="0" {{ old('status') == 2 ? 'selected' : '' }}>Khóa</option>
                                                </select>
                                            </div>
                                            {{-- <div class="col-lg-6 margin">
                                                <label for="" class="control-label text-left">Quyền</label>
                                                <select name="role" class="form-control">
                                                    <option value="user" {{ old('role', 'user') == 'user' ? 'selected' : '' }}>User</option>
                                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                                   
                                                </select>
                                            </div> --}}
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
        <!-- USER CREATE -->
        <script>
            window.ajaxLocationUrl = "{{ route('ajax.location.getLocation') }}";
            window.province_id = '{{ old('province_id') }}';
            window.district_id = '{{ old('district_id') }}';
            window.ward_id = '{{ old('ward_id') }}';
            function previewImage(event) {
                var reader = new FileReader();
                reader.onload = function(){
                    var output = document.getElementById('image-preview');
                    output.src = reader.result;
                    output.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            }


            // moved to window.* for location.js to access

           
        </script>
    @endsection
