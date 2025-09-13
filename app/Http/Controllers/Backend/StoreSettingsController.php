<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreSettingsController extends Controller
{
    private function getSettingsPath(): string
    {
        return storage_path('app/store_info.json');
    }

    public function get()
    {
        try {
            $path = $this->getSettingsPath();
            $defaults = [
                'name' => 'CỬA HÀNG THỜI TRANG',
                'address' => '123 Đường ABC, Quận XYZ, TP.HCM',
                'phone' => '0123 456 789',
                'email' => 'info@cuahang.com',
            ];
            if (!file_exists($path)) {
                return response()->json(['success' => true, 'data' => $defaults]);
            }
            $data = json_decode(file_get_contents($path), true) ?: $defaults;
            return response()->json(['success' => true, 'data' => array_merge($defaults, $data)]);
        } catch (\Exception $e) {
            Log::error('StoreSettings get error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Không thể tải cấu hình cửa hàng'], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
            ]);

            $path = $this->getSettingsPath();
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, json_encode($validated, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return response()->json(['success' => true, 'message' => 'Đã lưu thông tin cửa hàng', 'data' => $validated]);
        } catch (\Exception $e) {
            Log::error('StoreSettings update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Không thể lưu cấu hình cửa hàng'], 500);
        }
    }
}


