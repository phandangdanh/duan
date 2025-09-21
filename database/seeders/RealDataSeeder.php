<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RealDataSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ
        DB::table('sanpham_hinhanh')->delete();
        DB::table('chitietsanpham')->delete();
        DB::table('sanpham')->delete();
        DB::table('danhmuc')->delete();
        DB::table('mausac')->delete();
        DB::table('size')->delete();

        // Tạo danh mục thực tế
        $categories = [
            ['id' => 1, 'name' => 'Bàn ăn', 'slug' => 'ban-an', 'description' => 'Bàn ăn đẹp, chất lượng cao', 'parent_id' => 0, 'sort_order' => 1, 'status' => 'active'],
            ['id' => 2, 'name' => 'Ghế', 'slug' => 'ghe', 'description' => 'Ghế ngồi thoải mái', 'parent_id' => 0, 'sort_order' => 2, 'status' => 'active'],
            ['id' => 3, 'name' => 'Tủ', 'slug' => 'tu', 'description' => 'Tủ đựng đồ đa năng', 'parent_id' => 0, 'sort_order' => 3, 'status' => 'active'],
            ['id' => 4, 'name' => 'Giường', 'slug' => 'giuong', 'description' => 'Giường ngủ êm ái', 'parent_id' => 0, 'sort_order' => 4, 'status' => 'active'],
            ['id' => 5, 'name' => 'Sofa', 'slug' => 'sofa', 'description' => 'Sofa phòng khách', 'parent_id' => 0, 'sort_order' => 5, 'status' => 'active'],
        ];

        DB::table('danhmuc')->insert($categories);

        // Tạo màu sắc
        $colors = [
            ['id' => 1, 'ten' => 'Nâu gỗ', 'mota' => 'Màu nâu gỗ tự nhiên'],
            ['id' => 2, 'ten' => 'Trắng', 'mota' => 'Màu trắng tinh khiết'],
            ['id' => 3, 'ten' => 'Đen', 'mota' => 'Màu đen sang trọng'],
            ['id' => 4, 'ten' => 'Xám', 'mota' => 'Màu xám hiện đại'],
            ['id' => 5, 'ten' => 'Vàng', 'mota' => 'Màu vàng ấm áp'],
        ];

        DB::table('mausac')->insert($colors);

        // Tạo size
        $sizes = [
            ['id' => 1, 'ten' => 'S', 'mota' => 'Size nhỏ'],
            ['id' => 2, 'ten' => 'M', 'mota' => 'Size vừa'],
            ['id' => 3, 'ten' => 'L', 'mota' => 'Size lớn'],
            ['id' => 4, 'ten' => 'XL', 'mota' => 'Size rất lớn'],
        ];

        DB::table('size')->insert($sizes);

        // Tạo sản phẩm thực tế
        $products = [
            [
                'id' => 1,
                'maSP' => 'SP001',
                'tenSP' => 'Bàn ăn gỗ sồi 6 ghế',
                'id_danhmuc' => 1,
                'moTa' => 'Bàn ăn gỗ sồi tự nhiên, thiết kế hiện đại, phù hợp cho gia đình 6 người. Chất liệu gỗ sồi cao cấp, bền đẹp theo thời gian.',
                'base_price' => 2500000,
                'base_sale_price' => 2000000,
                'trangthai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'maSP' => 'SP002',
                'tenSP' => 'Ghế ăn gỗ sồi',
                'id_danhmuc' => 2,
                'moTa' => 'Ghế ăn gỗ sồi tự nhiên, thiết kế đơn giản nhưng sang trọng. Phù hợp với bàn ăn gỗ sồi.',
                'base_price' => 800000,
                'base_sale_price' => 0,
                'trangthai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'maSP' => 'SP003',
                'tenSP' => 'Tủ quần áo 4 cánh',
                'id_danhmuc' => 3,
                'moTa' => 'Tủ quần áo 4 cánh, thiết kế hiện đại, nhiều ngăn kéo tiện lợi. Chất liệu gỗ công nghiệp cao cấp.',
                'base_price' => 3500000,
                'base_sale_price' => 2800000,
                'trangthai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'maSP' => 'SP004',
                'tenSP' => 'Giường ngủ gỗ sồi 1m6',
                'id_danhmuc' => 4,
                'moTa' => 'Giường ngủ gỗ sồi tự nhiên, kích thước 1m6x2m. Thiết kế đơn giản, phù hợp với phòng ngủ hiện đại.',
                'base_price' => 4500000,
                'base_sale_price' => 0,
                'trangthai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'maSP' => 'SP005',
                'tenSP' => 'Sofa 3 chỗ ngồi',
                'id_danhmuc' => 5,
                'moTa' => 'Sofa 3 chỗ ngồi, bọc vải cao cấp, thiết kế hiện đại. Phù hợp cho phòng khách.',
                'base_price' => 5500000,
                'base_sale_price' => 4500000,
                'trangthai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'maSP' => 'SP006',
                'tenSP' => 'Bàn làm việc gỗ sồi',
                'id_danhmuc' => 1,
                'moTa' => 'Bàn làm việc gỗ sồi tự nhiên, thiết kế đơn giản, phù hợp cho văn phòng hoặc phòng làm việc tại nhà.',
                'base_price' => 1800000,
                'base_sale_price' => 0,
                'trangthai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'maSP' => 'SP007',
                'tenSP' => 'Ghế văn phòng xoay',
                'id_danhmuc' => 2,
                'moTa' => 'Ghế văn phòng xoay, có thể điều chỉnh độ cao, thiết kế ergonomic, phù hợp cho văn phòng.',
                'base_price' => 1200000,
                'base_sale_price' => 950000,
                'trangthai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'maSP' => 'SP008',
                'tenSP' => 'Tủ giày 3 tầng',
                'id_danhmuc' => 3,
                'moTa' => 'Tủ giày 3 tầng, thiết kế gọn gàng, phù hợp cho hành lang hoặc phòng khách. Chất liệu gỗ công nghiệp.',
                'base_price' => 800000,
                'base_sale_price' => 0,
                'trangthai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('sanpham')->insert($products);

        // Tạo hình ảnh sản phẩm
        $productImages = [
            ['sanpham_id' => 1, 'url' => 'fontend/img/products/ban-an-1.jpg', 'alt' => 'Bàn ăn gỗ sồi 6 ghế'],
            ['sanpham_id' => 2, 'url' => 'fontend/img/products/ghe-an-1.jpg', 'alt' => 'Ghế ăn gỗ sồi'],
            ['sanpham_id' => 3, 'url' => 'fontend/img/products/tu-quan-ao-1.jpg', 'alt' => 'Tủ quần áo 4 cánh'],
            ['sanpham_id' => 4, 'url' => 'fontend/img/products/giuong-ngu-1.jpg', 'alt' => 'Giường ngủ gỗ sồi 1m6'],
            ['sanpham_id' => 5, 'url' => 'fontend/img/products/sofa-3-cho-1.jpg', 'alt' => 'Sofa 3 chỗ ngồi'],
            ['sanpham_id' => 6, 'url' => 'fontend/img/products/ban-lam-viec-1.jpg', 'alt' => 'Bàn làm việc gỗ sồi'],
            ['sanpham_id' => 7, 'url' => 'fontend/img/products/ghe-van-phong-1.jpg', 'alt' => 'Ghế văn phòng xoay'],
            ['sanpham_id' => 8, 'url' => 'fontend/img/products/tu-giay-1.jpg', 'alt' => 'Tủ giày 3 tầng'],
        ];

        DB::table('sanpham_hinhanh')->insert($productImages);

        // Tạo chi tiết sản phẩm
        $productDetails = [
            ['id_sp' => 1, 'id_mausac' => 1, 'id_size' => 3, 'soluong' => 10, 'gia' => 2500000, 'gia_sale' => 2000000],
            ['id_sp' => 2, 'id_mausac' => 1, 'id_size' => 2, 'soluong' => 20, 'gia' => 800000, 'gia_sale' => 0],
            ['id_sp' => 3, 'id_mausac' => 2, 'id_size' => 4, 'soluong' => 5, 'gia' => 3500000, 'gia_sale' => 2800000],
            ['id_sp' => 4, 'id_mausac' => 1, 'id_size' => 3, 'soluong' => 3, 'gia' => 4500000, 'gia_sale' => 0],
            ['id_sp' => 5, 'id_mausac' => 3, 'id_size' => 3, 'soluong' => 2, 'gia' => 5500000, 'gia_sale' => 4500000],
            ['id_sp' => 6, 'id_mausac' => 1, 'id_size' => 3, 'soluong' => 8, 'gia' => 1800000, 'gia_sale' => 0],
            ['id_sp' => 7, 'id_mausac' => 3, 'id_size' => 2, 'soluong' => 15, 'gia' => 1200000, 'gia_sale' => 950000],
            ['id_sp' => 8, 'id_mausac' => 2, 'id_size' => 2, 'soluong' => 12, 'gia' => 800000, 'gia_sale' => 0],
        ];

        DB::table('chitietsanpham')->insert($productDetails);

        $this->command->info('Real data seeded successfully!');
    }
}
