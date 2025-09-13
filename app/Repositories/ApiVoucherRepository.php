<?php

namespace App\Repositories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ApiVoucherRepository
{
    public function getVouchers(array $filters, int $perPage, bool $returnAll)
    {
        $query = Voucher::query();

        // Apply filters
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('ma_voucher', 'like', "%{$keyword}%")
                  ->orWhere('ten_voucher', 'like', "%{$keyword}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('trang_thai', $filters['status']);
        }

        if (!empty($filters['discount_type'])) {
            $query->where('loai_giam_gia', $filters['discount_type']);
        }

        if ($filters['usable'] ?? false) {
            $query->usable();
        }

        $query->orderBy('created_at', 'desc');

        if ($returnAll) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Voucher
    {
        return Voucher::find($id);
    }


    public function create(array $data): Voucher
    {
        // Set default values
        $data['so_luong_da_su_dung'] = 0;
        $data['trang_thai'] = $data['trang_thai'] ?? 1;

        return Voucher::create($data);
    }

    public function update(int $id, array $data): ?Voucher
    {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            return null;
        }

        $voucher->update($data);
        return $voucher->fresh();
    }

    public function delete(int $id): bool
    {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            return false;
        }

        return $voucher->delete();
    }

}
