<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminLock
{
    /**
     * Handle an incoming request.
     * Middleware này ngăn chặn nút "Lùi" cho tất cả user đã đăng nhập
     * Chỉ cho phép logout mới thoát được
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Chỉ áp dụng cho trang admin và user đã đăng nhập
        $isAdminPage = str_contains($request->path(), 'admin/') || str_contains($request->path(), 'dashboard/');
        
        if ($user && $user->id >= 1 && $isAdminPage) {
            // Thêm headers ngăn cache
            $response = $next($request);
            
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            $response->headers->set('ETag', '"' . md5(time()) . '"');
            
            // Thêm JavaScript để ngăn TẤT CẢ cách thoát khỏi admin
            $content = $response->getContent();
            $adminLockScript = '
            <script>
            (function() {
                // Kiểm tra nếu đang ở trang admin
                const isAdminPage = window.location.pathname.includes("/admin/") || 
                                   window.location.pathname.includes("/dashboard/");
                
                if (isAdminPage) {
                    let allowExit = false;
                    
                    // Thêm state vào history để ngăn nút "Lùi"
                    window.history.pushState(null, null, window.location.href);
                    
                    // Ngăn chặn popstate (nút Lùi)
                    window.addEventListener("popstate", function(event) {
                        if (!allowExit) {
                            // Chỉ quay lại trang admin hiện tại, không reload
                            window.history.pushState(null, null, window.location.href);
                            console.log("🚫 Ngăn chặn nút Lùi - Vẫn ở trang admin");
                        }
                    });
                    
                    // Lắng nghe sự kiện logout
                    document.addEventListener("click", function(event) {
                        if (event.target.closest("form[action*=\"/logout\"]") || 
                            event.target.closest("button[type=\"submit\"]") ||
                            event.target.textContent.includes("Log out") ||
                            event.target.textContent.includes("Đăng xuất")) {
                            // Cho phép logout
                            allowExit = true;
                            console.log("🔓 Admin Logout: Cho phép thoát khỏi admin");
                        }
                    });
                    
                    console.log("🔒 Admin Lock: Đã kích hoạt bảo vệ trang admin - Chỉ ngăn nút Lùi!");
                }
            })();
            </script>';
            
            // Chèn script vào cuối body
            $content = str_replace('</body>', $adminLockScript . '</body>', $content);
            $response->setContent($content);
            
            return $response;
        }
        
        return $next($request);
    }
}