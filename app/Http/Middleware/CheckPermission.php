<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        $user = $request->user();
        
        if (!$user->hasPermission($permission)) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền truy cập. Yêu cầu quyền: ' . $permission
            ], 403);
        }

        return $next($request);
    }
}
