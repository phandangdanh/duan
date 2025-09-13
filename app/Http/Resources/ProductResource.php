<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'maSP' => $this->maSP,
            'tenSP' => $this->tenSP,
            'id_danhmuc' => $this->id_danhmuc,
            'moTa' => $this->moTa,
            'trangthai' => (bool) $this->trangthai,
            'base_price' => $this->base_price ? (float) $this->base_price : null,
            'base_sale_price' => $this->base_sale_price ? (float) $this->base_sale_price : null,
            'danhmuc' => $this->whenLoaded('danhmuc', function () {
                return [
                    'id' => $this->danhmuc->id,
                    'name' => $this->danhmuc->name,
                ];
            }),
            'hinhanh' => $this->whenLoaded('hinhanh', function () {
                return $this->hinhanh->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->url ? url($image->url) : null,
                        'is_default' => (bool) $image->is_default,
                    ];
                });
            }),
            'chitietsanpham' => $this->whenLoaded('chitietsanpham', function () {
                return $this->chitietsanpham->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'tenSp' => $detail->tenSp,
                        'id_mausac' => $detail->id_mausac,
                        'id_size' => $detail->id_size,
                        'soLuong' => (int) $detail->soLuong,
                        'gia' => $detail->gia ? (float) $detail->gia : null,
                        'gia_khuyenmai' => $detail->gia_khuyenmai ? (float) $detail->gia_khuyenmai : null,
                        'mausac' => $detail->relationLoaded('mausac') ? [
                            'id' => $detail->mausac->id,
                            'ten' => $detail->mausac->ten,
                        ] : null,
                        'size' => $detail->relationLoaded('size') ? [
                            'id' => $detail->size->id,
                            'ten' => $detail->size->ten,
                        ] : null,
                    ];
                });
            }),
            'binhluan' => $this->whenLoaded('binhluan', function () {
                return $this->binhluan->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'noiDung' => $comment->noiDung,
                        'diemDanhGia' => $comment->diemDanhGia ? (int) $comment->diemDanhGia : null,
                        'created_at' => $comment->created_at ? $comment->created_at->toISOString() : null,
                    ];
                });
            }),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
