<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DanhMuc;

class DanhMucSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh mục gốc
        $danhMuc1 = DanhMuc::create([
            'name' => 'Thực phẩm',
            'description' => 'Các loại thực phẩm tươi sống và đóng gói',
            'parent_id' => 0,
            'sort_order' => 1,
            'status' => 'active'
        ]);

        $danhMuc2 = DanhMuc::create([
            'name' => 'Đồ uống',
            'description' => 'Các loại đồ uống giải khát',
            'parent_id' => 0,
            'sort_order' => 2,
            'status' => 'active'
        ]);

        $danhMuc3 = DanhMuc::create([
            'name' => 'Đồ gia dụng',
            'description' => 'Các sản phẩm gia dụng trong nhà',
            'parent_id' => 0,
            'sort_order' => 3,
            'status' => 'active'
        ]);

        // Danh mục con của Thực phẩm
        DanhMuc::create([
            'name' => 'Rau củ quả',
            'description' => 'Các loại rau củ quả tươi',
            'parent_id' => $danhMuc1->id,
            'sort_order' => 1,
            'status' => 'active'
        ]);

        DanhMuc::create([
            'name' => 'Thịt cá',
            'description' => 'Các loại thịt cá tươi',
            'parent_id' => $danhMuc1->id,
            'sort_order' => 2,
            'status' => 'active'
        ]);

        // Danh mục con của Đồ uống
        DanhMuc::create([
            'name' => 'Nước ngọt',
            'description' => 'Các loại nước ngọt có gas',
            'parent_id' => $danhMuc2->id,
            'sort_order' => 1,
            'status' => 'active'
        ]);

        DanhMuc::create([
            'name' => 'Nước trái cây',
            'description' => 'Các loại nước trái cây tự nhiên',
            'parent_id' => $danhMuc2->id,
            'sort_order' => 2,
            'status' => 'active'
        ]);

        // Danh mục con của Đồ gia dụng
        DanhMuc::create([
            'name' => 'Đồ bếp',
            'description' => 'Các dụng cụ nhà bếp',
            'parent_id' => $danhMuc3->id,
            'sort_order' => 1,
            'status' => 'active'
        ]);

        DanhMuc::create([
            'name' => 'Đồ vệ sinh',
            'description' => 'Các sản phẩm vệ sinh nhà cửa',
            'parent_id' => $danhMuc3->id,
            'sort_order' => 2,
            'status' => 'active'
        ]);
    }
}
