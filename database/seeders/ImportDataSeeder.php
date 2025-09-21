<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Import dữ liệu từ file SQL
        $sqlFile = database_path('seeders/duantotnghiep_data.sql');
        
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            DB::unprepared($sql);
            $this->command->info('Data imported successfully from SQL file.');
        } else {
            // Tạo dữ liệu cơ bản nếu không có file SQL
            $this->createBasicData();
        }
    }

    private function createBasicData()
    {
        // Tạo danh mục cơ bản
        DB::table('danhmuc')->insert([
            [
                'id' => 1,
                'name' => 'Bàn ăn kính cường lực',
                'slug' => 'ban-an-kinh-cuong-luc',
                'description' => 'Bàn ăn mặt kính cường lực hiện đại, dễ lau chùi.',
                'parent_id' => 0,
                'sort_order' => 1,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Bàn học sinh',
                'slug' => 'ban-hoc-sinh',
                'description' => 'Bàn học sinh thiết kế tiện lợi, phù hợp mọi lứa tuổi.',
                'parent_id' => 0,
                'sort_order' => 2,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Bàn làm việc văn phòng',
                'slug' => 'ban-lam-viec-van-phong',
                'description' => 'Bàn làm việc cho nhân viên, kiểu dáng đơn giản hiện đại.',
                'parent_id' => 0,
                'sort_order' => 3,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Bàn giám đốc',
                'slug' => 'ban-giam-doc',
                'description' => 'Bàn giám đốc sang trọng, phong cách đẳng cấp.',
                'parent_id' => 0,
                'sort_order' => 4,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Giường tầng',
                'slug' => 'giuong-tang',
                'description' => 'Giường tầng cho trẻ em, sinh viên',
                'parent_id' => 0,
                'sort_order' => 5,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Giường trẻ em',
                'slug' => 'giuong-tre-em',
                'description' => 'Giường ngủ an toàn cho trẻ nhỏ',
                'parent_id' => 0,
                'sort_order' => 6,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Tủ quần áo gỗ sồi',
                'slug' => 'tu-quan-ao-go-soi',
                'description' => 'Tủ quần áo gỗ sồi bền đẹp',
                'parent_id' => 0,
                'sort_order' => 7,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Tủ quần áo gỗ lim',
                'slug' => 'tu-quan-ao-go-lim',
                'description' => 'Tủ quần áo gỗ lim cao cấp',
                'parent_id' => 0,
                'sort_order' => 8,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Tạo màu sắc
        DB::table('mausac')->insert([
            ['id' => 1, 'ten' => 'Đỏ', 'mota' => 'Màu đỏ tươi'],
            ['id' => 2, 'ten' => 'Xanh lá', 'mota' => 'Màu xanh lá cây'],
            ['id' => 3, 'ten' => 'Xanh dương', 'mota' => 'Màu xanh dương'],
            ['id' => 4, 'ten' => 'Vàng', 'mota' => 'Màu vàng tươi'],
            ['id' => 5, 'ten' => 'Đen', 'mota' => 'Màu đen'],
            ['id' => 6, 'ten' => 'Trắng', 'mota' => 'Màu trắng'],
            ['id' => 7, 'ten' => 'Hồng', 'mota' => 'Màu hồng'],
            ['id' => 8, 'ten' => 'Tím', 'mota' => 'Màu tím'],
            ['id' => 9, 'ten' => 'Cam', 'mota' => 'Màu cam'],
            ['id' => 10, 'ten' => 'Nâu', 'mota' => 'Màu nâu gỗ']
        ]);

        // Tạo size
        DB::table('size')->insert([
            ['id' => 1, 'ten' => 'XS', 'mota' => 'Extra Small - Rất nhỏ'],
            ['id' => 2, 'ten' => 'S', 'mota' => 'Small - Nhỏ'],
            ['id' => 3, 'ten' => 'M', 'mota' => 'Medium - Vừa'],
            ['id' => 4, 'ten' => 'L', 'mota' => 'Large - Lớn'],
            ['id' => 5, 'ten' => 'XL', 'mota' => 'Extra Large - Rất lớn'],
            ['id' => 6, 'ten' => 'XXL', 'mota' => 'Double Extra Large - Cực lớn']
        ]);

        // Tạo sản phẩm
        DB::table('sanpham')->insert([
            [
                'id' => 1,
                'maSP' => 'BA001',
                'tenSP' => 'Bàn ăn kính cường lực 6 ghế',
                'id_danhmuc' => 1,
                'moTa' => 'Bàn ăn mặt kính cường lực hiện đại, dễ lau chùi, phù hợp gia đình 6 người',
                'trangthai' => 1,
                'base_price' => 2500000,
                'base_sale_price' => 2000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'maSP' => 'BH001',
                'tenSP' => 'Bàn học sinh gỗ tự nhiên',
                'id_danhmuc' => 2,
                'moTa' => 'Bàn học sinh thiết kế tiện lợi, phù hợp mọi lứa tuổi, chất liệu gỗ tự nhiên',
                'trangthai' => 1,
                'base_price' => 800000,
                'base_sale_price' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'maSP' => 'BLV001',
                'tenSP' => 'Bàn làm việc văn phòng hiện đại',
                'id_danhmuc' => 3,
                'moTa' => 'Bàn làm việc cho nhân viên, kiểu dáng đơn giản hiện đại, có ngăn kéo tiện lợi',
                'trangthai' => 1,
                'base_price' => 1500000,
                'base_sale_price' => 1200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'maSP' => 'BGD001',
                'tenSP' => 'Bàn giám đốc cao cấp',
                'id_danhmuc' => 4,
                'moTa' => 'Bàn giám đốc sang trọng, phong cách đẳng cấp, chất liệu gỗ cao cấp',
                'trangthai' => 1,
                'base_price' => 5000000,
                'base_sale_price' => 4500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'maSP' => 'GT001',
                'tenSP' => 'Giường tầng trẻ em an toàn',
                'id_danhmuc' => 5,
                'moTa' => 'Giường tầng an toàn cho trẻ em, tiết kiệm không gian, thiết kế đẹp mắt',
                'trangthai' => 1,
                'base_price' => 1800000,
                'base_sale_price' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'maSP' => 'GTE001',
                'tenSP' => 'Giường trẻ em đơn an toàn',
                'id_danhmuc' => 6,
                'moTa' => 'Giường trẻ em đơn an toàn, màu sắc tươi sáng, thiết kế thân thiện',
                'trangthai' => 1,
                'base_price' => 1200000,
                'base_sale_price' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'maSP' => 'TQA001',
                'tenSP' => 'Tủ quần áo gỗ sồi bền đẹp',
                'id_danhmuc' => 7,
                'moTa' => 'Tủ quần áo gỗ sồi bền đẹp, nhiều ngăn kéo, thiết kế sang trọng',
                'trangthai' => 1,
                'base_price' => 3200000,
                'base_sale_price' => 2800000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'maSP' => 'TQAL001',
                'tenSP' => 'Tủ quần áo gỗ lim cao cấp',
                'id_danhmuc' => 8,
                'moTa' => 'Tủ quần áo gỗ lim cao cấp, nhiều ngăn kéo, thiết kế sang trọng',
                'trangthai' => 1,
                'base_price' => 4500000,
                'base_sale_price' => 4000000,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Tạo hình ảnh sản phẩm
        DB::table('sanpham_hinhanh')->insert([
            [
                'id' => 1,
                'sanpham_id' => 1,
                'url' => 'products/ban-an-kinh-cuong-luc.jpg',
                'is_default' => 1,
                'mota' => 'Bàn ăn kính cường lực chính diện',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'sanpham_id' => 2,
                'url' => 'products/ban-hoc-sinh-go.jpg',
                'is_default' => 1,
                'mota' => 'Bàn học sinh gỗ tự nhiên',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'sanpham_id' => 3,
                'url' => 'products/ban-lam-viec-van-phong.jpg',
                'is_default' => 1,
                'mota' => 'Bàn làm việc văn phòng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'sanpham_id' => 4,
                'url' => 'products/ban-giam-doc-cao-cap.jpg',
                'is_default' => 1,
                'mota' => 'Bàn giám đốc cao cấp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'sanpham_id' => 5,
                'url' => 'products/giuong-tang-tre-em.jpg',
                'is_default' => 1,
                'mota' => 'Giường tầng trẻ em',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'sanpham_id' => 6,
                'url' => 'products/giuong-tre-em-don.jpg',
                'is_default' => 1,
                'mota' => 'Giường trẻ em đơn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'sanpham_id' => 7,
                'url' => 'products/tu-quan-ao-go-soi.jpg',
                'is_default' => 1,
                'mota' => 'Tủ quần áo gỗ sồi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'sanpham_id' => 8,
                'url' => 'products/tu-quan-ao-go-lim.jpg',
                'is_default' => 1,
                'mota' => 'Tủ quần áo gỗ lim',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Tạo chi tiết sản phẩm
        DB::table('chitietsanpham')->insert([
            [
                'id' => 1,
                'id_sp' => 1,
                'id_mausac' => 5, // Đen
                'id_size' => 4, // L
                'gia' => 2500000,
                'soLuong' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'id_sp' => 2,
                'id_mausac' => 10, // Nâu
                'id_size' => 2, // S
                'gia' => 800000,
                'soLuong' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'id_sp' => 3,
                'id_mausac' => 5, // Đen
                'id_size' => 3, // M
                'gia' => 1500000,
                'soLuong' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'id_sp' => 4,
                'id_mausac' => 10, // Nâu
                'id_size' => 4, // L
                'gia' => 5000000,
                'soLuong' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'id_sp' => 5,
                'id_mausac' => 3, // Xanh dương
                'id_size' => 3, // M
                'gia' => 1800000,
                'soLuong' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'id_sp' => 6,
                'id_mausac' => 3, // Xanh dương
                'id_size' => 2, // S
                'gia' => 1200000,
                'soLuong' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'id_sp' => 7,
                'id_mausac' => 10, // Nâu
                'id_size' => 4, // L
                'gia' => 3200000,
                'soLuong' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'id_sp' => 8,
                'id_mausac' => 10, // Nâu
                'id_size' => 4, // L
                'gia' => 4500000,
                'soLuong' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->command->info('Basic data created successfully!');
    }
}
