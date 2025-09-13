<?php

namespace App\Repositories\Interfaces;

/**
 * Interface BaseRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all();
    public function find($id);
    public function create($payload);
}
