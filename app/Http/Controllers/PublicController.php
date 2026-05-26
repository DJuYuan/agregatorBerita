<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    /**
     * Halaman utama publik — menampilkan berita terbaru dengan paginasi kursor.
     *
     * Arsitektur:
     * - Cache::remember (300 detik) → men-bypass DB sepenuhnya pada hit kedua.
     * - cursorPaginate(15)          → tidak ada OFFSET lag, performa konstan O(1) di halaman berapapun.
     * - scopeFilter()               → logika WHERE bersih di level model, controller tetap ramping.
     * - with(['source.category', 'images']) → Eager Loading, mencegah N+1 Query Problem.
     */
    public function index(Request $request)
    {
        // ── 1. Tarik semua kategori (navbar) — cache 10 menit ────────────
        $categories = Cache::remember('categories_nav', 600, fn() => Category::all());

        // ── 2. Bangun cache key yang unik berdasarkan seluruh parameter URI ─
        $filters = [
            'search'   => $request->input('search'),
            'category' => $request->input('category'),
            'cursor'   => $request->input('cursor'), // cursor paginasi ikut di-cache
        ];
        $cacheKey = 'articles_' . md5(serialize(array_filter($filters)));

        // ── 3. Ambil data artikel — cache 5 menit per kombinasi filter ───
        $articles = Cache::remember($cacheKey, 300, function () use ($filters) {
            return Article::with(['source.category', 'images'])
                ->filter([
                    'search'   => $filters['search'],
                    'category' => $filters['category'],
                ])
                ->latest('published_at')
                ->cursorPaginate(15);
        });

        // Pastikan parameter query lain (search, category) ikut terbawa di link paginasi
        $articles->appends($request->except('cursor'));

        return view('welcome', compact('articles', 'categories'));
    }

    /**
     * Rute perantara untuk melacak klik artikel sebelum melempar ke situs asli.
     */
    public function go($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        
        // Catat klik
        $article->increment('clicks');

        // Arahkan ke URL asli di tab baru (ditangani oleh atribut target="_blank" di view)
        return redirect()->away($article->link);
    }
}
