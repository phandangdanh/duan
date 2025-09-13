<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Models\SanPhamMausacImage;
use App\Models\ChiTietSanPham;
use App\Models\MauSac;
use App\Models\Size;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SanPhamColorController extends Controller
{
    /**
     * Hiển thị form tạo sản phẩm mới với cấu trúc theo màu
     */
    public function create()
    {
        try {
            $danhmucs = DanhMuc::active()->orderBy('name')->get();
            $mausacs = MauSac::orderBy('mota')->get();
            $sizes = Size::orderBy('mota')->get();

            return view('backend.sanpham.create_new', compact('danhmucs', 'mausacs', 'sizes'));
        } catch (\Exception $e) {
            Log::error('SanPhamColorController create error: ' . $e->getMessage());
            return redirect()->route('sanpham.index')->with('error', 'Không thể tải form tạo sản phẩm!');
        }
    }

    /**
     * Lưu sản phẩm với cấu trúc theo màu
     */
    public function store(Request $request)
    {
        $request->validate([
            'tenSP' => 'required|string|max:255',
            'maSP' => 'nullable|string|max:100|unique:sanpham,maSP',
            'id_danhmuc' => 'required|exists:danhmuc,id',
            'moTa' => 'nullable|string',
            'trangthai' => 'required|in:0,1',
            'color_variants' => 'required|array|min:1',
            'color_variants.*.mausac_id' => 'required|exists:mausac,id',
            'color_variants.*.ten' => 'nullable|string|max:255',
            'color_variants.*.images.main' => 'required|image|max:2048',
            'color_variants.*.images.extra.*' => 'nullable|image|max:2048',
            'color_variants.*.sizes' => 'required|array|min:1',
            'color_variants.*.sizes.*.size_id' => 'required|exists:size,id',
            'color_variants.*.sizes.*.soLuong' => 'required|integer|min:0',
            'color_variants.*.sizes.*.gia' => 'required|numeric|min:0',
            'color_variants.*.sizes.*.gia_khuyenmai' => 'nullable|numeric|min:0',
        ], [
            'tenSP.required' => 'Tên sản phẩm không được để trống',
            'id_danhmuc.required' => 'Vui lòng chọn danh mục',
            'color_variants.required' => 'Vui lòng thêm ít nhất một màu sắc',
            'color_variants.*.mausac_id.required' => 'Vui lòng chọn màu sắc',
            'color_variants.*.images.main.required' => 'Vui lòng chọn ảnh chính cho màu',
            'color_variants.*.sizes.required' => 'Mỗi màu phải có ít nhất một size',
            'color_variants.*.sizes.*.size_id.required' => 'Vui lòng chọn size',
            'color_variants.*.sizes.*.soLuong.required' => 'Số lượng không được để trống',
            'color_variants.*.sizes.*.gia.required' => 'Giá không được để trống',
        ]);

        DB::beginTransaction();
        try {
            // Tạo mã sản phẩm nếu không có
            $maSP = $request->input('maSP');
            if (empty($maSP)) {
                $maSP = 'SP' . date('Ymd') . Str::random(4);
            }

            // Tạo sản phẩm chính
            $sanpham = SanPham::create([
                'maSP' => $maSP,
                'tenSP' => $request->input('tenSP'),
                'id_danhmuc' => $request->input('id_danhmuc'),
                'moTa' => $request->input('moTa'),
                'trangthai' => $request->input('trangthai', 1),
            ]);

            Log::info('Created product: ' . $sanpham->id);

            // Xử lý từng màu sắc
            foreach ($request->input('color_variants') as $colorIndex => $colorVariant) {
                $mausacId = $colorVariant['mausac_id'];
                $tenMau = $colorVariant['ten'] ?? '';

                Log::info("Processing color variant {$colorIndex}: mausac_id={$mausacId}, ten={$tenMau}");

                // Xử lý ảnh chính của màu
                if ($request->hasFile("color_variants.{$colorIndex}.images.main")) {
                    $mainImage = $request->file("color_variants.{$colorIndex}.images.main");
                    $mainImagePath = $this->uploadImage($mainImage, 'products/colors');
                    
                    // Lưu ảnh chính
                    SanPhamMausacImage::create([
                        'sanpham_id' => $sanpham->id,
                        'mausac_id' => $mausacId,
                        'url' => $mainImagePath,
                        'is_default' => true,
                        'mota' => 'Ảnh chính của màu ' . $tenMau,
                    ]);
                }

                // Xử lý ảnh phụ của màu
                if ($request->hasFile("color_variants.{$colorIndex}.images.extra")) {
                    $extraImages = $request->file("color_variants.{$colorIndex}.images.extra");
                    foreach ($extraImages as $extraImage) {
                        $extraImagePath = $this->uploadImage($extraImage, 'products/colors');
                        
                        SanPhamMausacImage::create([
                            'sanpham_id' => $sanpham->id,
                            'mausac_id' => $mausacId,
                            'url' => $extraImagePath,
                            'is_default' => false,
                            'mota' => 'Ảnh phụ của màu ' . $tenMau,
                        ]);
                    }
                }

                // Xử lý các size của màu
                foreach ($colorVariant['sizes'] as $sizeIndex => $sizeData) {
                    ChiTietSanPham::create([
                        'id_sp' => $sanpham->id,
                        'id_mausac' => $mausacId,
                        'id_size' => $sizeData['size_id'],
                        'soLuong' => $sizeData['soLuong'],
                        'tenSp' => $tenMau ?: $sanpham->tenSP,
                        'gia' => $sizeData['gia'],
                        'gia_khuyenmai' => $sizeData['gia_khuyenmai'] ?? null,
                    ]);
                }
            }

            DB::commit();
            
            Log::info('Successfully created product with color variants: ' . $sanpham->id);
            
            return redirect()->route('sanpham.index')->with('success', 'Thêm sản phẩm thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPhamColorController store error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Không thể tạo sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Upload ảnh và trả về đường dẫn
     */
    private function uploadImage($file, $directory = 'products')
    {
        $destination = public_path($directory);
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);
        
        return $directory . '/' . $filename;
    }

    /**
     * Hiển thị chi tiết sản phẩm theo màu
     */
    public function show($id)
    {
        try {
            $sanpham = SanPham::with([
                'danhmuc',
                'chitietsanpham.mausac',
                'chitietsanpham.size',
                'sanphamMausacImages.mausac'
            ])->findOrFail($id);

            // Nhóm chi tiết theo màu
            $colorVariants = $sanpham->chitietsanpham->groupBy('id_mausac');
            
            // Nhóm ảnh theo màu
            $colorImages = $sanpham->sanphamMausacImages->groupBy('mausac_id');

            return view('backend.sanpham.show_color', compact('sanpham', 'colorVariants', 'colorImages'));
        } catch (\Exception $e) {
            Log::error('SanPhamColorController show error: ' . $e->getMessage());
            return redirect()->route('sanpham.index')->with('error', 'Không thể tải chi tiết sản phẩm!');
        }
    }

    /**
     * Form chỉnh sửa sản phẩm theo màu
     */
    public function edit($id)
    {
        try {
            $sanpham = SanPham::with([
                'danhmuc',
                'chitietsanpham.mausac',
                'chitietsanpham.size',
                'sanphamMausacImages.mausac'
            ])->findOrFail($id);

            $danhmucs = DanhMuc::active()->orderBy('name')->get();
            $mausacs = MauSac::orderBy('mota')->get();
            $sizes = Size::orderBy('mota')->get();

            // Nhóm dữ liệu theo màu để hiển thị trong form
            $colorVariants = $sanpham->chitietsanpham->groupBy('id_mausac');
            $colorImages = $sanpham->sanphamMausacImages->groupBy('mausac_id');

            return view('backend.sanpham.edit_color', compact(
                'sanpham', 'danhmucs', 'mausacs', 'sizes', 'colorVariants', 'colorImages'
            ));
        } catch (\Exception $e) {
            Log::error('SanPhamColorController edit error: ' . $e->getMessage());
            return redirect()->route('sanpham.index')->with('error', 'Không thể tải form chỉnh sửa!');
        }
    }

    /**
     * Cập nhật sản phẩm theo màu
     */
    public function update(Request $request, $id)
    {
        // Validation tương tự như store nhưng không bắt buộc ảnh mới
        $request->validate([
            'tenSP' => 'required|string|max:255',
            'maSP' => 'nullable|string|max:100|unique:sanpham,maSP,' . $id,
            'id_danhmuc' => 'required|exists:danhmuc,id',
            'moTa' => 'nullable|string',
            'trangthai' => 'required|in:0,1',
            'color_variants' => 'required|array|min:1',
            'color_variants.*.mausac_id' => 'required|exists:mausac,id',
            'color_variants.*.ten' => 'nullable|string|max:255',
            'color_variants.*.images.main' => 'nullable|image|max:2048',
            'color_variants.*.images.extra.*' => 'nullable|image|max:2048',
            'color_variants.*.sizes' => 'required|array|min:1',
            'color_variants.*.sizes.*.size_id' => 'required|exists:size,id',
            'color_variants.*.sizes.*.soLuong' => 'required|integer|min:0',
            'color_variants.*.sizes.*.gia' => 'required|numeric|min:0',
            'color_variants.*.sizes.*.gia_khuyenmai' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $sanpham = SanPham::findOrFail($id);
            
            // Cập nhật thông tin cơ bản
            $sanpham->update([
                'tenSP' => $request->input('tenSP'),
                'maSP' => $request->input('maSP'),
                'id_danhmuc' => $request->input('id_danhmuc'),
                'moTa' => $request->input('moTa'),
                'trangthai' => $request->input('trangthai', 1),
            ]);

            // Xóa dữ liệu cũ
            ChiTietSanPham::where('id_sp', $sanpham->id)->delete();
            SanPhamMausacImage::where('sanpham_id', $sanpham->id)->delete();

            // Thêm dữ liệu mới (tương tự như store)
            foreach ($request->input('color_variants') as $colorIndex => $colorVariant) {
                $mausacId = $colorVariant['mausac_id'];
                $tenMau = $colorVariant['ten'] ?? '';

                // Xử lý ảnh chính của màu
                if ($request->hasFile("color_variants.{$colorIndex}.images.main")) {
                    $mainImage = $request->file("color_variants.{$colorIndex}.images.main");
                    $mainImagePath = $this->uploadImage($mainImage, 'products/colors');
                    
                    SanPhamMausacImage::create([
                        'sanpham_id' => $sanpham->id,
                        'mausac_id' => $mausacId,
                        'url' => $mainImagePath,
                        'is_default' => true,
                        'mota' => 'Ảnh chính của màu ' . $tenMau,
                    ]);
                }

                // Xử lý ảnh phụ của màu
                if ($request->hasFile("color_variants.{$colorIndex}.images.extra")) {
                    $extraImages = $request->file("color_variants.{$colorIndex}.images.extra");
                    foreach ($extraImages as $extraImage) {
                        $extraImagePath = $this->uploadImage($extraImage, 'products/colors');
                        
                        SanPhamMausacImage::create([
                            'sanpham_id' => $sanpham->id,
                            'mausac_id' => $mausacId,
                            'url' => $extraImagePath,
                            'is_default' => false,
                            'mota' => 'Ảnh phụ của màu ' . $tenMau,
                        ]);
                    }
                }

                // Xử lý các size của màu
                foreach ($colorVariant['sizes'] as $sizeIndex => $sizeData) {
                    ChiTietSanPham::create([
                        'id_sp' => $sanpham->id,
                        'id_mausac' => $mausacId,
                        'id_size' => $sizeData['size_id'],
                        'soLuong' => $sizeData['soLuong'],
                        'tenSp' => $tenMau ?: $sanpham->tenSP,
                        'gia' => $sizeData['gia'],
                        'gia_khuyenmai' => $sizeData['gia_khuyenmai'] ?? null,
                    ]);
                }
            }

            DB::commit();
            
            return redirect()->route('sanpham.index')->with('success', 'Cập nhật sản phẩm thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SanPhamColorController update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Không thể cập nhật sản phẩm: ' . $e->getMessage());
        }
    }
}
