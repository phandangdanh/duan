<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang đăng nhập Google
     */
    public function redirectToGoogle()
    {
        // @phpstan-ignore-next-line
        return Socialite::driver('google')
            // @phpstan-ignore-next-line
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Xử lý callback từ Google sau khi xác thực
     */
    public function handleGoogleCallback()
    {
        try {
            // Lấy thông tin người dùng từ Google
            $googleUser = Socialite::driver('google')->user();
            
            // Tìm user theo email
            $user = UserModel::where('email', $googleUser->email)->first();
            
            if ($user) {
                // Nếu user đã tồn tại, cập nhật thông tin Google
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar ?? $user->avatar,
                    'provider' => 'google',
                ]);
            } else {
                // Nếu user chưa tồn tại, tạo mới
                $user = UserModel::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(rand(1000000, 9999999)), // Mật khẩu ngẫu nhiên
                    'provider' => 'google',
                    'email_verified_at' => now(), // Email đã được xác thực qua Google
                    'status' => 1, // Kích hoạt tài khoản
                    'user_catalogue_id' => 2, // 2 = user thường
                ]);
            }
            
            // Đăng nhập user
            Auth::login($user);
            
            // Tạo token nếu sử dụng API
            $token = $user->createToken('google-auth-token')->plainTextToken;
            
            // Lưu token vào session để sử dụng sau này nếu cần
            session(['api_token' => $token]);
            
            // Chuyển hướng về trang trước đó hoặc trang chủ
            return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
            
        } catch (Exception $e) {
            // Xử lý lỗi
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại: ' . $e->getMessage());
        }
    }
}
