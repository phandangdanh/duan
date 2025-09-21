<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /**
     * Hiển thị form quên mật khẩu
     */
    public function showLinkRequestForm()
    {
        return view('fontend.auth.passwords.email');
    }

    /**
     * Xử lý gửi email đặt lại mật khẩu
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email'], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.'
        ]);
 
        $status = Password::sendResetLink(
            $request->only('email')
        );
 
        return $status === Password::RESET_LINK_SENT
                    ? back()->with('status', 'Chúng tôi đã gửi email đặt lại mật khẩu đến địa chỉ email của bạn!')
                    : back()->withErrors(['email' => 'Không tìm thấy địa chỉ email này trong hệ thống của chúng tôi.']);
    }
}
