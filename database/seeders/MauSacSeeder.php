<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MauSacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mausacs = [
            ['ten' => 'Đỏ', 'mota' => 'Màu đỏ tươi'],
            ['ten' => 'Xanh lá', 'mota' => 'Màu xanh lá cây'],
            ['ten' => 'Xanh dương', 'mota' => 'Màu xanh dương'],
            ['ten' => 'Vàng', 'mota' => 'Màu vàng tươi'],
            ['ten' => 'Đen', 'mota' => 'Màu đen'],
            ['ten' => 'Trắng', 'mota' => 'Màu trắng'],
            ['ten' => 'Hồng', 'mota' => 'Màu hồng'],
            ['ten' => 'Tím', 'mota' => 'Màu tím'],
            ['ten' => 'Cam', 'mota' => 'Màu cam'],
            ['ten' => 'Nâu', 'mota' => 'Màu nâu'],
            ['ten' => 'Xám', 'mota' => 'Màu xám'],
            ['ten' => 'Bạc', 'mota' => 'Màu bạc'],
            ['ten' => 'Vàng nhạt', 'mota' => 'Màu vàng nhạt'],
            ['ten' => 'Xanh nhạt', 'mota' => 'Màu xanh nhạt'],
            ['ten' => 'Xanh lá nhạt', 'mota' => 'Màu xanh lá nhạt'],
            ['ten' => 'Hồng nhạt', 'mota' => 'Màu hồng nhạt'],
        ];

        foreach ($mausacs as $mausac) {
            DB::table('mausac')->insert([
                'ten' => $mausac['ten'],
                'mota' => $mausac['mota'],
            ]);
        }

        $this->command->info('Đã thêm ' . count($mausacs) . ' màu sắc vào bảng mausac.');
    }
}
