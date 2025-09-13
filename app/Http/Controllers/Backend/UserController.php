<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\UserServiceInterface;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use App\Repositories\Interfaces\DistrictRepositoryInterface;
use App\Repositories\Interfaces\WardRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    protected $provinceRepository;
    protected $userRepository;
    public function __construct(UserServiceInterface $userService, ProvinceRepository $provinceRepository, UserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->provinceRepository = $provinceRepository;
        $this->userRepository = $userRepository;
    }

    
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'user_catalogue_id', 'perpage']);
        $users = $this->userService->searchUsers($filters);

        // Lấy thống kê tổng quan + dữ liệu biểu đồ + user mới
        $stats = $this->userService->getUserStats();
        $chart = $this->userService->getRegistrationChartData(30);
        $recentUsers = $this->userService->getRecentUsers(10);

        $config = [
            'css' => ['backend/css/plugins/switchery/switchery.css'],
            'js' => ['backend/js/plugins/switchery/switchery.js'],
        ];
        $config['seo'] = config('apps.user.index');

        return view('backend.user.index', compact('config', 'users', 'stats', 'chart', 'recentUsers'));
    }

    public function statistics(Request $request)
    {
        // Lọc theo khoảng thời gian: 7/30/90 ngày
        $range = (int)$request->get('range', 30);
        if (!in_array($range, [7, 30, 90])) { $range = 30; }

        $stats = $this->userService->getUserStats();
        $chart = $this->userService->getRegistrationChartData($range);
        $recentUsers = $this->userService->getRecentUsers(10);

        $config = ['seo' => ['title' => 'Thống kê User', 'table' => 'Thống kê User']];
        return view('backend.user.statistics', compact('config','stats','chart','recentUsers','range'));
    }

    public function create()
    {
        $locations = [
        'province' => $this->provinceRepository->all(),
        'district' => [],
        'ward' => [],
        ];
        $config = [
            'css' => ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',

            ],
        ];
        $config['seo'] =  config('apps.user.create');

        return view('backend.user.create', compact('config', 'locations'));
    }

    public function store(StoreUserRequest $request)
    {       
        if ($this->userService->create($request)) {
            return redirect()->route('user.index')->with('success', 'Thêm thành viên thành công');
        }
        return redirect()->back()->with('error', 'Thêm thành viên thất bại . Hãy thử lại');
    }

    public function edit($id)
    {
        $user = $this->userRepository->findById($id);
        
        $userCatalogue = [
            1 => 'Quản trị viên',
            2 => 'Cộng tác viên'
        ];

        $provinces = $this->provinceRepository->all();
        $districts = [];
        if ($user->province_id) {
            $districtRepository = app(DistrictRepositoryInterface::class);
            $districts = $districtRepository->getByProvinceId($user->province_id);
        }
        $wards = [];
        if ($user->district_id) {
            $wardRepository = app(WardRepositoryInterface::class);
            $wards = $wardRepository->getByDistrictId($user->district_id);
        }
        $locations = [
            'province' => $provinces,
            'district' => $districts,
            'ward' => $wards,
        ];
        

        $config = [
            'css' => ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',

            ],
        ];
        $config['seo'] =  config('apps.user.update');

        return view('backend.user.update', compact('config', 'locations', 'user', 'userCatalogue'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        if ($this->userService->update($id, $request)) {
            return redirect()->route('user.index')->with('success', 'Cập nhật thành viên thành công');
        }
        return redirect()->back()->with('error', 'Cập nhật thành viên thất bại. Hãy thử lại');
    }

    public function destroy($id)
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            return redirect()->route('user.index')->with('error', 'Không tìm thấy user!');
        }

        if ($this->userService->delete($id)) {
            return redirect()->route('user.index')->with('success', 'Xóa thành viên thành công!');
        }
        return redirect()->route('user.index')->with('error', 'Xóa thành viên thất bại!');
    }


    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        // Chuẩn hóa: chấp nhận chuỗi "1,2,3" hoặc mảng
        if (is_string($ids)) {
            $ids = array_filter(array_map('intval', explode(',', $ids)), function($v){ return $v > 0; });
        } elseif (is_array($ids)) {
            $ids = array_filter(array_map('intval', $ids), function($v){ return $v > 0; });
        } else {
            $ids = [];
        }
        $action = $request->input('action_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một user.');
        }

        switch ($action) {
            case 'delete':
                $deleted = $this->userService->deleteMany($ids);
                if ($deleted) {
                    return redirect()->route('user.index')->with('success', 'Xóa thành viên thành công!');
                }
                return redirect()->route('user.index')->with('error', 'Xóa thành viên thất bại!');
            case 'lock':
                $updated = $this->userService->updateStatusMany($ids, 0);
                if ($updated) {
                    return redirect()->route('user.index')->with('success', 'Khóa thành viên thành công!');
                }
                return redirect()->route('user.index')->with('error', 'Khóa thành viên thất bại!');
            case 'unlock':
                $updated = $this->userService->updateStatusMany($ids, 1);
                if ($updated) {
                    return redirect()->route('user.index')->with('success', 'Mở khóa thành viên thành công!');
                }
                return redirect()->route('user.index')->with('error', 'Mở khóa thành viên thất bại!');
            case 'set_collaborator':
                $updated = $this->userService->updateRoleMany($ids, 2); // 2 = Cộng tác viên
                if ($updated) {
                    return redirect()->route('user.index')->with('success', 'Chuyển thành cộng tác viên thành công!');
                }
                return redirect()->route('user.index')->with('error', 'Chuyển quyền thất bại!');
            case 'set_admin':
                $updated = $this->userService->updateRoleMany($ids, 1); // 1 = Quản trị viên
                if ($updated) {
                    return redirect()->route('user.index')->with('success', 'Chuyển thành quản trị viên thành công!');
                }
                return redirect()->route('user.index')->with('error', 'Chuyển quyền thất bại!');
            default:
                return redirect()->back()->with('error', 'Hành động không hợp lệ!');
        }
    }
    
}
