<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Halaman utama publik — menampilkan semua berita terbaru.
     * Menggunakan Eager Loading untuk mencegah N+1 Query Problem
     * (source.category dan images dimuat bersamaan dalam satu query).
     */
    public function index(Request $request)
    {
        // Tarik semua kategori untuk ditampilkan di Navbar secara dinamis
        $categories = Category::all();

        // Bangun query dasar dengan Eager Loading optimal
        $query = Article::with(['source.category', 'images'])->latest();

        // Filter pencarian (GET standar, tanpa Livewire/AJAX)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori jika dipilih dari navbar
        if ($request->filled('category')) {
            $query->whereHas('source.category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        $articles = $query->get();

        // Kirim data ke view
        return view('welcome', compact('articles', 'categories'));
    }
}
