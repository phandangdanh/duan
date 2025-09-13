<?php

namespace App\Services;

use App\Models\Voucher;
use App\Repositories\VoucherRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoucherService
{
    protected $voucherRepository;

    public function __construct(VoucherRepository $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
    }

    /**
     * Lấy danh sách voucher với phân trang
     */
    public function getPaginatedVouchers(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->voucherRepository->paginate($perPage, $filters);
    }

    /**
     * Lấy tất cả voucher
     */
    public function getAllVouchers(array $filters = []): Collection
    {
        return $this->voucherRepository->all($filters);
    }

    /**
     * Lấy voucher theo ID
     */
    public function getVoucherById(int $id): ?Voucher
    {
        return $this->voucherRepository->find($id);
    }

    /**
     * Lấy voucher theo mã
     */
    public function getVoucherByCode(string $maVoucher): ?Voucher
    {
        return $this->voucherRepository->findByCode($maVoucher);
    }

    /**
     * Tạo voucher mới
     */
    public function createVoucher(array $data): array
    {
        try {
            DB::beginTransaction();

            // Kiểm tra mã voucher trùng lặp
            if ($this->voucherRepository->findByCode($data['ma_voucher'])) {
                return [
                    'success' => false,
                    'message' => 'Mã voucher đã tồn tại',
                    'data' => null
                ];
            }

            // Tạo voucher
            $voucher = $this->voucherRepository->create($data);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Tạo voucher thành công',
                'data' => $voucher
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Lỗi khi tạo voucher: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Cập nhật voucher
     */
    public function updateVoucher(int $id, array $data): array
    {
        try {
            DB::beginTransaction();

            $voucher = $this->voucherRepository->find($id);
            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher không tồn tại',
                    'data' => null
                ];
            }

            // Kiểm tra mã voucher trùng lặp (trừ voucher hiện tại)
            if (isset($data['ma_voucher'])) {
                $existingVoucher = $this->voucherRepository->findByCode($data['ma_voucher']);
                if ($existingVoucher && $existingVoucher->id !== $id) {
                    return [
                        'success' => false,
                        'message' => 'Mã voucher đã tồn tại',
                        'data' => null
                    ];
                }
            }

            // Cập nhật voucher
            $updated = $this->voucherRepository->update($id, $data);

            if (!$updated) {
                return [
                    'success' => false,
                    'message' => 'Cập nhật voucher thất bại',
                    'data' => null
                ];
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Cập nhật voucher thành công',
                'data' => $voucher->fresh()
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Lỗi khi cập nhật voucher: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Xóa voucher
     */
    public function deleteVoucher(int $id): array
    {
        try {
            DB::beginTransaction();

            $voucher = $this->voucherRepository->find($id);
            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher không tồn tại',
                    'data' => null
                ];
            }

            // Có thể xóa voucher đã sử dụng (bỏ comment nếu muốn hạn chế)
            // if ($voucher->so_luong_da_su_dung > 0) {
            //     return [
            //         'success' => false,
            //         'message' => 'Không thể xóa voucher đã được sử dụng',
            //         'data' => null
            //     ];
            // }

            $deleted = $this->voucherRepository->delete($id);

            if (!$deleted) {
                return [
                    'success' => false,
                    'message' => 'Xóa voucher thất bại',
                    'data' => null
                ];
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Xóa voucher thành công',
                'data' => null
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Lỗi khi xóa voucher: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Lấy voucher có thể sử dụng
     */
    public function getUsableVouchers(): Collection
    {
        return $this->voucherRepository->getUsableVouchers();
    }

    /**
     * Lấy voucher theo mã và kiểm tra có thể sử dụng
     */
    public function getUsableVoucherByCode(string $maVoucher): ?Voucher
    {
        return $this->voucherRepository->getUsableVoucherByCode($maVoucher);
    }

    /**
     * Lấy voucher có thể áp dụng cho đơn hàng
     */
    public function getApplicableVouchers(float $orderTotal): Collection
    {
        return $this->voucherRepository->getApplicableVouchers($orderTotal);
    }

    /**
     * Kiểm tra và áp dụng voucher cho đơn hàng
     */
    public function applyVoucherToOrder(string $maVoucher, float $orderTotal): array
    {
        $voucher = $this->getUsableVoucherByCode($maVoucher);
        
        if (!$voucher) {
            return [
                'success' => false,
                'message' => 'Voucher không tồn tại hoặc không thể sử dụng',
                'data' => null
            ];
        }

        if (!$voucher->canApplyToOrder($orderTotal)) {
            return [
                'success' => false,
                'message' => 'Voucher không thể áp dụng cho đơn hàng này',
                'data' => null
            ];
        }

        $discountAmount = $voucher->calculateDiscount($orderTotal);

        return [
            'success' => true,
            'message' => 'Voucher có thể áp dụng',
            'data' => [
                'voucher' => $voucher,
                'discount_amount' => $discountAmount,
                'final_amount' => $orderTotal - $discountAmount
            ]
        ];
    }

    /**
     * Sử dụng voucher (tăng số lượng đã sử dụng)
     */
    public function useVoucher(int $voucherId): array
    {
        try {
            $voucher = $this->voucherRepository->find($voucherId);
            
            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher không tồn tại',
                    'data' => null
                ];
            }

            if (!$voucher->isUsable()) {
                return [
                    'success' => false,
                    'message' => 'Voucher không thể sử dụng',
                    'data' => null
                ];
            }

            $incremented = $voucher->incrementUsage();
            
            if (!$incremented) {
                return [
                    'success' => false,
                    'message' => 'Không thể sử dụng voucher',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'Sử dụng voucher thành công',
                'data' => $voucher->fresh()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi sử dụng voucher: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Hoàn trả voucher (giảm số lượng đã sử dụng)
     */
    public function refundVoucher(int $voucherId): array
    {
        try {
            $voucher = $this->voucherRepository->find($voucherId);
            
            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher không tồn tại',
                    'data' => null
                ];
            }

            $decremented = $voucher->decrementUsage();
            
            if (!$decremented) {
                return [
                    'success' => false,
                    'message' => 'Không thể hoàn trả voucher',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'Hoàn trả voucher thành công',
                'data' => $voucher->fresh()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi hoàn trả voucher: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Lấy thống kê voucher
     */
    public function getVoucherStatistics(): array
    {
        return $this->voucherRepository->getStatistics();
    }

    /**
     * Lấy voucher sắp hết hạn
     */
    public function getExpiringSoonVouchers(int $days = 7): Collection
    {
        return $this->voucherRepository->getExpiringSoon($days);
    }

    /**
     * Lấy voucher có số lượng thấp
     */
    public function getLowStockVouchers(int $threshold = 10): Collection
    {
        return $this->voucherRepository->getLowStock($threshold);
    }

    /**
     * Tìm kiếm voucher
     */
    public function searchVouchers(string $keyword): Collection
    {
        return $this->voucherRepository->search($keyword);
    }

    /**
     * Lấy voucher phổ biến nhất
     */
    public function getMostUsedVouchers(int $limit = 10): Collection
    {
        return $this->voucherRepository->getMostUsed($limit);
    }

    /**
     * Tạo mã voucher tự động
     */
    public function generateVoucherCode(string $prefix = 'VOUCHER'): string
    {
        do {
            $code = $prefix . '_' . strtoupper(uniqid());
        } while ($this->voucherRepository->findByCode($code));

        return $code;
    }

    /**
     * Kích hoạt/tạm dừng voucher
     */
    public function toggleVoucherStatus(int $id): array
    {
        try {
            $voucher = $this->voucherRepository->find($id);
            
            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher không tồn tại',
                    'data' => null
                ];
            }

            $newStatus = !$voucher->trang_thai;
            $this->voucherRepository->update($id, ['trang_thai' => $newStatus]);

            return [
                'success' => true,
                'message' => $newStatus ? 'Kích hoạt voucher thành công' : 'Tạm dừng voucher thành công',
                'data' => $voucher->fresh()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi thay đổi trạng thái voucher: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
