<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\DanhMucServiceInterface;

class DanhMucAjaxController extends Controller
{
    protected $danhMucService;

    public function __construct(DanhMucServiceInterface $danhMucService)
    {
        $this->danhMucService = $danhMucService;
    }

    public function toggleStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $danhMuc = $this->danhMucService->toggleStatus($id, $status);

        if (!$danhMuc) {
            return response()->json(['success' => false, 'message' => 'Danh mục không tồn tại!']);
        }
        return response()->json([
            'success' => true,
            'status' => $danhMuc->status,
            'message' => $danhMuc->status === 'active' 
                ? 'Danh mục đã được kích hoạt!' 
                : 'Danh mục đã bị vô hiệu hóa!'
        ]);
        
    }
}
