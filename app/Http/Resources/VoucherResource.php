<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ma_voucher' => $this->ma_voucher,
            'ten_voucher' => $this->ten_voucher,
            'mota' => $this->mota,
            'loai_giam_gia' => $this->loai_giam_gia,
            'loai_giam_gia_text' => $this->loai_giam_gia_text,
            'gia_tri' => $this->gia_tri,
            'gia_tri_toi_thieu' => $this->gia_tri_toi_thieu,
            'gia_tri_toi_da' => $this->gia_tri_toi_da,
            'so_luong' => $this->so_luong,
            'so_luong_da_su_dung' => $this->so_luong_da_su_dung,
            'so_luong_con_lai' => $this->so_luong_con_lai,
            'ngay_bat_dau' => $this->ngay_bat_dau?->format('Y-m-d H:i:s'),
            'ngay_ket_thuc' => $this->ngay_ket_thuc?->format('Y-m-d H:i:s'),
            'trang_thai' => $this->trang_thai,
            'trang_thai_text' => $this->trang_thai_text,
            'is_usable' => $this->isUsable(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}