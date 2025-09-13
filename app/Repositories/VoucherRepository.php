<?php

namespace App\Repositories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class VoucherRepository
{
    protected $model;

    public function __construct(Voucher $model)
    {
        $this->model = $model;
    }

    /**
     * Lấy danh sách voucher với phân trang
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Áp dụng filters
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('ma_voucher', 'like', "%{$search}%")
                  ->orWhere('ten_voucher', 'like', "%{$search}%")
                  ->orWhere('mota', 'like', "%{$search}%");
            });
        }

        if (isset($filters['trang_thai']) && $filters['trang_thai'] !== '') {
            $query->where('trang_thai', $filters['trang_thai']);
        }

        if (isset($filters['loai_giam_gia']) && $filters['loai_giam_gia']) {
            $query->where('loai_giam_gia', $filters['loai_giam_gia']);
        }

        if (isset($filters['ngay_bat_dau']) && $filters['ngay_bat_dau']) {
            $query->whereDate('ngay_bat_dau', '>=', $filters['ngay_bat_dau']);
        }

        if (isset($filters['ngay_ket_thuc']) && $filters['ngay_ket_thuc']) {
            $query->whereDate('ngay_ket_thuc', '<=', $filters['ngay_ket_thuc']);
        }

        if (isset($filters['trang_thai_hoat_dong'])) {
            switch ($filters['trang_thai_hoat_dong']) {
                case 'dang_hoat_dong':
                    $query->active()->valid()->available();
                    break;
                case 'chua_bat_dau':
                    $query->where('ngay_bat_dau', '>', Carbon::now());
                    break;
                case 'da_het_han':
                    $query->where('ngay_ket_thuc', '<', Carbon::now());
                    break;
                case 'het_so_luong':
                    $query->whereRaw('so_luong <= so_luong_da_su_dung');
                    break;
                case 'tam_dung':
                    $query->where('trang_thai', 0);
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Lấy tất cả voucher
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        // Áp dụng filters
        if (isset($filters['trang_thai']) && $filters['trang_thai'] !== '') {
            $query->where('trang_thai', $filters['trang_thai']);
        }

        if (isset($filters['loai_giam_gia']) && $filters['loai_giam_gia']) {
            $query->where('loai_giam_gia', $filters['loai_giam_gia']);
        }

        if (isset($filters['usable']) && $filters['usable']) {
            $query->usable();
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Lấy voucher theo ID
     */
    public function find(int $id): ?Voucher
    {
        return $this->model->find($id);
    }

    /**
     * Lấy voucher theo mã
     */
    public function findByCode(string $maVoucher): ?Voucher
    {
        return $this->model->where('ma_voucher', $maVoucher)->first();
    }

    /**
     * Tạo voucher mới
     */
    public function create(array $data): Voucher
    {
        return $this->model->create($data);
    }

    /**
     * Cập nhật voucher
     */
    public function update(int $id, array $data): bool
    {
        $voucher = $this->find($id);
        if (!$voucher) {
            return false;
        }

        return $voucher->update($data);
    }

    /**
     * Xóa voucher
     */
    public function delete(int $id): bool
    {
        $voucher = $this->find($id);
        if (!$voucher) {
            return false;
        }

        return $voucher->delete();
    }

    /**
     * Lấy voucher có thể sử dụng
     */
    public function getUsableVouchers(): Collection
    {
        return $this->model->usable()->get();
    }

    /**
     * Lấy voucher theo mã và kiểm tra có thể sử dụng
     */
    public function getUsableVoucherByCode(string $maVoucher): ?Voucher
    {
        return $this->model->usable()->where('ma_voucher', $maVoucher)->first();
    }

    /**
     * Lấy voucher có thể áp dụng cho đơn hàng
     */
    public function getApplicableVouchers(float $orderTotal): Collection
    {
        return $this->model->usable()
            ->where('gia_tri_toi_thieu', '<=', $orderTotal)
            ->get();
    }

    /**
     * Lấy thống kê voucher
     */
    public function getStatistics(): array
    {
        $total = $this->model->count();
        $active = $this->model->active()->count();
        $usable = $this->model->usable()->count();
        $expired = $this->model->where('ngay_ket_thuc', '<', Carbon::now())->count();
        $outOfStock = $this->model->whereRaw('so_luong <= so_luong_da_su_dung')->count();

        return [
            'total' => $total,
            'active' => $active,
            'usable' => $usable,
            'expired' => $expired,
            'out_of_stock' => $outOfStock,
            'inactive' => $total - $active
        ];
    }

    /**
     * Lấy voucher sắp hết hạn
     */
    public function getExpiringSoon(int $days = 7): Collection
    {
        $expiryDate = Carbon::now()->addDays($days);
        
        return $this->model->active()
            ->where('ngay_ket_thuc', '<=', $expiryDate)
            ->where('ngay_ket_thuc', '>', Carbon::now())
            ->orderBy('ngay_ket_thuc', 'asc')
            ->get();
    }

    /**
     * Lấy voucher có số lượng thấp
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return $this->model->active()
            ->whereRaw('(so_luong - so_luong_da_su_dung) <= ?', [$threshold])
            ->whereRaw('so_luong > so_luong_da_su_dung')
            ->orderByRaw('(so_luong - so_luong_da_su_dung) ASC')
            ->get();
    }

    /**
     * Tìm kiếm voucher
     */
    public function search(string $keyword): Collection
    {
        return $this->model->where(function ($query) use ($keyword) {
            $query->where('ma_voucher', 'like', "%{$keyword}%")
                  ->orWhere('ten_voucher', 'like', "%{$keyword}%")
                  ->orWhere('mota', 'like', "%{$keyword}%");
        })->orderBy('created_at', 'desc')->get();
    }

    /**
     * Lấy voucher theo khoảng thời gian
     */
    public function getByDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy voucher phổ biến nhất (sử dụng nhiều nhất)
     */
    public function getMostUsed(int $limit = 10): Collection
    {
        return $this->model->orderBy('so_luong_da_su_dung', 'desc')
            ->limit($limit)
            ->get();
    }
}
