<?php

namespace App\Services;

use App\Repositories\ApiVoucherRepository;
use App\Models\Voucher;

class ApiVoucherService
{
    public function __construct(private ApiVoucherRepository $repo)
    {
    }

    public function list(array $filters, int $perPage, bool $returnAll)
    {
        return $this->repo->getVouchers($filters, $perPage, $returnAll);
    }

    public function find(int $id): ?Voucher
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Voucher
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): ?Voucher
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

}
