<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use App\Models\BinhLuan;
use App\Models\SanPhamHinhanh;
use App\Models\ChiTietDonHang;
use App\Models\DanhGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Http\Requests\StoreSanPhamRequest;
use App\Http\Requests\UpdateSanPhamRequest;
use App\Http\Requests\IndexSanPhamRequest;
use App\Services\Interfaces\SanPhamServiceInterface;



class SanPhamController extends Controller
{
    // Upload file tạm thời
    public function uploadTemp(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                if ($file->isValid()) {
                    $tempKey = 'temp_' . time() . '_' . Str::random(10);
                    $tempPath = $file->storeAs('tmp', $tempKey, 'public');
                    
                    return response()->json([
                        'success' => true,
                        'temp_key' => $tempKey,
                        'temp_path' => $tempPath
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'File không hợp lệ'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload: ' . $e->getMessage()
            ], 500);
        }
    }

    // Danh sách sản phẩm
    public function index(IndexSanPhamRequest $request)
    {
        try {
            /** @var SanPhamServiceInterface $service */
            $service = app(SanPhamServiceInterface::class);
            $data = $service->list($request->validated());

            // Đảm bảo dữ liệu được truyền đúng cách
            $sanphams = $data['items'] ?? collect([]);
            $danhmucs = $data['danhmucs'] ?? collect([]);
            $stats = $data['stats'] ?? [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'avg_price' => 0,
                'total_stock' => 0,
            ];

            return view('backend.sanpham.index', [
                'sanphams' => $sanphams,
                'danhmucs' => $danhmucs,
                'stats'    => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('SanPham index error: ' . $e->getMessage());
            
            // Trả về view với dữ liệu mặc định khi có lỗi
            return view('backend.sanpham.index', [
                'sanphams' => collect([]),
                'danhmucs' => DanhMuc::orderBy('name')->get(),
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'avg_price' => 0,
                    'total_stock' => 0,
                ]
            ])->with('error', 'Có lỗi xảy ra khi tải danh sách sản phẩm! Vui lòng thử lại sau.');
        }
    }

    // Hiển thị form thêm sản phẩm
    public function create()
    {
        /** @var SanPhamServiceInterface $service */
        $service = app(SanPhamServiceInterface::class);
        $data = $service->getCreateData();
        // Không xóa old input để giữ lại dữ liệu khi validation fail
        // session()->forget('_old_input');
        return view('backend.sanpham.create', $data);
    }

    // Lưu sản phẩm
    public function store(StoreSanPhamRequest $request)
    {
        try {
        $validated = $request->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Khi validation lỗi, upload file tạm thời và giữ lại
            $oldInput = $request->except(['_token']);
            $tempKeys = [];
            
            // Xử lý ảnh chính
            if ($request->hasFile('image_main')) {
                $imageMain = $request->file('image_main');
                if ($imageMain->isValid()) {
                    $tempKey = 'temp_main_' . time() . '_' . Str::random(10);
                    // Lưu vào storage/app/public/tmp thay vì storage/app/temp
                    $tempPath = $imageMain->storeAs('tmp', $tempKey, 'public');
                    $oldInput['image_main_name'] = $imageMain->getClientOriginalName();
                    $oldInput['temp_main_key'] = $tempKey;
                    $tempKeys[] = $tempKey;
                }
            }
            
            // Xử lý ảnh phụ
            if ($request->hasFile('image_extra')) {
                $oldInput['image_extra_names'] = [];
                $oldInput['temp_extra_keys'] = [];
                foreach ($request->file('image_extra') as $image) {
                    if ($image->isValid()) {
                        $tempKey = 'temp_extra_' . time() . '_' . Str::random(10);
                        // Lưu vào storage/app/public/tmp thay vì storage/app/temp
                        $tempPath = $image->storeAs('tmp', $tempKey, 'public');
                        $oldInput['image_extra_names'][] = $image->getClientOriginalName();
                        $oldInput['temp_extra_keys'][] = $tempKey;
                        $tempKeys[] = $tempKey;
                    }
                }
            }
            
            $oldInput['temp_keys'] = $tempKeys;
            
            return redirect()->back()->withErrors($e->errors())->withInput($oldInput);
        }

        try {
            // Ủy quyền cho Service xử lý
            /** @var SanPhamServiceInterface $service */
            $service = app(SanPhamServiceInterface::class);

            // xử lý upload: sinh đường dẫn relative để service lưu DB
            $imageMainPath = null;
            if ($request->hasFile('image_main')) {
                $image = $request->file('image_main');
                $destination = public_path('backend/images');
                if (!is_dir($destination)) { @mkdir($destination, 0755, true); }
                $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                $image->move($destination, $filename);
                $imageMainPath = 'backend/images/' . $filename;
            } elseif ($request->filled('image_main_name')) {
                // Giữ lại ảnh cũ khi submit lại: nếu phía client gửi lại tên file, cố gắng lấy trong thư mục backend/images
                $existing = public_path('backend/images/' . $request->input('image_main_name'));
                if (file_exists($existing)) {
                    $imageMainPath = 'backend/images/' . $request->input('image_main_name');
                }
            } elseif ($request->filled('temp_main_key')) {
                // Move từ tmp sang backend/images
                $tmpPath = 'public/tmp/' . $request->input('temp_main_key');
                if (Storage::exists($tmpPath)) {
                    $ext = pathinfo($request->input('temp_main_key'), PATHINFO_EXTENSION);
                    if (empty($ext)) {
                        $mime = Storage::mimeType($tmpPath) ?: 'image/jpeg';
                        $map = [
                            'image/jpeg' => 'jpg',
                            'image/jpg' => 'jpg',
                            'image/png' => 'png',
                            'image/gif' => 'gif',
                            'image/webp' => 'webp'
                        ];
                        $ext = $map[$mime] ?? 'jpg';
                    }
                    $filename = time() . '_' . Str::random(8) . '.' . $ext;
                    $publicDir = public_path('backend/images');
                    if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
                    $publicDest = $publicDir . DIRECTORY_SEPARATOR . $filename;
                    // copy file từ storage sang public
                    $contents = Storage::get($tmpPath);
                    file_put_contents($publicDest, $contents);
                    Storage::delete($tmpPath);
                    $imageMainPath = 'backend/images/' . $filename;
                }
            }
            $extraPaths = [];
            if ($request->hasFile('image_extra')) {
                $destination = public_path('backend/images');
                if (!is_dir($destination)) { @mkdir($destination, 0755, true); }
                foreach ($request->file('image_extra') as $image) {
                    if ($image->isValid()) {
                        $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                        $image->move($destination, $filename);
                        $extraPaths[] = 'backend/images/' . $filename;
                    }
                }
            } elseif ($request->filled('temp_extra_keys')) {
                // Xử lý ảnh phụ từ file tạm
                $tempExtraKeys = $request->input('temp_extra_keys', []);
                if (is_array($tempExtraKeys)) {
                    foreach ($tempExtraKeys as $key) {
                        $tmpPath = 'public/tmp/' . $key;
                        if (Storage::exists($tmpPath)) {
                            $ext = pathinfo($key, PATHINFO_EXTENSION);
                            if (empty($ext)) {
                                $mime = Storage::mimeType($tmpPath) ?: 'image/jpeg';
                                $map = [
                                    'image/jpeg' => 'jpg',
                                    'image/jpg' => 'jpg',
                                    'image/png' => 'png',
                                    'image/gif' => 'gif',
                                    'image/webp' => 'webp'
                                ];
                                $ext = $map[$mime] ?? 'jpg';
                            }
                            $filename = time() . '_' . Str::random(8) . '.' . $ext;
                            $publicDir = public_path('backend/images');
                            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
                            $publicDest = $publicDir . DIRECTORY_SEPARATOR . $filename;
                            $contents = Storage::get($tmpPath);
                            file_put_contents($publicDest, $contents);
                            Storage::delete($tmpPath);
                            $extraPaths[] = 'backend/images/' . $filename;
                        }
                    }
                }
            }

            $payload = $validated;
            $payload['image_main_path'] = $imageMainPath;
            $payload['image_extra_paths'] = $extraPaths;
            $payload['variants'] = $request->input('variants', []);
            
            // Debug logging
            Log::info('SanPhamController store: variants = ' . json_encode($payload['variants']));
            Log::info('SanPhamController store: full payload = ' . json_encode($payload));

            $sanphamId = $service->create($payload);

            
            // Kiểm tra nếu là AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thêm sản phẩm thành công!',
                    'data' => [ 'id' => $sanphamId ]
                ]);
            }
            
            return redirect()->route('sanpham.index')->with('success', 'Thêm sản phẩm thành công!');
        } catch (\Exception $e) {
            Log::error('SanPham store error: ' . $e->getMessage());
            
            // Kiểm tra nếu là AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tạo sản phẩm: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Không thể tạo sản phẩm: ' . $e->getMessage());
        }
    }


    // Form sửa sản phẩm
    public function edit($id)
    {
        /** @var SanPhamServiceInterface $service */
        $service = app(SanPhamServiceInterface::class);
        $data = $service->getEditData((int)$id);
        return view('backend.sanpham.update', $data);
    }

    // Cập nhật sản phẩm
    public function update(UpdateSanPhamRequest $request, $id)
    {
        try {
        $validated = $request->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Khi validation lỗi, upload file tạm thời và giữ lại
            $oldInput = $request->except(['_token', '_method']);
            $tempKeys = [];
            
            // Xử lý ảnh chính
            if ($request->hasFile('image_main')) {
                $imageMain = $request->file('image_main');
                if ($imageMain->isValid()) {
                    $tempKey = 'temp_main_' . time() . '_' . Str::random(10);
                    // Lưu vào storage/app/public/tmp thay vì storage/app/temp
                    $tempPath = $imageMain->storeAs('tmp', $tempKey, 'public');
                    $oldInput['image_main_name'] = $imageMain->getClientOriginalName();
                    $oldInput['temp_main_key'] = $tempKey;
                    $tempKeys[] = $tempKey;
                }
            }
            
            // Xử lý ảnh phụ
            if ($request->hasFile('image_extra')) {
                $oldInput['image_extra_names'] = [];
                $oldInput['temp_extra_keys'] = [];
                foreach ($request->file('image_extra') as $image) {
                    if ($image->isValid()) {
                        $tempKey = 'temp_extra_' . time() . '_' . Str::random(10);
                        // Lưu vào storage/app/public/tmp thay vì storage/app/temp
                        $tempPath = $image->storeAs('tmp', $tempKey, 'public');
                        $oldInput['image_extra_names'][] = $image->getClientOriginalName();
                        $oldInput['temp_extra_keys'][] = $tempKey;
                        $tempKeys[] = $tempKey;
                    }
                }
            }
            
            $oldInput['temp_keys'] = $tempKeys;
            
            return redirect()->back()->withErrors($e->errors())->withInput($oldInput);
        }

        try {
            /** @var SanPhamServiceInterface $service */
            $service = app(SanPhamServiceInterface::class);

            // xử lý upload: sinh đường dẫn relative để service lưu DB
            $imageMainPath = null;
            if ($request->hasFile('image_main')) {
                $image = $request->file('image_main');
                $destination = public_path('backend/images');
                if (!is_dir($destination)) { @mkdir($destination, 0755, true); }
                $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                $image->move($destination, $filename);
                $imageMainPath = 'backend/images/' . $filename;
            }
            $extraPaths = [];
            if ($request->hasFile('image_extra')) {
                $destination = public_path('backend/images');
                if (!is_dir($destination)) { @mkdir($destination, 0755, true); }
                foreach ($request->file('image_extra') as $image) {
                    if ($image->isValid()) {
                        $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                        $image->move($destination, $filename);
                        $extraPaths[] = 'backend/images/' . $filename;
                    }
                }
            } elseif ($request->filled('temp_extra_keys')) {
                // Xử lý ảnh phụ từ file tạm
                $tempExtraKeys = $request->input('temp_extra_keys', []);
                if (is_array($tempExtraKeys)) {
                    foreach ($tempExtraKeys as $key) {
                        $tmpPath = 'public/tmp/' . $key;
                        if (Storage::exists($tmpPath)) {
                            $ext = pathinfo($key, PATHINFO_EXTENSION);
                            if (empty($ext)) {
                                $mime = Storage::mimeType($tmpPath) ?: 'image/jpeg';
                                $map = [
                                    'image/jpeg' => 'jpg',
                                    'image/jpg' => 'jpg',
                                    'image/png' => 'png',
                                    'image/gif' => 'gif',
                                    'image/webp' => 'webp'
                                ];
                                $ext = $map[$mime] ?? 'jpg';
                            }
                            $filename = time() . '_' . Str::random(8) . '.' . $ext;
                            $publicDir = public_path('backend/images');
                            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
                            $publicDest = $publicDir . DIRECTORY_SEPARATOR . $filename;
                            $contents = Storage::get($tmpPath);
                            file_put_contents($publicDest, $contents);
                            Storage::delete($tmpPath);
                            $extraPaths[] = 'backend/images/' . $filename;
                        }
                    }
                }
            }

            $payload = $validated;
            $payload['image_main_path'] = $imageMainPath;
            $payload['image_extra_paths'] = $extraPaths;
            $payload['variants'] = $request->input('variants', []);
            $payload['deleted_images'] = $request->input('deleted_images', []);

            // Debug logging
            Log::info('SanPhamController update: deleted_images = ' . json_encode($payload['deleted_images']));
            Log::info('SanPhamController update: full payload = ' . json_encode($payload));

            try {
            $service->update((int)$id, $payload);
                Log::info('SanPhamController update: Service update completed successfully');
            } catch (\Exception $e) {
                Log::error('SanPhamController update: Service update failed: ' . $e->getMessage());
                Log::error('SanPhamController update: Stack trace: ' . $e->getTraceAsString());
                throw $e;
            }

            return redirect()->route('sanpham.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            Log::error('SanPham update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Không thể cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Kiểm tra sản phẩm có tồn tại không (chỉ sản phẩm chưa xóa)
            $sanpham = SanPham::active()->findOrFail($id);
            
            Log::info('SanPhamController destroy: Starting deletion for product ID: ' . $id);
            
            // Xóa mềm theo thứ tự để tránh vi phạm khóa ngoại
            $deletedCounts = $this->softDeleteProductRelatedData($id);
            
            // Xóa sản phẩm chính
            $sanpham->delete();
            
            DB::commit();
            
            Log::info('SanPhamController destroy: Successfully deleted product ID: ' . $id, $deletedCounts);
            
            return redirect()->route('sanpham.index')->with('success', 'Xóa sản phẩm thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPhamController destroy error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể xóa sản phẩm: ' . $e->getMessage());
        }
    }

    // Xóa sản phẩm qua POST request (để tương thích với form POST)
    public function destroyPost($id)
    {
        return $this->destroy($id);
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

    /**
     * Xóa mềm dữ liệu liên quan để có thể phục hồi
     */
    private function softDeleteProductRelatedData(int $productId): array
    {
        $deletedCounts = [];
        try {
            // 1. Chi tiết sản phẩm
            $chiTietSanPhamIds = ChiTietSanPham::where('id_sp', $productId)->pluck('id')->toArray();
            if (!empty($chiTietSanPhamIds)) {
                // Đánh giá -> Chi tiết đơn hàng
                $chitietDonHangIds = ChiTietDonHang::whereIn('id_chitietsanpham', $chiTietSanPhamIds)->pluck('id')->toArray();
                if (!empty($chitietDonHangIds)) {
                    $deletedCounts['danhgia'] = DanhGia::whereIn('id_chitietdonhang', $chitietDonHangIds)->delete();
                    $deletedCounts['chitietdonhang'] = ChiTietDonHang::whereIn('id_chitietsanpham', $chiTietSanPhamIds)->delete();
                }
                $deletedCounts['chitietsanpham'] = ChiTietSanPham::where('id_sp', $productId)->delete();
            }

            // 2. Bình luận
            $deletedCounts['binhluan'] = BinhLuan::where('id_sp', $productId)->delete();

            // 3. Hình ảnh sản phẩm
            $deletedCounts['hinhanh'] = SanPhamHinhanh::where('sanpham_id', $productId)->delete();

        } catch (\Exception $e) {
            Log::error("Error soft deleting related data for product {$productId}: " . $e->getMessage());
            throw $e;
        }
        return $deletedCounts;
    }

    /**
     * Phục hồi dữ liệu liên quan đến sản phẩm
     */
    private function restoreProductRelatedData(int $productId): array
    {
        $restoredCounts = [];
        
        try {
            // 1. Phục hồi hình ảnh sản phẩm (nếu có)
            $hinhanhCount = SanPhamHinhanh::withTrashed()
                ->where('sanpham_id', $productId)
                ->restore();
            $restoredCounts['hinhanh'] = $hinhanhCount;
            
            // 2. Phục hồi chi tiết sản phẩm (nếu có)
            $chitietsanphamCount = ChiTietSanPham::withTrashed()
                ->where('id_sp', $productId)
                ->restore();
            $restoredCounts['chitietsanpham'] = $chitietsanphamCount;
            
            // 3. Phục hồi bình luận (nếu có)
            $binhluanCount = BinhLuan::withTrashed()
                ->where('id_sp', $productId)
                ->restore();
            $restoredCounts['binhluan'] = $binhluanCount;
            
            // 4. Phục hồi hình ảnh sản phẩm (nếu có)
            $hinhanhCount = SanPhamHinhanh::withTrashed()
                ->where('sanpham_id', $productId)
                ->restore();
            $restoredCounts['hinhanh'] = $hinhanhCount;
            
            // 5. Phục hồi chi tiết đơn hàng và đánh giá (nếu có)
            $chiTietSanPhamIds = ChiTietSanPham::where('id_sp', $productId)->pluck('id')->toArray();
            
            if (!empty($chiTietSanPhamIds)) {
                // ChiTietDonHang không có soft delete, chỉ cần kiểm tra tồn tại
                $chitietdonhangCount = ChiTietDonHang::whereIn('id_chitietsanpham', $chiTietSanPhamIds)->count();
                $restoredCounts['chitietdonhang'] = $chitietdonhangCount;
                
                $chitietDonHangIds = ChiTietDonHang::whereIn('id_chitietsanpham', $chiTietSanPhamIds)->pluck('id')->toArray();
                
                if (!empty($chitietDonHangIds)) {
                    // DanhGia không có soft delete, chỉ cần kiểm tra tồn tại
                    $danhgiaCount = DanhGia::whereIn('id_chitietdonhang', $chitietDonHangIds)->count();
                    $restoredCounts['danhgia'] = $danhgiaCount;
                }
            }
            
            Log::info("Restored related data for product {$productId}:", $restoredCounts);
            
        } catch (\Exception $e) {
            Log::error("Error restoring related data for product {$productId}: " . $e->getMessage());
            throw $e;
        }
        
        return $restoredCounts;
    }

    // Toggle trạng thái (redirect, nút bấm)
    public function toggle($id)
    {
        /** @var SanPhamServiceInterface $service */
        $service = app(SanPhamServiceInterface::class);
        $service->toggle((int)$id);
        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    // Tìm kiếm (filter)
    public function search(IndexSanPhamRequest $request)
    {
        /** @var SanPhamServiceInterface $service */
        $service = app(SanPhamServiceInterface::class);
        $data = $service->list($request->validated());

        return view('backend.sanpham.index', [
            'sanphams' => $data['items'],
            'danhmucs' => $data['danhmucs'],
            'stats'    => $data['stats'],
        ]);
    }

    // Bulk actions - giữ lại để tương thích ngược
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $idsRaw = $request->input('ids', '');
        $ids = is_array($idsRaw) ? $idsRaw : array_filter(explode(',', $idsRaw));

        if (empty($ids)) {
            $message = 'Không có sản phẩm nào được chọn.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        try {
            /** @var SanPhamServiceInterface $service */
            $service = app(SanPhamServiceInterface::class);
            $result = $service->bulk((string)$action, $ids);
            $message = $result['message'] ?? 'Thao tác hoàn tất';

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $message, 'updated' => ($result['updated'] ?? [])]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Bulk action error: '.$e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Lỗi khi thực hiện thao tác.'], 500);
            }
            return redirect()->back()->with('error', 'Lỗi khi thực hiện thao tác.');
        }
    }

    // Cập nhật trạng thái kinh doanh cho nhiều sản phẩm
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:sanpham,id',
            'status' => 'required|in:0,1'
        ]);

        $ids = $request->input('ids');
        $status = $request->input('status');

        try {
            DB::beginTransaction();
            
            $updatedCount = SanPham::whereIn('id', $ids)->update(['trangthai' => $status]);
            
            DB::commit();
            
            $statusText = $status == 1 ? 'kinh doanh' : 'ngừng kinh doanh';
            $message = "Đã cập nhật trạng thái {$statusText} cho {$updatedCount} sản phẩm.";
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated_count' => $updatedCount
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk status update error: ' . $e->getMessage());
            
            $errorMessage = 'Không thể cập nhật trạng thái sản phẩm: ' . $e->getMessage();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    // Xóa nhiều sản phẩm
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:sanpham,id'
        ]);

        $ids = $request->input('ids');

        try {
            DB::beginTransaction();
            
            $deletedCount = 0;
            $errors = [];
            
            foreach ($ids as $id) {
                try {
                    // Kiểm tra sản phẩm có tồn tại không (chỉ sản phẩm chưa xóa)
                    $sanpham = SanPham::active()->findOrFail($id);
                    
                    // Xóa mềm dữ liệu liên quan trước khi xóa sản phẩm chính
                    $this->softDeleteProductRelatedData($id);
                    
                    // Xóa sản phẩm chính
                    $sanpham->delete();
                    $deletedCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Sản phẩm ID {$id}: " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            $message = "Đã xóa thành công {$deletedCount} sản phẩm.";
            if (!empty($errors)) {
                $message .= " Lỗi: " . implode(', ', $errors);
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount,
                    'errors' => $errors
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete error: ' . $e->getMessage());
            
            $errorMessage = 'Không thể xóa sản phẩm: ' . $e->getMessage();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }
    }

   

    // API endpoints
    public function apiIndex(IndexSanPhamRequest $request)
    {
        try {
            /** @var SanPhamServiceInterface $service */
            $service = app(SanPhamServiceInterface::class);
            $data = $service->list($request->validated());

            return response()->json([
                'success' => true,
                'data' => $data['items'],
                'message' => 'Lấy danh sách sản phẩm thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('SanPham API index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra!'
            ], 500);
        }
    }

    public function apiShow($id)
    {
        try {
            /** @var SanPhamServiceInterface $service */
            $service = app(SanPhamServiceInterface::class);
            $sanpham = $service->getById((int)$id);
            
            return response()->json([
                'success' => true,
                'data' => $sanpham,
                'message' => 'Lấy thông tin sản phẩm thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('SanPham API show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm!'
            ], 404);
        }
    }

    // Hiển thị chi tiết sản phẩm (trang HTML)
    public function show($id)
    {
        try {
            /** @var SanPhamServiceInterface $service */
            $service = app(SanPhamServiceInterface::class);
            $sanpham = $service->getById((int)$id);
            return view('backend.sanpham.show', compact('sanpham'));
        } catch (\Exception $e) {
            Log::error('SanPham show error: ' . $e->getMessage());
            return redirect()->route('sanpham.index')->with('error', 'Không thể tải chi tiết sản phẩm!');
        }
    }

    // Thống kê
    public function statistics()
    {
        try {
            /** @var SanPhamServiceInterface $service */
            $service = app(SanPhamServiceInterface::class);
            $stats = $service->getStatistics();
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Lấy thống kê thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('SanPham statistics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Trang thống kê sản phẩm (KPI + charts)
    public function statisticsPage(Request $request)
    {
        $range = (int) $request->get('range', 30);
        if (!in_array($range, [7, 30, 90])) { $range = 30; }
        $stats = [
            'total' => SanPham::count(),
            'active' => SanPham::where('trangthai', 1)->count(),
            'inactive' => SanPham::where('trangthai', 0)->count(),
            // Tổng tồn kho lấy từ bảng chi tiết (cột soLuong)
            'total_stock' => (int) \App\Models\ChiTietSanPham::sum('soLuong'),
        ];

        // Dữ liệu chart theo khoảng chọn
        $labels = [];
        $data = [];
        for ($i = $range-1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $labels[] = $date->format('d/m');
            $data[] = SanPham::whereDate('created_at', $date)->count();
        }

        return view('backend.sanpham.statistics', compact('stats','labels','data','range'));
    }

    // Xóa vĩnh viễn sản phẩm
    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();
            
            // Xóa dữ liệu liên quan trước
            $this->deleteProductRelatedData($id);
            
            // Xóa sản phẩm vĩnh viễn
            $sanpham = SanPham::withTrashed()->findOrFail($id);
            $sanpham->forceDelete();
            
            DB::commit();
            
            return redirect()->route('sanpham.index')->with('success', 'Xóa vĩnh viễn sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPham forceDelete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể xóa vĩnh viễn sản phẩm: ' . $e->getMessage());
        }
    }

    // Phục hồi sản phẩm
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            
            // Lấy sản phẩm đã xóa mềm
            $sanpham = SanPham::withTrashed()->findOrFail($id);
            
            // Kiểm tra và phục hồi dữ liệu liên quan
            $this->restoreProductRelatedData($id);
            
            // Phục hồi sản phẩm chính
            $sanpham->restore();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Phục hồi sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPham restore error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể phục hồi sản phẩm: ' . $e->getMessage());
        }
    }

    // Phục hồi tất cả sản phẩm
    public function restoreAll()
    {
        try {
            DB::beginTransaction();
            
            // Lấy danh sách ID sản phẩm đã xóa
            $trashedProductIds = SanPham::onlyTrashed()->pluck('id')->toArray();
            
            if (empty($trashedProductIds)) {
                return redirect()->back()->with('warning', 'Không có sản phẩm nào để phục hồi!');
            }
            
            $totalRestored = 0;
            $restoredCounts = [];
            
            // Phục hồi từng sản phẩm và dữ liệu liên quan
            foreach ($trashedProductIds as $productId) {
                $restoredCounts[$productId] = $this->restoreProductRelatedData($productId);
                $totalRestored++;
            }
            
            // Phục hồi sản phẩm chính
            $count = SanPham::onlyTrashed()->restore();
            
            DB::commit();
            
            Log::info("Restored all products: {$count} products, details:", $restoredCounts);
            
            return redirect()->route('sanpham.index')->with('success', "Đã phục hồi {$count} sản phẩm và tất cả dữ liệu liên quan!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPham restoreAll error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể phục hồi tất cả sản phẩm: ' . $e->getMessage());
        }
    }

    // Hiển thị danh sách sản phẩm đã xóa mềm
    public function trashed()
    {
        try {
            $trashedProducts = SanPham::onlyTrashed()
                ->with(['danhmuc', 'hinhanh', 'chitietsanpham'])
                ->orderBy('deleted_at', 'desc')
                ->paginate(20);
            
            $stats = [
                'total_trashed' => SanPham::onlyTrashed()->count(),
                'total_active' => SanPham::count(),
                'total_all' => SanPham::withTrashed()->count()
            ];
            
            return view('backend.sanpham.trashed', compact('trashedProducts', 'stats'));
        } catch (\Exception $e) {
            Log::error('SanPham trashed error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể tải danh sách sản phẩm đã xóa!');
        }
    }

    // Xóa vĩnh viễn tất cả sản phẩm đã xóa mềm
    public function forceDeleteAll()
    {
        try {
            DB::beginTransaction();
            
            // Lấy tất cả sản phẩm đã xóa mềm
            $trashedProducts = SanPham::onlyTrashed()->get();
            
            foreach ($trashedProducts as $product) {
                // Xóa dữ liệu liên quan
                $this->deleteProductRelatedData($product->id);
                // Xóa vĩnh viễn
                $product->forceDelete();
            }
            
            DB::commit();
            
            $count = $trashedProducts->count();
            return redirect()->route('sanpham.index')->with('success', "Đã xóa vĩnh viễn {$count} sản phẩm!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPham forceDeleteAll error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể xóa vĩnh viễn tất cả sản phẩm: ' . $e->getMessage());
        }
    }
}
