<?php

namespace App\Repositories\Interfaces;

interface SanPhamRepositoryInterface
{
    public function createProduct(array $data): int; // returns product id
    public function insertDetails(int $productId, array $variants): void;
    public function saveMainImage(int $productId, string $relativeUrl): void;
    public function saveExtraImages(int $productId, array $relativeUrls): void;
    public function clearDetails(int $productId): void;
    public function listWithFilters(array $filters, $perPage): array; // returns ['items'=>Collection|Paginator, 'pagination'=>mixed]
    public function toggleStatus(int $id): int; // returns new status 0/1
    public function bulkUpdateStatus(array $ids, int $status): int; // affected rows
    public function bulkDelete(array $ids): void;
    public function getByIdWithRelations(int $id): \App\Models\SanPham;
    public function getStatistics(): array;
}


