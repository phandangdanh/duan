<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDanhMucRequest;
use App\Http\Requests\UpdateDanhMucRequest;
use App\Services\Interfaces\DanhMucServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DanhMucController extends Controller
{
    protected $danhMucService;

    public function __construct(DanhMucServiceInterface $danhMucService)
    {
        $this->danhMucService = $danhMucService;
    }

    public function index(Request $request)
    {
        try {
            
            clearUserSessionErrors();
            
            $config = [
                'seo' => [
                    'title' => 'Quản lý danh mục',
                    'table' => 'DANH SÁCH DANH MỤC'
                ]
            ];

            // Xử lý filter
            // Chuẩn hóa perpage để tránh lỗi nhân/chia với chuỗi
            $perPageRaw = $request->get('perpage');
            if ($perPageRaw === 'all' || $perPageRaw === 'tat_ca') {
                $perPage = null; // service sẽ hiểu là lấy tất cả
            } elseif (!is_null($perPageRaw) && !is_numeric($perPageRaw)) {
                $perPage = 10;
            } else {
                $perPage = is_null($perPageRaw) ? 10 : (int) $perPageRaw;
            }

            $filters = [
                'keyword' => $request->get('keyword'),
                'status' => $request->get('status'),
                'sort' => $request->get('sort', 'name'),
                'perpage' => $perPage,
            ];

            // Nếu có filter thì search, không thì lấy tất cả
            if (!empty($filters['keyword']) || !empty($filters['status']) || !empty($filters['sort'])) {
                $categories = $this->danhMucService->searchCategories($filters);
            } else {
                $categories = $this->danhMucService->paginate($filters['perpage']);
            }

            // Lấy thống kê tổng quan
            $stats = $this->danhMucService->getDanhMucStats();

            return view('backend.danhmuc.index', compact('config', 'categories', 'filters', 'stats'));
        } catch (\Exception $e) {
            Log::error('DanhMuc index error: ' . $e->getMessage());
            return view('backend.danhmuc.index', [
                'config' => [
                    'seo' => [
                        'title' => 'Quản lý danh mục',
                        'table' => 'DANH SÁCH DANH MỤC'
                    ]
                ],
                'categories' => collect([]),
                'filters' => [],
                'stats' => [
                    'total_categories' => 0,
                    'active_categories' => 0,
                    'inactive_categories' => 0,
                    'root_categories' => 0,
                    'sub_categories' => 0,
                    'categories_with_image' => 0,
                    'categories_without_image' => 0,
                ]
            ])->with('error', 'Có lỗi xảy ra khi tải dữ liệu!');
        }
    }

    public function statistics(Request $request)
    {
        $range = $request->get('range', 30);
        if (!in_array((int)$range, [7,30,90])) { $range = 30; }

        $stats = $this->danhMucService->getDanhMucStats();

        // Chuyển dữ liệu biểu đồ theo ngày gần $range ngày
        $labels = [];
        $data = [];
        for ($i=$range-1; $i>=0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $labels[] = $date->format('d/m');
            $data[] = \App\Models\DanhMuc::whereDate('created_at', $date)->count();
        }

        $topCategories = $this->danhMucService->getTopCategories(10);

        return view('backend.danhmuc.statistics', compact('stats','labels','data','range','topCategories'));
    }

    public function create()
    {
        $config = [
            'seo' => [
                'title' => 'Thêm danh mục mới',
                'table' => 'THÊM DANH MỤC MỚI'
            ]
        ];

        // Lấy cây danh mục để hiển thị dạng phân cấp
        $categories = $this->danhMucService->getCategoryTree();

        return view('backend.danhmuc.create', compact('config', 'categories'));
    }

    public function store(StoreDanhMucRequest $request)
    {
        try {
            // Xóa session error không liên quan
            clearUserSessionErrors();
            
            // Validation đã được xử lý tự động bởi StoreDanhMucRequest
            $category = $this->danhMucService->create($request);

            if (!$category) {
                return redirect()->back()
                    ->with('error', 'Không thể tạo danh mục!')
                    ->withInput();
            }

            return redirect()->route('danhmuc.index')
                ->with('success', 'Thêm danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Category store error: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            
            $errorMessage = 'Có lỗi xảy ra khi thêm danh mục!';
            if (str_contains($e->getMessage(), 'duplicate entry')) {
                $errorMessage = 'Tên danh mục hoặc slug đã tồn tại!';
            } elseif (str_contains($e->getMessage(), 'parent_id')) {
                $errorMessage = 'Danh mục cha không tồn tại!';
            } elseif (str_contains($e->getMessage(), 'upload ảnh')) {
                $errorMessage = 'Lỗi upload ảnh: ' . $e->getMessage();
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function show($id)
    {
        // Có thể implement nếu cần
        return redirect()->route('danhmuc.index');
    }

    public function edit($id)
    {
        try {
            $config = [
                'seo' => [
                    'title' => 'Chỉnh sửa danh mục',
                    'table' => 'CHỈNH SỬA DANH MỤC'
                ]
            ];

            $category = $this->danhMucService->find($id);
            if (!$category) {
                return redirect()->route('danhmuc.index')
                    ->with('error', 'Không tìm thấy danh mục!');
            }

            // Lấy cây danh mục để hiển thị dạng phân cấp
            $categories = $this->danhMucService->getCategoryTree();

            return view('backend.danhmuc.edit', compact('config', 'category', 'categories'));
        } catch (\Exception $e) {
            Log::error('Category edit error: ' . $e->getMessage());
            return redirect()->route('danhmuc.index')
                ->with('error', 'Có lỗi xảy ra!');
        }
    }

    public function update(UpdateDanhMucRequest $request, $id)
    {
        try {
            // Xóa session error không liên quan
            clearUserSessionErrors();
            
            // Validation đã được xử lý tự động bởi UpdateDanhMucRequest
            $category = $this->danhMucService->update($id, $request);

            if (!$category) {
                return redirect()->back()
                    ->with('error', 'Không thể cập nhật danh mục!')
                    ->withInput();
            }

            return redirect()->route('danhmuc.index')
                ->with('success', 'Cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Category update error: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            
            $errorMessage = 'Có lỗi xảy ra khi cập nhật danh mục!';
            if (str_contains($e->getMessage(), 'duplicate entry')) {
                $errorMessage = 'Tên danh mục hoặc slug đã tồn tại!';
            } elseif (str_contains($e->getMessage(), 'parent_id')) {
                $errorMessage = 'Danh mục cha không tồn tại!';
            } elseif (str_contains($e->getMessage(), 'upload ảnh')) {
                $errorMessage = 'Lỗi upload ảnh: ' . $e->getMessage();
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Xóa session error không liên quan
            clearUserSessionErrors();
            
            $category = $this->danhMucService->delete($id);

            if (!$category) {
                return redirect()->back()
                    ->with('error', 'Không thể xóa danh mục!');
            }

            return redirect()->route('danhmuc.index')
                ->with('success', 'Xóa danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Category delete error: ' . $e->getMessage());
            
            $errorMessage = 'Có lỗi xảy ra khi xóa danh mục!';
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $errorMessage = 'Không thể xóa danh mục vì có danh mục con!';
            } elseif (str_contains($e->getMessage(), 'upload ảnh')) {
                $errorMessage = 'Lỗi xóa ảnh: ' . $e->getMessage();
            }
            
            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            // Xóa các session error không liên quan trước khi xử lý
            clearUserSessionErrors();
            
            $category = $this->danhMucService->find($id);
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy danh mục!'
                ], 404);
            }

            $newStatus = $category->status === 'active' ? 'inactive' : 'active';
            $this->danhMucService->toggleStatus($id, $newStatus);

            // Thông điệp rõ ràng theo trạng thái mới
            $message = $newStatus === 'active' 
                ? 'Danh mục đã được kích hoạt!'
                : 'Danh mục đã bị vô hiệu hóa!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Category toggle status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái!'
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $filters = $request->only(['keyword', 'status', 'sort']);
            $categories = $this->danhMucService->searchCategories($filters);

            return view('backend.danhmuc.component.table', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Category search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm!'
            ], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        try {
            $actionType = $request->input('action_type');
            $ids = $request->input('ids', []);

            // Chuyển đổi string thành array nếu cần
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một danh mục!');
            }

            switch ($actionType) {
                case 'delete':
                    $result = $this->danhMucService->deleteMany($ids);
                    $message = 'Xóa thành công ' . count($ids) . ' danh mục!';
                    break;
                case 'activate':
                    $result = $this->danhMucService->updateStatusMany($ids, 'active');
                    $message = 'Kích hoạt thành công ' . count($ids) . ' danh mục!';
                    break;
                case 'deactivate':
                    $result = $this->danhMucService->updateStatusMany($ids, 'inactive');
                    $message = 'Vô hiệu hóa thành công ' . count($ids) . ' danh mục!';
                    break;
                default:
                    return redirect()->back()->with('error', 'Hành động không hợp lệ!');
            }

            if ($result) {
                return redirect()->back()->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi thực hiện hành động!');
            }
        } catch (\Exception $e) {
            Log::error('Category bulk action error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra!');
        }
    }

}
