<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            ['ten' => 'XS', 'mota' => 'Extra Small - Rất nhỏ'],
            ['ten' => 'S', 'mota' => 'Small - Nhỏ'],
            ['ten' => 'M', 'mota' => 'Medium - Vừa'],
            ['ten' => 'L', 'mota' => 'Large - Lớn'],
            ['ten' => 'XL', 'mota' => 'Extra Large - Rất lớn'],
            ['ten' => 'XXL', 'mota' => 'Double Extra Large - Cực lớn'],
            ['ten' => 'XXXL', 'mota' => 'Triple Extra Large - Siêu lớn'],
            ['ten' => '28', 'mota' => 'Size 28 (Quần)'],
            ['ten' => '29', 'mota' => 'Size 29 (Quần)'],
            ['ten' => '30', 'mota' => 'Size 30 (Quần)'],
            ['ten' => '31', 'mota' => 'Size 31 (Quần)'],
            ['ten' => '32', 'mota' => 'Size 32 (Quần)'],
            ['ten' => '33', 'mota' => 'Size 33 (Quần)'],
            ['ten' => '34', 'mota' => 'Size 34 (Quần)'],
            ['ten' => '35', 'mota' => 'Size 35 (Quần)'],
            ['ten' => '36', 'mota' => 'Size 36 (Quần)'],
            ['ten' => '37', 'mota' => 'Size 37 (Quần)'],
            ['ten' => '38', 'mota' => 'Size 38 (Quần)'],
            ['ten' => '39', 'mota' => 'Size 39 (Quần)'],
            ['ten' => '40', 'mota' => 'Size 40 (Quần)'],
            ['ten' => '41', 'mota' => 'Size 41 (Quần)'],
            ['ten' => '42', 'mota' => 'Size 42 (Quần)'],
            ['ten' => '43', 'mota' => 'Size 43 (Quần)'],
            ['ten' => '44', 'mota' => 'Size 44 (Quần)'],
            ['ten' => '45', 'mota' => 'Size 45 (Quần)'],
            ['ten' => '46', 'mota' => 'Size 46 (Quần)'],
            ['ten' => '47', 'mota' => 'Size 47 (Quần)'],
            ['ten' => '48', 'mota' => 'Size 48 (Quần)'],
            ['ten' => '49', 'mota' => 'Size 49 (Quần)'],
            ['ten' => '50', 'mota' => 'Size 50 (Quần)'],
            ['ten' => 'Free Size', 'mota' => 'Một size duy nhất'],
            ['ten' => 'One Size', 'mota' => 'Một size duy nhất'],
        ];

        foreach ($sizes as $size) {
            DB::table('size')->insert([
                'ten' => $size['ten'],
                'mota' => $size['mota'],
            ]);
        }

        $this->command->info('Đã thêm ' . count($sizes) . ' size vào bảng size.');
    }
}
