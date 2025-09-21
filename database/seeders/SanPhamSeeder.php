<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SanPham;
use App\Models\SanPhamHinhanh;
use App\Models\ChiTietSanPham;
use App\Models\MauSac;
use App\Models\Size;
use App\Models\DanhMuc;

class SanPhamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh mục từ dữ liệu thực tế
        $danhMucBanAn = DanhMuc::where('name', 'Bàn ăn kính cường lực')->first();
        $danhMucBanHoc = DanhMuc::where('name', 'Bàn học sinh')->first();
        $danhMucBanLamViec = DanhMuc::where('name', 'Bàn làm việc văn phòng')->first();
        $danhMucBanGiamDoc = DanhMuc::where('name', 'Bàn giám đốc')->first();
        $danhMucGiuongTang = DanhMuc::where('name', 'Giường tầng')->first();
        $danhMucGiuongTreEm = DanhMuc::where('name', 'Giường trẻ em')->first();
        $danhMucTuQuanAo = DanhMuc::where('name', 'Tủ quần áo gỗ sồi')->first();
        $danhMucTuQuanAoLim = DanhMuc::where('name', 'Tủ quần áo gỗ lim')->first();

        // Tạo màu sắc
        $mauSac1 = MauSac::firstOrCreate(['ten' => 'Đỏ', 'mota' => 'Màu đỏ']);
        $mauSac2 = MauSac::firstOrCreate(['ten' => 'Xanh', 'mota' => 'Màu xanh']);
        $mauSac3 = MauSac::firstOrCreate(['ten' => 'Vàng', 'mota' => 'Màu vàng']);
        $mauSac4 = MauSac::firstOrCreate(['ten' => 'Đen', 'mota' => 'Màu đen']);
        $mauSac5 = MauSac::firstOrCreate(['ten' => 'Trắng', 'mota' => 'Màu trắng']);
        $mauSac6 = MauSac::firstOrCreate(['ten' => 'Nâu', 'mota' => 'Màu nâu gỗ']);

        // Tạo size
        $size1 = Size::firstOrCreate(['ten' => 'S', 'mota' => 'Size S']);
        $size2 = Size::firstOrCreate(['ten' => 'M', 'mota' => 'Size M']);
        $size3 = Size::firstOrCreate(['ten' => 'L', 'mota' => 'Size L']);
        $size4 = Size::firstOrCreate(['ten' => 'XL', 'mota' => 'Size XL']);

        // Sản phẩm 1: Bàn ăn kính cường lực
        $sanPham1 = SanPham::create([
            'maSP' => 'BA001',
            'tenSP' => 'Bàn ăn kính cường lực 6 ghế',
            'id_danhmuc' => $danhMucBanAn->id,
            'moTa' => 'Bàn ăn mặt kính cường lực hiện đại, dễ lau chùi, phù hợp gia đình 6 người',
            'trangthai' => 1,
            'base_price' => 2500000,
            'base_sale_price' => 2000000,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham1->id,
            'url' => 'products/ban-an-kinh-cuong-luc.jpg',
            'is_default' => true,
            'mota' => 'Bàn ăn kính cường lực chính diện'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham1->id,
            'id_mausac' => $mauSac4->id, // Đen
            'id_size' => $size4->id, // XL
            'gia' => 2500000,
            'soLuong' => 10
        ]);

        // Sản phẩm 2: Bàn học sinh
        $sanPham2 = SanPham::create([
            'maSP' => 'BH001',
            'tenSP' => 'Bàn học sinh gỗ tự nhiên',
            'id_danhmuc' => $danhMucBanHoc->id,
            'moTa' => 'Bàn học sinh thiết kế tiện lợi, phù hợp mọi lứa tuổi, chất liệu gỗ tự nhiên',
            'trangthai' => 1,
            'base_price' => 800000,
            'base_sale_price' => 0,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham2->id,
            'url' => 'products/ban-hoc-sinh-go.jpg',
            'is_default' => true,
            'mota' => 'Bàn học sinh gỗ tự nhiên'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham2->id,
            'id_mausac' => $mauSac6->id, // Nâu gỗ
            'id_size' => $size2->id, // M
            'gia' => 800000,
            'soLuong' => 25
        ]);

        // Sản phẩm 3: Bàn làm việc văn phòng
        $sanPham3 = SanPham::create([
            'maSP' => 'BLV001',
            'tenSP' => 'Bàn làm việc văn phòng hiện đại',
            'id_danhmuc' => $danhMucBanLamViec->id,
            'moTa' => 'Bàn làm việc cho nhân viên, kiểu dáng đơn giản hiện đại, có ngăn kéo tiện lợi',
            'trangthai' => 1,
            'base_price' => 1500000,
            'base_sale_price' => 1200000,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham3->id,
            'url' => 'products/ban-lam-viec-van-phong.jpg',
            'is_default' => true,
            'mota' => 'Bàn làm việc văn phòng'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham3->id,
            'id_mausac' => $mauSac4->id, // Đen
            'id_size' => $size3->id, // L
            'gia' => 1500000,
            'soLuong' => 20
        ]);

        // Sản phẩm 4: Bàn giám đốc
        $sanPham4 = SanPham::create([
            'maSP' => 'BGD001',
            'tenSP' => 'Bàn giám đốc cao cấp',
            'id_danhmuc' => $danhMucBanGiamDoc->id,
            'moTa' => 'Bàn giám đốc sang trọng, phong cách đẳng cấp, chất liệu gỗ cao cấp',
            'trangthai' => 1,
            'base_price' => 5000000,
            'base_sale_price' => 4500000,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham4->id,
            'url' => 'products/ban-giam-doc-cao-cap.jpg',
            'is_default' => true,
            'mota' => 'Bàn giám đốc cao cấp'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham4->id,
            'id_mausac' => $mauSac6->id, // Nâu gỗ
            'id_size' => $size4->id, // XL
            'gia' => 5000000,
            'soLuong' => 5
        ]);

        // Sản phẩm 5: Giường tầng
        $sanPham5 = SanPham::create([
            'maSP' => 'GT001',
            'tenSP' => 'Giường tầng trẻ em an toàn',
            'id_danhmuc' => $danhMucGiuongTang->id,
            'moTa' => 'Giường tầng an toàn cho trẻ em, tiết kiệm không gian, thiết kế đẹp mắt',
            'trangthai' => 1,
            'base_price' => 1800000,
            'base_sale_price' => 0,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham5->id,
            'url' => 'products/giuong-tang-tre-em.jpg',
            'is_default' => true,
            'mota' => 'Giường tầng trẻ em'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham5->id,
            'id_mausac' => $mauSac2->id, // Xanh
            'id_size' => $size3->id, // L
            'gia' => 1800000,
            'soLuong' => 15
        ]);

        // Sản phẩm 6: Giường trẻ em
        $sanPham6 = SanPham::create([
            'maSP' => 'GTE001',
            'tenSP' => 'Giường trẻ em đơn an toàn',
            'id_danhmuc' => $danhMucGiuongTreEm->id,
            'moTa' => 'Giường trẻ em đơn an toàn, màu sắc tươi sáng, thiết kế thân thiện',
            'trangthai' => 1,
            'base_price' => 1200000,
            'base_sale_price' => 0,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham6->id,
            'url' => 'products/giuong-tre-em-don.jpg',
            'is_default' => true,
            'mota' => 'Giường trẻ em đơn'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham6->id,
            'id_mausac' => $mauSac2->id, // Xanh
            'id_size' => $size2->id, // M
            'gia' => 1200000,
            'soLuong' => 12
        ]);

        // Sản phẩm 7: Tủ quần áo gỗ sồi
        $sanPham7 = SanPham::create([
            'maSP' => 'TQA001',
            'tenSP' => 'Tủ quần áo gỗ sồi bền đẹp',
            'id_danhmuc' => $danhMucTuQuanAo->id,
            'moTa' => 'Tủ quần áo gỗ sồi bền đẹp, nhiều ngăn kéo, thiết kế sang trọng',
            'trangthai' => 1,
            'base_price' => 3200000,
            'base_sale_price' => 2800000,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham7->id,
            'url' => 'products/tu-quan-ao-go-soi.jpg',
            'is_default' => true,
            'mota' => 'Tủ quần áo gỗ sồi'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham7->id,
            'id_mausac' => $mauSac6->id, // Nâu gỗ
            'id_size' => $size4->id, // XL
            'gia' => 3200000,
            'soLuong' => 8
        ]);

        // Sản phẩm 8: Tủ quần áo gỗ lim
        $sanPham8 = SanPham::create([
            'maSP' => 'TQAL001',
            'tenSP' => 'Tủ quần áo gỗ lim cao cấp',
            'id_danhmuc' => $danhMucTuQuanAoLim->id,
            'moTa' => 'Tủ quần áo gỗ lim cao cấp, nhiều ngăn kéo, thiết kế sang trọng',
            'trangthai' => 1,
            'base_price' => 4500000,
            'base_sale_price' => 4000000,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham8->id,
            'url' => 'products/tu-quan-ao-go-lim.jpg',
            'is_default' => true,
            'mota' => 'Tủ quần áo gỗ lim'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham8->id,
            'id_mausac' => $mauSac6->id, // Nâu gỗ
            'id_size' => $size4->id, // XL
            'gia' => 4500000,
            'soLuong' => 6
        ]);

        // Sản phẩm 9: Bàn ăn kính cường lực 4 ghế
        $sanPham9 = SanPham::create([
            'maSP' => 'BA002',
            'tenSP' => 'Bàn ăn kính cường lực 4 ghế',
            'id_danhmuc' => $danhMucBanAn->id,
            'moTa' => 'Bàn ăn mặt kính cường lực hiện đại, dễ lau chùi, phù hợp gia đình 4 người',
            'trangthai' => 1,
            'base_price' => 1800000,
            'base_sale_price' => 1500000,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham9->id,
            'url' => 'products/ban-an-kinh-cuong-luc-4-ghe.jpg',
            'is_default' => true,
            'mota' => 'Bàn ăn kính cường lực 4 ghế'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham9->id,
            'id_mausac' => $mauSac4->id, // Đen
            'id_size' => $size3->id, // L
            'gia' => 1800000,
            'soLuong' => 15
        ]);

        // Sản phẩm 10: Bàn học sinh gấp gọn
        $sanPham10 = SanPham::create([
            'maSP' => 'BH002',
            'tenSP' => 'Bàn học sinh gấp gọn',
            'id_danhmuc' => $danhMucBanHoc->id,
            'moTa' => 'Bàn học gấp gọn, tiết kiệm không gian, phù hợp căn hộ nhỏ',
            'trangthai' => 1,
            'base_price' => 600000,
            'base_sale_price' => 0,
        ]);

        SanPhamHinhanh::create([
            'sanpham_id' => $sanPham10->id,
            'url' => 'products/ban-hoc-sinh-gap-gon.jpg',
            'is_default' => true,
            'mota' => 'Bàn học sinh gấp gọn'
        ]);

        ChiTietSanPham::create([
            'id_sp' => $sanPham10->id,
            'id_mausac' => $mauSac5->id, // Trắng
            'id_size' => $size2->id, // M
            'gia' => 600000,
            'soLuong' => 30
        ]);
    }
}