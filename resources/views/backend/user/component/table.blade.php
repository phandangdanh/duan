<div style="display: flex; justify-content: end; margin: 10px;">
    {{-- Form Xóa tất cả đã chọn --}}
    <form id="bulk-delete-form" method="POST" action="{{ route('user.bulkAction') }}" style="display:inline;">
        @csrf
        <input type="hidden" name="action_type" value="delete">
        <button type="submit" class="btn btn-danger mb0 btn-sm mr-10" id="bulk-delete-btn" disabled style="margin-right:10px;">Xóa tất cả đã chọn</button>
    </form>

    {{-- Form Khóa đã chọn --}}
    <form id="bulk-lock-form" method="POST" action="{{ route('user.bulkAction') }}" style="display:inline;">
        @csrf
        <input type="hidden" name="action_type" value="lock">
        <button type="submit" class="btn btn-warning mb0 btn-sm mr-10" id="bulk-lock-btn" disabled style="margin-right:10px;">Khóa đã chọn</button>
    </form>

    {{-- Form Mở khóa đã chọn --}}
    <form id="bulk-unlock-form" method="POST" action="{{ route('user.bulkAction') }}" style="display:inline;">
        @csrf
        <input type="hidden" name="action_type" value="unlock">
        <button type="submit" class="btn btn-success mb0 btn-sm" id="bulk-unlock-btn" disabled>Mở khóa đã chọn</button>
    </form>

    {{-- Form Cộng tác viên --}}
    <form id="bulk-collab-form" method="POST" action="{{ route('user.bulkAction') }}" style="display:inline; margin-left: 10px;">
        @csrf
        <input type="hidden" name="action_type" value="set_collaborator">
        <button type="submit" class="btn btn-info mb0 btn-sm mr-10" id="bulk-collab-btn" disabled>Chuyển thành cộng tác viên</button>
    </form>

    {{-- Form Quản trị viên --}}
    <form id="bulk-admin-form" method="POST" action="{{ route('user.bulkAction') }}" style="display:inline; margin-left: 10px;">
        @csrf
        <input type="hidden" name="action_type" value="set_admin">
        <button type="submit" class="btn btn-primary mb0 btn-sm" id="bulk-admin-btn" disabled>Chuyển thành quản trị viên</button>
    </form>
</div>

<table class="table table-striped table-bordered text-center">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkAll"></th>
            <th>Ảnh đại diện</th>
            <th>Họ và tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Địa chỉ</th>
            <th>Địa chỉ chi tiết</th>
            <th>Ngày sinh</th>
            <th>Quyền</th>
            <th>Mô tả</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
            <tr>
                <td><input type="checkbox" class="checkbox-item" value="{{ $user->id }}"></td>
                <td>
                    @if ($user->image)
                    <img src="{{ asset('uploads/' . $user->image) }}" 
                    alt="Avatar" 
                    style="width:60px; height:60px; object-fit:cover;">
                    @else
                        <span>Chưa có ảnh</span>
                    @endif
                </td>
                <td class="info-item name">{{ $user->name }}</td>
                <td class="info-item email">{{ $user->email }}</td>
                <td class="info-item phone">{{ $user->phone }}</td>
                <td>{{ $user->address }}</td>
                <td>
                    {{ $user->province->name ?? '' }}
                    {{ $user->district ? ' - ' . $user->district->name : '' }}
                    {{ $user->ward ? ' - ' . $user->ward->name : '' }}
                </td>
                <td>
                    @if (!empty($user->birthday))
                        {{ \Carbon\Carbon::parse($user->birthday)->format('d-m-Y') }}
                    @endif
                </td>
                <td>
                    {{ $user->user_catalogue_id == 1 ? 'Quản trị viên' : 'Cộng tác viên' }}
                </td>
                <td>{{ $user->description }}</td>
                <td class="text-center">
                    <div class="status-container">
                        <button type="button" class="btn btn-sm status-btn {{ $user->status == 1 ? 'btn-success' : 'btn-danger' }}" data-id="{{ $user->id }}">
                            <i class="fa {{ $user->status == 1 ? 'fa-check-circle' : 'fa-lock' }}"></i>
                            {{ $user->status == 1 ? 'Hoạt động' : 'Khóa' }}
                        </button>
                    </div>
                </td>
                <td class="text-center">
                    <a href="{{ route('user.edit', $user->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                    <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="form-delete-user" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-delete-user"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center text-danger">Không tìm thấy người dùng nào.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@php
    $isPaginator = $users instanceof \Illuminate\Pagination\LengthAwarePaginator || $users instanceof \Illuminate\Pagination\Paginator;
@endphp
@if($isPaginator)
<div class="d-flex justify-content-center mt-3">
    {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>
@endif
