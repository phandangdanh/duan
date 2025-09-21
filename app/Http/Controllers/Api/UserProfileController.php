<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class UserProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     summary="Lấy thông tin profile user",
     *     description="User có thể xem thông tin profile của chính mình",
     *     tags={"2. Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lấy thông tin profile thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lấy thông tin profile thành công"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'status' => true,
                'message' => 'Lấy thông tin profile thành công',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'avatar' => $user->avatar,
                        'status' => $user->status,
                        'user_catalogue_id' => $user->user_catalogue_id,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/user/profile",
     *     summary="Cập nhật profile user",
     *     description="User có thể cập nhật thông tin profile của chính mình",
     *     tags={"2. Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *             @OA\Property(property="phone", type="string", example="0901234567"),
     *             @OA\Property(property="address", type="string", example="123 ABC Street")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật profile thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cập nhật profile thành công")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();
            
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:500',
            ]);

            $user->update($request->only(['name', 'phone', 'address']));

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật profile thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }
}
