<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            // Nếu là API request
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chưa đăng nhập'
                ], 401);
            }
            // Nếu là web request
            return redirect()->route('login');
        }

        $user = $request->user();
        
        // Kiểm tra role đơn giản dựa trên user_catalogue_id
        $userRole = $user->user_catalogue_id ?? 0;
        
        // 1 = admin, 2 = user
        // Chỉ cho phép admin (user_catalogue_id = 1) vào admin
        if ($role === 'admin' && $userRole !== 1) {
            // Nếu là API request
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền truy cập. Yêu cầu quyền admin.'
                ], 403);
            }
            // Nếu là web request - redirect về home với thông báo
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang admin!');
        }
        
        if ($role === 'user' && $userRole !== 2) {
            // Nếu là API request
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền truy cập. Yêu cầu quyền user.'
                ], 403);
            }
            // Nếu là web request
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập!');
        }

        return $next($request);
    }
}
