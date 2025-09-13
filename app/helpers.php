<?php

if (!function_exists('user_avatar_url')) {
    function user_avatar_url($path) {
        if (empty($path)) {
            return asset('backend/img/profile_small.jpg');
        }
        return asset('storage/' . ltrim($path, '/'));
    }
}

if (!function_exists('clearUserSessionErrors')) {
    /**
     * Xóa các session error không liên quan đến danh mục
     */
    function clearUserSessionErrors()
    {
        if (session()->has('error')) {
            $error = session('error');
            if (str_contains(strtolower($error), 'user') || 
                str_contains(strtolower($error), 'người dùng') ||
                str_contains(strtolower($error), 'không tồn tại')) {
                session()->forget('error');
                session()->forget('errors');
            }
        }
        
        if (session()->has('success')) {
            $success = session('success');
            if (str_contains(strtolower($success), 'user') || 
                str_contains(strtolower($success), 'người dùng')) {
                session()->forget('success');
            }
        }
        
        if (session()->has('warning')) {
            $warning = session('warning');
            if (str_contains(strtolower($warning), 'user') || 
                str_contains(strtolower($warning), 'người dùng')) {
                session()->forget('warning');
            }
        }
    }
}