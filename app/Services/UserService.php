<?php

namespace App\Services;

use App\Services\Interfaces\UserServiceInterface;
use App\Models\UserModel;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class UserService implements UserServiceInterface
{
    protected $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function paginate()
    {
        return $this->userRepository->getAllPaginate();
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->except('_token', 'send', 'rest_password');

            if (isset($payload['ward_id']) && $payload['ward_id'] == "0") {
                $payload['ward_id'] = null;
            }
            if (isset($payload['district_id']) && $payload['district_id'] == "0") {
                $payload['district_id'] = null;
            }
            if (isset($payload['province_id']) && $payload['province_id'] == "0") {
                $payload['province_id'] = null;
            }

           
            // Hash password
            $payload['password'] = Hash::make($payload['password']);

            // Xử lý ngày sinh nếu cần
            if (isset($payload['birthday'])) {
                try {
                    $carbonDate = Carbon::createFromFormat('d/m/Y', $payload['birthday']);
                    $payload['birthday'] = $carbonDate->format('Y-m-d');
                } catch (\Exception $e) {
                    $payload['birthday'] = $payload['birthday'];
                }
            }

            // Xử lý upload ảnh đại diện trong create()
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                // Lưu trực tiếp vào public/uploads/avatars
                $file->move(public_path('uploads/avatars'), $filename);
                $payload['image'] = 'avatars/' . $filename; // chỉ lưu phần relative path
            }


            // Đảm bảo user_catalogue_id được set (mặc định là 2 = user thường)
            if (!isset($payload['user_catalogue_id'])) {
                $payload['user_catalogue_id'] = 2; // 2 = user thường
            }

            $user = $this->userRepository->create($payload);

            // Tự động tạo token cho user mới được tạo bởi admin
            try {
                $token = $user->createToken('admin-created-token')->plainTextToken;
                Log::info("Token created for admin-created user ID: {$user->id}");
            } catch (\Exception $e) {
                Log::error("Failed to create token for user ID {$user->id}: " . $e->getMessage());
                // Không throw exception vì việc tạo user đã thành công
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User create failed: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Xóa tất cả token của user
     */
    public function revokeAllTokens($userId)
    {
        try {
            $user = $this->userRepository->find($userId);
            if ($user) {
                $deletedCount = $user->tokens()->delete();
                Log::info("Revoked {$deletedCount} tokens for user ID: {$userId}");
                return $deletedCount;
            }
            return 0;
        } catch (\Exception $e) {
            Log::error("Failed to revoke tokens for user ID {$userId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Xóa token cụ thể của user
     */
    public function revokeToken($userId, $tokenId)
    {
        try {
            $user = $this->userRepository->find($userId);
            if ($user) {
                $deleted = $user->tokens()->where('id', $tokenId)->delete();
                Log::info("Revoked token ID {$tokenId} for user ID: {$userId}");
                return $deleted > 0;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to revoke token {$tokenId} for user ID {$userId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy danh sách token của user
     */
    public function getUserTokens($userId)
    {
        try {
            $user = $this->userRepository->find($userId);
            if ($user) {
                return $user->tokens()->get();
            }
            return collect();
        } catch (\Exception $e) {
            Log::error("Failed to get tokens for user ID {$userId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Xóa tất cả token của user (dùng khi xóa user)
     */
    public function deleteUserTokens($userId)
    {
        try {
            $user = $this->userRepository->find($userId);
            if ($user) {
                $deletedCount = $user->tokens()->delete();
                Log::info("Deleted {$deletedCount} tokens for user ID: {$userId}");
                return $deletedCount;
            }
            return 0;
        } catch (\Exception $e) {
            Log::error("Failed to delete tokens for user ID {$userId}: " . $e->getMessage());
            return 0;
        }
    }

    public function registerFrontend(array $data, ?string $userAgent = null, ?string $ip = null)
    {
        DB::beginTransaction();
        try {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 1, // Kích hoạt ngay lập tức
                'user_agent' => $userAgent,
                'ip' => $ip,
                'user_catalogue_id' => 2,
            ];

            $user = $this->userRepository->create($payload);

            // Gửi email chào mừng
            $userNameEmail = $user->email; // Email làm tên tài khoản
            $userPassword = $data['password']; // Mật khẩu từ dữ liệu đăng ký ban đầu
            $shopName = config('app.name');
            $realUserName = $data['name']; // Tên người dùng thật

            Mail::to($user->email)->send(new WelcomeEmail($userNameEmail, $userPassword, $shopName, $realUserName));

            DB::commit();
            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Register frontend failed: '.$e->getMessage());
            throw $e;
        }
    }

    public function update($id, $request)
    {
        $user = $this->userRepository->findById($id);
        $data = $request->only([
            'name', 'email', 'phone', 'province_id', 'district_id', 'ward_id', 'address', 'birthday', 'description', 'status', 'role', 'user_catalogue_id'
        ]);
        // Xử lý ảnh location
        if (isset($data['ward_id']) && $data['ward_id'] == "0") {
            $data['ward_id'] = null;
        }
        if (isset($data['district_id']) && $data['district_id'] == "0") {
            $data['district_id'] = null;
        }
        if (isset($data['province_id']) && $data['province_id'] == "0") {
            $data['province_id'] = null;
        }
        // Xử lý ảnh đại diện trong update()
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($user->image && file_exists(public_path('uploads/' . $user->image))) {
                unlink(public_path('uploads/' . $user->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['image'] = 'avatars/' . $filename;
        } else {
            $data['image'] = $user->image;
        }
        return $this->userRepository->update($id, $data);
            }

    public function delete($id)
    {
        $user = $this->userRepository->findById($id);
        if (!$user) return false;

        // Xóa ảnh đại diện
        if ($user->image && file_exists(public_path('uploads/' . $user->image))) {
            unlink(public_path('uploads/' . $user->image));
        }

        // Xóa tất cả token của user trước khi xóa user
        try {
            $user->tokens()->delete();
            Log::info("Deleted all tokens for user ID: {$id}");
        } catch (\Exception $e) {
            Log::error("Failed to delete tokens for user ID {$id}: " . $e->getMessage());
            // Không throw exception vì việc xóa user vẫn cần tiếp tục
        }

        return $this->userRepository->delete($id);
    }

    public function toggleStatus($id, $status)
    {
        $user = $this->userRepository->find($id);
        if (!$user) return false;
        $user->status = $status;
        $user->save();
        return $user;
    }

    public function searchUsers(array $filters)
    {
            return $this->userRepository->search($filters);
    }

    public function deleteMany(array $ids)
    {
        // Xóa token của tất cả user trước khi xóa
        foreach ($ids as $id) {
            try {
                $user = $this->userRepository->findById($id);
                if ($user) {
                    $user->tokens()->delete();
                    Log::info("Deleted all tokens for user ID: {$id}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to delete tokens for user ID {$id}: " . $e->getMessage());
            }
        }

        return $this->userRepository->deleteMany($ids);
    }

    public function updateStatusMany(array $ids, $status)
    {
        return $this->userRepository->updateStatusMany($ids, $status);
    }

    public function updateRoleMany(array $ids, $role)
    {
        return $this->userRepository->updateRoleMany($ids, $role);
    }

    public function getAllPaginate()
    {
        return UserModel::with(['province', 'district', 'ward'])
            ->orderBy('created_at', 'desc')
            ->paginate(10); 
    }

    // Thống kê user
    public function getUserStats(): array
    {
        $stats = [
            'total_users' => UserModel::count(),
            'active_users' => UserModel::where('status', 1)->count(), // status = 1 là active
            'inactive_users' => UserModel::where('status', 0)->count(), // status = 0 là inactive
            'admin_users' => UserModel::where('user_catalogue_id', 1)->count(), // admin có catalogue_id = 1
            'regular_users' => UserModel::where('user_catalogue_id', '>', 1)->count(),
            'users_with_image' => UserModel::whereNotNull('image')->where('image', '!=', '')->count(),
            'users_without_image' => UserModel::where(function($query) {
                $query->whereNull('image')->orWhere('image', '');
            })->count(),
        ];

        return $stats;
    }

    /**
     * Biểu đồ người dùng đăng ký theo ngày
     * @param int $days số ngày gần đây
     * @return array [labels[], data[]]
     */
    public function getRegistrationChartData(int $days = 30): array
    {
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d/m');
            $count = UserModel::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Lấy 10 user mới nhất
     */
    public function getRecentUsers(int $limit = 10)
    {
        return UserModel::orderBy('created_at', 'desc')->limit($limit)->get(['id','name','email','created_at','status','user_catalogue_id']);
    }

    /**
     * Xử lý logic đăng nhập cho frontend.
     * @param array $credentials ['email', 'password']
     * @return \App\Models\UserModel|null Trả về đối tượng người dùng nếu đăng nhập thành công, ngược lại null.
     * @throws \Exception
     */
    public function loginFrontend(array $credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null; // Đăng nhập thất bại
        }

        // Bỏ kiểm tra trạng thái người dùng vì đã kích hoạt mặc định khi đăng ký
        // Tất cả người dùng đều có thể đăng nhập

        return $user;
    }

    /**
     * Xử lý logic đăng xuất của người dùng.
     */
    public function logoutUser(): void
    {
        auth()->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();
    }
}