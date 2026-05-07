<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TestController extends Controller
{
    /**
     * Menampilkan antarmuka pengujian (Throw-away UI).
     */
    public function index()
    {
        // Mengambil semua artikel beserta relasi gambarnya
        // Diurutkan berdasarkan artikel terbaru (published_at)
        $articles = Article::with('images')->latest('published_at')->get();
        
        return view('test-engine', compact('articles'));
    }

    /**
     * Memicu perintah command artisan 'news:fetch'.
     */
    public function fetch(Request $request)
    {
        // Menjalankan command secara sinkronus
        Artisan::call('news:fetch');
        
        // Mengambil output command jika dibutuhkan (bisa disave ke log)
        // $output = Artisan::output();
        
        // Redirect kembali ke halaman dengan pesan sukses
        return back()->with('success', 'Mesin pencari berita (news:fetch) berhasil dijalankan!');
    }
}
