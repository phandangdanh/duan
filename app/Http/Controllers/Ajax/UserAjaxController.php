<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\UserServiceInterface;

class UserAjaxController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function toggleStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $user = $this->userService->toggleStatus($id, $status);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User không tồn tại!']);
        }

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'message' => $user->status == 1 ? 'Đã mở khóa user!' : 'Đã khóa user!'
        ]);
    }
}