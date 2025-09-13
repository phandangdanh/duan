<?php

namespace App\Services\Interfaces;

interface SanPhamServiceInterface
{
    public function create(array $payload): int; // returns product id
    public function update(int $id, array $payload): void;
    public function list(array $filters): array; // returns ['items'=>Collection,'stats'=>array,'danhmucs'=>Collection,'pagination'=>mixed]
    public function getCreateData(): array; // ['danhmucs','mausacs','sizes']
    public function getEditData(int $id): array;   // ['sanpham','danhmucs','mausacs','sizes','variants']
    public function toggle(int $id): int;
    public function bulk(string $action, array $ids): array; // returns ['updated'=>array,'message'=>string]
    public function getStatistics(): array;
    public function getById(int $id): \App\Models\SanPham;
}


