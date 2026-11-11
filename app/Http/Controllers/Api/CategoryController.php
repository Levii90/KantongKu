<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryIndexRequest;
use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    public function index(CategoryIndexRequest $request): JsonResponse
    {
        $query = Category::forUser($request->user());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->orderBy('name')->get()->map(function (Category $category) {
            return [
                'id' => (int) $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'icon' => $category->icon,
                'color' => $category->color,
                'is_system_default' => (bool) $category->is_system_default,
            ];
        })->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createForUser($request->user(), $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dibuat.',
            'data' => $this->serializeCategory($category),
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        abort_unless(Category::forUser(request()->user())->whereKey($category->id)->exists(), 404);

        return response()->json([
            'status' => 'success',
            'data' => $this->serializeCategory($category),
        ]);
    }

    public function update(Category $category, CategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->updateForUser($request->user(), $category, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $this->serializeCategory($category),
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteForUser(request()->user(), $category);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }

    private function serializeCategory(Category $category): array
    {
        return [
            'id' => (int) $category->id,
            'name' => $category->name,
            'type' => $category->type,
            'icon' => $category->icon,
            'color' => $category->color,
            'is_system_default' => (bool) $category->is_system_default,
        ];
    }
}
