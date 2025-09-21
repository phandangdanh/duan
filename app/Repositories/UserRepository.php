<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\UserModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(UserModel $model)
    {
        $this->model = $model;
    }
    public function getAllPaginate()
    {
        return UserModel::with(['province', 'district', 'ward'])
            ->orderBy('created_at', 'desc') 
            ->paginate(10);
    }

    public function findById($id)
    {
        return UserModel::with(['province', 'district', 'ward'])->find($id);
    }

    public function find($id)
    {
        return UserModel::find($id);
    }

    public function update($id, $data)
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = $this->findById($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    public function search(array $filters)
    {
        $query = UserModel::query();

        if (!empty($filters['keyword'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['keyword'] . '%')
                ->orWhere('email', 'like', '%' . $filters['keyword'] . '%')
                ->orWhere('phone', 'like', '%' . $filters['keyword'] . '%')
                ->orWhere('address', 'like', '%' . $filters['keyword'] . '%'); 
            });
        }

        if (!empty($filters['user_catalogue_id']) && $filters['user_catalogue_id'] != 0) {
            $query->where('user_catalogue_id', $filters['user_catalogue_id']);
        }

        $perPage = $filters['perpage'] ?? 10;
        $query->orderBy('created_at', 'desc'); 
    
        if ($perPage == 'all') {
            return $query->get(); 
        }
        return $query->paginate($perPage);
    }

    public function deleteMany(array $ids)
    {
        return UserModel::whereIn('id', $ids)->delete();
    }

    public function updateStatusMany(array $ids, $status)
    {
        return UserModel::whereIn('id', $ids)->update(['status' => $status]);
    }

    public function updateRoleMany(array $ids, $role)
    {
        return UserModel::whereIn('id', $ids)->update(['user_catalogue_id' => $role]);
    }

    public function findByEmail(string $email)
    {
        return UserModel::where('email', $email)->first();
    }
}
