<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\UserModel;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\LoginRequest;
// use App\Mail\WelcomeEmail;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = app(UserService::class)->registerFrontend($data, $request->userAgent(), $request->ip());

        // // Gửi email chào mừng (Logic đã chuyển sang UserService)
        // $userName = $user->name; // Hoặc một trường khác cho tên người dùng
        // $password = $request->password; // Lấy mật khẩu từ request
        // $shopName = config('app.name'); // Hoặc tên cửa hàng từ cấu hình khác

        // Mail::to($user->email)->send(new WelcomeEmail($userName, $password, $shopName));

        // // Dòng gửi email xác minh cũng đã chuyển sang UserService
        // $freshUser = $user->fresh(['emailVerification']);
        // $token = optional($freshUser->emailVerification)->token;
        // if (!$token) {
        //     // fallback: try query directly (in case relation not set for some reason)
        //     $record = \App\Models\EmailVerification::where('user_id', $freshUser->id)->latest('id')->first();
        //     $token = $record?->token;
        // }
        // if ($token) {
        //     Mail::to($freshUser->email)->send(new VerifyEmail($freshUser, $token));
        // }

        // Đăng nhập người dùng sau khi đăng ký
        auth()->login($user);
        
        // Tạo token cho người dùng mới đăng ký
        try {
            $token = $user->createToken('register-token')->plainTextToken;
            session(['api_token' => $token]);
        } catch (\Exception $e) {
            // Bỏ qua lỗi tạo token nếu có
            // Log lỗi nếu cần: Log::error('Token creation failed: ' . $e->getMessage());
        }

        // Redirect về trang trước đó hoặc trang chủ
        return redirect()->intended(route('home'))->with('success', 'Đăng ký thành công!');
    }

    /**
     * Xử lý yêu cầu đăng nhập của người dùng.
     * @param \App\Http\Requests\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        try {
            $user = app(UserService::class)->loginFrontend($credentials);

            if (!$user) {
                return back()->withInput()->withErrors(['email' => 'Thông tin đăng nhập không chính xác.']);
            }

            // Đăng nhập người dùng vào phiên
            auth()->login($user);
            
            // Tạo token giống như đăng nhập Google
            try {
                $token = $user->createToken('auth-token')->plainTextToken;
                session(['api_token' => $token]);
            } catch (\Exception $e) {
                // Bỏ qua lỗi tạo token nếu có
                // Log lỗi nếu cần: Log::error('Token creation failed: ' . $e->getMessage());
            }

            // Redirect về trang trước đó hoặc trang chủ
            return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * Xử lý yêu cầu đăng xuất của người dùng.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        // Xóa token khỏi database nếu user đã đăng nhập
        if (auth()->check()) {
            $user = UserModel::find(auth()->id());
            // Xóa tất cả token của user hiện tại
            $user->tokens()->delete();
        }
        
        // Xóa session token
        session()->forget('api_token');
        
        // Đăng xuất người dùng
        app(UserService::class)->logoutUser();

        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
