<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    // Thêm phương thức handle để kiểm tra status
    public function handle($request, \Closure $next, ...$guards)
    {
        $response = parent::handle($request, $next, ...$guards);

        // Nếu đã đăng nhập và user bị khóa
        if (auth()->check()) {
            $user = auth()->user();
            // Chỉ kiểm tra status nếu user có status field và status != 1
            if (isset($user->status) && $user->status != 1) {
                auth()->logout();
                // Nếu là request ajax
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Tài khoản đã bị khóa!'], 403);
                }
                // Nếu là request bình thường
                return redirect()->route('login')->withErrors(['Tài khoản đã bị khóa!']);
            }
        }

        return $response;
    }
}