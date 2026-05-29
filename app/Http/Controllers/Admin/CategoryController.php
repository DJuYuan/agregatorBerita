<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('sources')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $name = $validated['name'];
        $keywords = $validated['keywords'] ?? null;

        Category::create([
            'name'     => $name,
            'slug'     => Str::slug($name),
            'keywords' => $keywords,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil ditambahkan.");
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil dihapus.");
    }
}
