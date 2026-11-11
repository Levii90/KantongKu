<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    public function index(Request $request): View
    {
        $categories = Category::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $this->categoryService->createForUser($request->user(), $request->validated());

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Request $request, Category $category): View
    {
        abort_unless($category->user_id === $request->user()->id && ! $category->is_system_default, 404);

        return view('categories.form', compact('category'));
    }

    public function update(CategoryStoreRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->updateForUser($request->user(), $category, $request->validated());

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->categoryService->deleteForUser($request->user(), $category);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
