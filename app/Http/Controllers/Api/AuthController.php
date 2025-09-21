<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Đăng nhập API và trả về token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Mật khẩu không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra thông tin đăng nhập
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không chính xác',
            ], 401);
        }

        $user = UserModel::where('email', $request->email)->first();

        // Kiểm tra trạng thái người dùng
        if (!$user->isActive()) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn chưa được kích hoạt hoặc đã bị khóa',
            ], 403);
        }

        // Tạo token mới
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Đăng ký tài khoản API
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tạo người dùng mới
        $user = UserModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Xác minh email ngay lập tức
            'status' => 1, // Kích hoạt tài khoản
        ]);

        // Gán vai trò "user" cho người dùng mới
        $userRole = \App\Models\Role::where('slug', 'user')->first();
        if ($userRole) {
            $user->roles()->attach($userRole->id);
        }

        // Tạo token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Đăng xuất API
     */
    public function logout(Request $request)
    {
        // Xóa token hiện tại
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đăng xuất thành công',
        ]);
    }

    /**
     * Lấy thông tin người dùng hiện tại
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        // Lấy thông tin vai trò và quyền
        $roles = $user->roles()->with('permissions')->get();
        $permissions = [];
        
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->slug;
            }
        }
        
        // Loại bỏ các quyền trùng lặp
        $permissions = array_unique($permissions);

        return response()->json([
            'status' => true,
            'user' => $user,
            'roles' => $roles->pluck('slug'),
            'permissions' => $permissions,
        ]);
    }
}
