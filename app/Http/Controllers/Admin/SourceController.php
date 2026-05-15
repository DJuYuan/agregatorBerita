<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSourceRequest;
use App\Models\Category;
use App\Models\Source;

class SourceController extends Controller
{
    public function index()
    {
        $sources = Source::with('category')->orderBy('name')->get();
        return view('admin.sources.index', compact('sources'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.sources.create', compact('categories'));
    }

    public function store(StoreSourceRequest $request)
    {
        $validated = $request->validated();

        $source = Source::create([
            'name'        => $validated['name'],
            'category_id' => $validated['category_id'],
            'rss_url'     => $validated['rss_url'],
            'is_active'   => true,
        ]);

        return redirect()->route('admin.sources.index')
            ->with('success', "Sumber RSS \"{$source->name}\" berhasil ditambahkan.");
    }

    public function destroy(Source $source)
    {
        $name = $source->name;
        $source->delete();

        return redirect()->route('admin.sources.index')
            ->with('success', "Sumber RSS \"{$name}\" berhasil dihapus.");
    }

    public function toggle(Source $source)
    {
        $source->update(['is_active' => !$source->is_active]);
        $status = $source->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.sources.index')
            ->with('success', "Sumber \"{$source->name}\" berhasil {$status}.");
    }
}
