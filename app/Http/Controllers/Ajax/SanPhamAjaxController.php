<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use App\Models\BinhLuan;
use App\Models\SanPhamHinhanh;
use App\Models\ChiTietDonHang;
use App\Models\DanhGia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SanPhamAjaxController extends Controller
{
    public function uploadTemp(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
            'type' => 'required|in:main,extra'
        ]);

        try {
            $file = $request->file('file');
            $tmpDir = 'public/tmp';
            $key = $request->input('type') . '_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            // Lưu file vào storage/app/public/tmp
            $path = $file->storeAs($tmpDir, $key);

            return response()->json([
                'success' => true,
                'temp_key' => $key,
                'file_name' => $file->getClientOriginalName(),
                'url' => Storage::url('tmp/' . $key)
            ]);
        } catch (\Exception $e) {
            Log::error('Upload temp error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload thất bại'], 500);
        }
    }
    public function toggleStatus(Request $request, $id)
    {
        try {
            $sanpham = SanPham::active()->findOrFail($id);

            // Nếu client gửi status thì dùng, nếu không thì toggle
            if ($request->has('status')) {
                $sanpham->trangthai = (int) $request->input('status') ? 1 : 0;
            } else {
                $sanpham->trangthai = $sanpham->trangthai == 1 ? 0 : 1;
            }

            $sanpham->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => ['id' => $sanpham->id, 'trangthai' => $sanpham->trangthai]
            ]);
        } catch (\Exception $e) {
            Log::error('SanPhamAjax toggleStatus error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái'
            ], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        try {
            // Debug: Log request data
            Log::info('SanPhamAjax bulkAction called with:', [
                'action_type' => $request->input('action_type'),
                'ids' => $request->input('ids'),
                'all_inputs' => $request->all(),
                'headers' => $request->headers->all()
            ]);
            
            $actionType = $request->input('action_type');
            $ids = $request->input('ids', []);

            // Chuyển đổi string thành array nếu cần
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (empty($ids)) {
                Log::warning('SanPhamAjax bulkAction: No IDs provided');
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn ít nhất một sản phẩm!'
                ], 400);
            }

            switch ($actionType) {
                case 'delete':
                    Log::info('SanPhamAjax bulkAction: Processing delete for IDs: ' . implode(',', $ids));
                    $result = $this->bulkDelete($ids);
                    $message = 'Xóa thành công ' . count($ids) . ' sản phẩm!';
                    break;
                case 'activate':
                    Log::info('SanPhamAjax bulkAction: Processing activate for IDs: ' . implode(',', $ids));
                    $result = $this->bulkUpdateStatus($ids, 1);
                    $message = 'Kích hoạt thành công ' . count($ids) . ' sản phẩm!';
                    break;
                case 'deactivate':
                    Log::info('SanPhamAjax bulkAction: Processing deactivate for IDs: ' . implode(',', $ids));
                    $result = $this->bulkUpdateStatus($ids, 0);
                    $message = 'Vô hiệu hóa thành công ' . count($ids) . ' sản phẩm!';
                    break;
                default:
                    Log::warning('SanPhamAjax bulkAction: Invalid action type:', $actionType);
                    return response()->json([
                        'success' => false,
                        'message' => 'Hành động không hợp lệ!'
                    ], 400);
            }

            if ($result) {
                Log::info('SanPhamAjax bulkAction: Success for action ' . $actionType . ' with IDs: ' . implode(',', $ids));
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated' => $ids
                ]);
            } else {
                Log::error('SanPhamAjax bulkAction: Failed for action ' . $actionType . ' with IDs: ' . implode(',', $ids));
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thực hiện hành động!'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('SanPhamAjax bulk action error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa dữ liệu liên quan đến sản phẩm theo thứ tự đúng
     */
    private function deleteProductRelatedData(int $productId): array
    {
        $deletedCounts = [];
        
        try {
            // 1. Lấy tất cả chi tiết sản phẩm của sản phẩm này (bao gồm cả đã xóa mềm)
            $chiTietSanPhamIds = ChiTietSanPham::withTrashed()->where('id_sp', $productId)->pluck('id')->toArray();
            
            if (!empty($chiTietSanPhamIds)) {
                // 2. Xóa đánh giá (danhgia) - phụ thuộc vào chitietdonhang
                $chitietDonHangIds = ChiTietDonHang::whereIn('id_chitietsanpham', $chiTietSanPhamIds)->pluck('id')->toArray();
                
                if (!empty($chitietDonHangIds)) {
                    $deletedCounts['danhgia'] = DanhGia::whereIn('id_chitietdonhang', $chitietDonHangIds)->delete();
                    
                    // 3. Xóa chi tiết đơn hàng
                    $deletedCounts['chitietdonhang'] = ChiTietDonHang::whereIn('id_chitietsanpham', $chiTietSanPhamIds)->delete();
                }
                
                // 4. Xóa vĩnh viễn chi tiết sản phẩm (bao gồm cả đã xóa mềm)
                $deletedCounts['chitietsanpham'] = ChiTietSanPham::withTrashed()->where('id_sp', $productId)->forceDelete();
            }
            
            // 5. Xóa bình luận (bao gồm cả đã xóa mềm)
            $deletedCounts['binhluan'] = BinhLuan::withTrashed()->where('id_sp', $productId)->forceDelete();
            
            // 6. Xóa vĩnh viễn hình ảnh sản phẩm (bao gồm cả đã xóa mềm)
            $deletedCounts['hinhanh'] = SanPhamHinhanh::withTrashed()->where('sanpham_id', $productId)->forceDelete();
            
            Log::info("Force deleted related data for product {$productId}:", $deletedCounts);
            
        } catch (\Exception $e) {
            Log::error("Error force deleting related data for product {$productId}: " . $e->getMessage());
            throw $e;
        }
        
        return $deletedCounts;
    }

    private function bulkDelete(array $ids): bool
    {
        try {
            DB::beginTransaction();
            
            $totalDeleted = 0;
            $allDeletedCounts = [];
            
            foreach ($ids as $id) {
                $sanpham = SanPham::find($id);
                if ($sanpham) {
                    Log::info('SanPhamAjax bulkDelete: Deleting product ID: ' . $id);
                    
                    // Xóa dữ liệu liên quan
                    $deletedCounts = $this->deleteProductRelatedData($id);
                    $allDeletedCounts[$id] = $deletedCounts;
                    
                    // Xóa sản phẩm chính
                    $sanpham->delete();
                    $totalDeleted++;
                }
            }
            
            DB::commit();
            
            Log::info('SanPhamAjax bulkDelete: Successfully deleted ' . $totalDeleted . ' products', [
                'deleted_ids' => $ids,
                'deleted_counts' => $allDeletedCounts
            ]);
            
            return $totalDeleted > 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPhamAjax bulkDelete error: ' . $e->getMessage());
            return false;
        }
    }

    private function bulkUpdateStatus(array $ids, int $status): bool
    {
        try {
            $affected = SanPham::whereIn('id', $ids)->update(['trangthai' => $status]);
            return $affected > 0;
        } catch (\Exception $e) {
            Log::error('SanPhamAjax bulkUpdateStatus error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Kiểm tra sản phẩm có tồn tại không (chỉ sản phẩm chưa xóa)
            $sanpham = SanPham::active()->findOrFail($id);
            
            Log::info('SanPhamAjax deleteProduct: Starting deletion for product ID: ' . $id);
            
            // Xóa theo thứ tự để tránh vi phạm khóa ngoại
            $deletedCounts = $this->deleteProductRelatedData($id);
            
            // Xóa sản phẩm chính
            $sanpham->delete();
            
            DB::commit();
            
            Log::info('SanPhamAjax deleteProduct: Successfully deleted product ID: ' . $id, $deletedCounts);
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công!',
                'data' => [
                    'id' => $id,
                    'deleted_counts' => $deletedCounts
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPhamAjax deleteProduct error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa sản phẩm: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStats()
    {
        try {
            $stats = [
                'total' => SanPham::active()->count(),
                'active' => SanPham::kinhDoanh()->count(),
                'inactive' => SanPham::ngungKinhDoanh()->count(),
                'avg_price' => DB::table('chitietsanpham')->avg('gia') ?? 0,
                'total_stock' => DB::table('chitietsanpham')->sum('soLuong') ?? 0,
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('SanPhamAjax getStats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thống kê'
            ], 500);
        }
    }

    /**
     * Kiểm tra dữ liệu sẽ bị xóa trước khi thực hiện xóa (dry run)
     */
    public function checkDeleteImpact(Request $request, $id)
    {
        try {
            $sanpham = SanPham::active()->findOrFail($id);
            
            $impact = [
                'sanpham' => [
                    'id' => $sanpham->id,
                    'tenSP' => $sanpham->tenSP,
                    'maSP' => $sanpham->maSP
                ],
                'will_be_deleted' => []
            ];
            
            // Đếm dữ liệu sẽ bị xóa (chỉ cho sản phẩm chưa xóa)
            $chiTietSanPhamIds = ChiTietSanPham::where('id_sp', $id)->pluck('id')->toArray();
            
            if (!empty($chiTietSanPhamIds)) {
                $chitietDonHangIds = ChiTietDonHang::whereIn('id_chitietsanpham', $chiTietSanPhamIds)->pluck('id')->toArray();
                
                if (!empty($chitietDonHangIds)) {
                    $impact['will_be_deleted']['danhgia'] = DanhGia::whereIn('id_chitietdonhang', $chitietDonHangIds)->count();
                    $impact['will_be_deleted']['chitietdonhang'] = ChiTietDonHang::whereIn('id_chitietsanpham', $chiTietSanPhamIds)->count();
                }
                
                $impact['will_be_deleted']['chitietsanpham'] = ChiTietSanPham::where('id_sp', $id)->count();
            }
            
            $impact['will_be_deleted']['binhluan'] = BinhLuan::where('id_sp', $id)->count();
            $impact['will_be_deleted']['hinhanh'] = SanPhamHinhanh::where('sanpham_id', $id)->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Kiểm tra tác động xóa thành công',
                'data' => $impact
            ]);
            
        } catch (\Exception $e) {
            Log::error('SanPhamAjax checkDeleteImpact error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra tác động xóa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test method để kiểm tra chức năng xóa
     * Chỉ dùng trong môi trường development
     */
    public function testDeleteFunctionality(Request $request)
    {
        if (config('app.env') !== 'local') {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng test chỉ khả dụng trong môi trường local'
            ], 403);
        }

        try {
            $testResults = [];
            
            // Test 1: Kiểm tra relationships
            $testResults['relationships'] = [
                'sanpham_has_chitietsanpham' => method_exists(SanPham::class, 'chitietsanpham'),
                'sanpham_has_binhluan' => method_exists(SanPham::class, 'binhluan'),
                'sanpham_has_hinhanh' => method_exists(SanPham::class, 'hinhanh'),
                'chitietsanpham_has_chitietdonhang' => method_exists(ChiTietSanPham::class, 'chitietdonhang'),
                'chitietdonhang_has_danhgia' => method_exists(ChiTietDonHang::class, 'danhgia'),
            ];

            // Test 2: Đếm số lượng records trong các bảng
            $testResults['table_counts'] = [
                'sanpham' => SanPham::active()->count(),
                'chitietsanpham' => ChiTietSanPham::count(),
                'binhluan' => BinhLuan::count(),
                'sanpham_hinhanh' => SanPhamHinhanh::count(),
                'chitietdonhang' => ChiTietDonHang::count(),
                'danhgia' => DanhGia::count(),
            ];

            // Test 3: Kiểm tra một sản phẩm có dữ liệu liên quan
            $sampleProduct = SanPham::active()->with(['chitietsanpham', 'binhluan', 'hinhanh'])->first();
            if ($sampleProduct) {
                $testResults['sample_product'] = [
                    'id' => $sampleProduct->id,
                    'tenSP' => $sampleProduct->tenSP,
                    'chitietsanpham_count' => $sampleProduct->chitietsanpham->count(),
                    'binhluan_count' => $sampleProduct->binhluan->count(),
                    'hinhanh_count' => $sampleProduct->hinhanh->count(),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Test chức năng xóa thành công',
                'data' => $testResults
            ]);

        } catch (\Exception $e) {
            Log::error('SanPhamAjax testDeleteFunctionality error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi test: ' . $e->getMessage()
            ], 500);
        }
    }
}