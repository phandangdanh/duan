// Admin Security - Ngăn chặn nút "Lùi" và bảo vệ admin
(function() {
    'use strict';

    // Kiểm tra nếu đang ở trang admin
    const isAdminPage = window.location.pathname.includes('/admin/') || 
                       window.location.pathname.includes('/dashboard/');

    if (isAdminPage) {
        // Ngăn chặn nút "Lùi" của trình duyệt
        window.addEventListener('popstate', function(event) {
            // Luôn chuyển về trang admin hiện tại
            window.history.pushState(null, null, window.location.href);
        });

        // Thêm state vào history để ngăn nút "Lùi"
        window.history.pushState(null, null, window.location.href);

        // Ngăn chặn phím tắt F5, Ctrl+R
        document.addEventListener('keydown', function(event) {
            if (event.key === 'F5' || (event.ctrlKey && event.key === 'r')) {
                event.preventDefault();
                // Reload trang admin thay vì refresh cache
                window.location.reload(true);
            }
        });

        // Ngăn chặn right-click context menu
        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

        // Ngăn chặn phím tắt Ctrl+U, Ctrl+S, Ctrl+A
        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && (event.key === 'u' || event.key === 's' || event.key === 'a')) {
                event.preventDefault();
            }
        });

        // Kiểm tra session mỗi 30 giây
        setInterval(function() {
            fetch('/admin/check-session', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.status === 401 || response.status === 403) {
                    // Session hết hạn hoặc không có quyền
                    window.location.href = '/dangnhap';
                }
            })
            .catch(error => {
                console.log('Session check failed:', error);
            });
        }, 30000);

        console.log('🔒 Admin Security: Đã kích hoạt bảo vệ admin');
    }
})();
