<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryIndexRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
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
}
