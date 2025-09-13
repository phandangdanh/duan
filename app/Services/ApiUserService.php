<?php

namespace App\Services;

use App\Repositories\ApiUserRepository;
use App\Models\UserModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class ApiUserService
{
    protected $apiUserRepository;

    public function __construct(ApiUserRepository $apiUserRepository)
    {
        $this->apiUserRepository = $apiUserRepository;
    }

    /**
     * Get users with pagination and filters
     */
    public function getUsers(array $filters = [], int $perPage = 10, bool $returnAll = false): array
    {
        $result = $this->apiUserRepository->getUsers($filters, $perPage, $returnAll);
        
        if ($returnAll) {
            return [
                'data' => $result,
                'pagination' => null,
            ];
        }
        
        $currentPage = $result->currentPage();
        $lastPage = $result->lastPage();
        $pages = $lastPage > 0 ? range(1, $lastPage) : [];
        
        return [
            'data' => $result->items(),
            'pagination' => [
                'current_page' => $currentPage,
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'last_page' => $lastPage,
                'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
                'next_page' => $currentPage < $lastPage ? $currentPage + 1 : null,
                'prev_url' => $currentPage > 1 ? $result->url($currentPage - 1) : null,
                'next_url' => $currentPage < $lastPage ? $result->url($currentPage + 1) : null,
                'first_url' => $lastPage > 0 ? $result->url(1) : null,
                'last_url' => $lastPage > 0 ? $result->url($lastPage) : null,
                'pages' => $pages,
                'path' => $result->path(),
            ],
        ];
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?UserModel
    {
        return $this->apiUserRepository->getUserById($id);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): UserModel
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return $this->apiUserRepository->createUser($data);
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): ?UserModel
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        // Nếu client gửi giá trị không hợp lệ cho image/birthday thì bỏ qua để giữ nguyên dữ liệu gốc
        if (array_key_exists('image', $data)) {
            $img = trim((string) $data['image']);
            if ($img === '') {
                unset($data['image']);
            } else {
                // Nếu chỉ là tên file (không phải URL), build URL đầy đủ theo uploads/avatars
                if (!filter_var($img, FILTER_VALIDATE_URL)) {
                    $base = url('/uploads/avatars');
                    $data['image'] = rtrim($base, '/') . '/' . ltrim($img, '/');
                }
            }
        }
        if (array_key_exists('birthday', $data)) {
            $bd = $data['birthday'];
            if ($bd === '' || (strtotime($bd) === false)) {
                unset($data['birthday']);
            }
        }
        
        $updated = $this->apiUserRepository->updateUser($id, $data);
        
        if (!$updated) {
            return null;
        }
        
        return $this->apiUserRepository->getUserById($id);
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): bool
    {
        return $this->apiUserRepository->deleteUser($id);
    }
}
