<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\User;
use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use Carbon\Carbon;

class DonHangSeeder extends Seeder
{
    public function run()
    {
        // Tạo một số user mẫu nếu chưa có
        $users = User::take(5)->get();
        if ($users->isEmpty()) {
            $users = collect();
            for ($i = 1; $i <= 5; $i++) {
                $user = User::create([
                    'name' => "Khách hàng {$i}",
                    'email' => "customer{$i}@example.com",
                    'password' => bcrypt('password'),
                    'phone' => "012345678{$i}",
                    'address' => "Địa chỉ {$i}, TP.HCM"
                ]);
                $users->push($user);
            }
        }

        // Tạo một số sản phẩm mẫu nếu chưa có
        $sanphams = SanPham::take(10)->get();
        if ($sanphams->isEmpty()) {
            $sanphams = collect();
            for ($i = 1; $i <= 10; $i++) {
                $sanpham = SanPham::create([
                    'maSP' => "SP{$i}",
                    'tenSP' => "Sản phẩm mẫu {$i}",
                    'id_danhmuc' => 1,
                    'moTa' => "Mô tả sản phẩm {$i}",
                    'trangthai' => 1
                ]);
                $sanphams->push($sanpham);
            }
        }

        // Tạo chi tiết sản phẩm
        $sanphams->each(function ($sanpham) {
            ChiTietSanPham::create([
                'id_sp' => $sanpham->id,
                'id_mausac' => 1,
                'id_size' => 1,
                'soLuong' => rand(10, 100),
                'tenSp' => $sanpham->tenSP,
                'gia' => rand(100000, 1000000),
                'gia_khuyenmai' => rand(50000, 800000)
            ]);
        });

        // Tạo đơn hàng mẫu
        $trangThaiOptions = [
            DonHang::TRANGTHAI_CHO_XAC_NHAN,
            DonHang::TRANGTHAI_DA_XAC_NHAN,
            DonHang::TRANGTHAI_DANG_GIAO,
            DonHang::TRANGTHAI_DA_GIAO,
            DonHang::TRANGTHAI_DA_HUY
        ];

        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();
            $trangThai = $trangThaiOptions[array_rand($trangThaiOptions)];
            $ngayTao = Carbon::now()->subDays(rand(1, 30));
            
            $donhang = DonHang::create([
                'id_user' => $user->id,
                'trangthai' => $trangThai,
                'ngaytao' => $ngayTao,
                'ngaythanhtoan' => $trangThai === DonHang::TRANGTHAI_DA_GIAO ? $ngayTao->addDays(rand(1, 5)) : null,
                'nhanvien' => 'Nhân viên ' . rand(1, 3),
                'tensp' => 'Đơn hàng #' . $i,
                'tongtien' => 0, // Sẽ được tính sau
                'ghichu' => 'Ghi chú đơn hàng ' . $i,
                'lichsutrangthai' => json_encode([
                    [
                        'trangthai_cu' => null,
                        'trangthai_moi' => $trangThai,
                        'thoi_gian' => $ngayTao->toDateTimeString(),
                        'nhan_vien' => 'System'
                    ]
                ])
            ]);

            // Tạo chi tiết đơn hàng
            $soLuongSanPham = rand(1, 5);
            $tongTien = 0;
            
            for ($j = 1; $j <= $soLuongSanPham; $j++) {
                $sanpham = $sanphams->random();
                $chiTietSanPham = ChiTietSanPham::where('id_sp', $sanpham->id)->first();
                
                if ($chiTietSanPham) {
                    $soLuong = rand(1, 3);
                    $donGia = $chiTietSanPham->gia_khuyenmai ?: $chiTietSanPham->gia;
                    $thanhTien = $donGia * $soLuong;
                    $tongTien += $thanhTien;
                    
                    ChiTietDonHang::create([
                        'id_donhang' => $donhang->id,
                        'id_chitietsanpham' => $chiTietSanPham->id,
                        'tensanpham' => $sanpham->tenSP,
                        'dongia' => $donGia,
                        'soluong' => $soLuong,
                        'thanhtien' => $thanhTien,
                        'ghichu' => "Ghi chú sản phẩm {$j}"
                    ]);
                }
            }
            
            // Cập nhật tổng tiền
            $donhang->update(['tongtien' => $tongTien]);
        }

        $this->command->info('Đã tạo dữ liệu mẫu cho đơn hàng thành công!');
    }
}