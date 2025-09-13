<?php

namespace App\Repositories;

use App\Repositories\Interfaces\WardRepositoryInterface;
use App\Models\Ward;

/**
 * Class WardRepository
 * @package App\Repositories
 */
class WardRepository extends BaseRepository implements WardRepositoryInterface
{
    protected $model;
    public function __construct(Ward $model)
    {
        $this->model = $model;
    }
    public function getByDistrictId(int $districtId = 0)
    {
        return $this->model->where('district_code', '=', $districtId)->get();
    }
}
