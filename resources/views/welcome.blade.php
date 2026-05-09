<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warta Jogja — Kompilasi Informasi Yogyakarta</title>
    <meta name="description" content="Warta Jogja adalah portal agregasi berita lokal Yogyakarta yang mengumpulkan informasi terkini dari berbagai sumber terpercaya di sekitar DIY.">

    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Transisi halus untuk card hover */
        .news-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px -8px rgba(0,0,0,0.12);
        }
        /* Navbar scroll effect */
        .navbar-scrolled {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background-color: rgba(255,255,255,0.9) !important;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    {{-- ============================================================ --}}
    {{-- 1. TOP NAVBAR (STICKY)                                       --}}
    {{-- ============================================================ --}}
    <nav id="navbar" class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo / App Name --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-black text-sm">W</span>
                    </div>
                    <span class="text-xl font-black text-gray-900 tracking-tight">Warta<span class="text-blue-600">Jogja</span></span>
                </a>

                {{-- Navigation Links (Desktop) --}}
                <div class="hidden md:flex items-center gap-1">
                    {{-- Link "Semua" --}}
                    <a href="{{ route('home') }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150
                              {{ !request()->filled('category') && !request()->filled('search') 
                                 ? 'text-blue-600 bg-blue-50 font-semibold' 
                                 : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Semua
                    </a>

                    {{-- Loop Kategori Dinamis --}}
                    @foreach ($categories as $category)
                        <a href="{{ route('home', ['category' => $category->slug]) }}"
                           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150
                                  {{ request('category') === $category->slug 
                                     ? 'text-blue-600 bg-blue-50 font-semibold' 
                                     : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>

                {{-- Mobile Menu Toggle --}}
                <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

            </div>

            {{-- Mobile Dropdown --}}
            <div id="mobile-menu" class="hidden md:hidden pb-3 pt-1 border-t border-gray-100">
                <div class="flex flex-col gap-1 mt-2">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 text-sm font-medium rounded-lg
                              {{ !request()->filled('category') ? 'text-blue-600 bg-blue-50' : 'text-gray-600' }}">
                        Semua
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('home', ['category' => $category->slug]) }}"
                           class="px-3 py-2 text-sm font-medium rounded-lg
                                  {{ request('category') === $category->slug ? 'text-blue-600 bg-blue-50' : 'text-gray-600' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>

    {{-- ============================================================ --}}
    {{-- 2. HERO SECTION (SEARCH FOCUS)                               --}}
    {{-- ============================================================ --}}
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 text-center">

            {{-- Eyebrow / keterangan kecil --}}
            <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-5">
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                Diperbarui otomatis setiap hari
            </div>

            {{-- Headline --}}
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight mb-4">
                Kompilasi Informasi<br>
                <span class="text-blue-600">Yogyakarta</span> Hari Ini
            </h1>
            <p class="text-gray-500 text-base sm:text-lg mb-10">
                Agregasi berita lokal dari berbagai portal terpercaya, tersaji dalam satu halaman.
            </p>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('home') }}" class="relative max-w-2xl mx-auto">
                {{-- Preserve filter kategori jika ada --}}
                @if(request()->filled('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <div class="flex items-center bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-lg focus-within:shadow-lg focus-within:border-blue-400 transition-all duration-200 overflow-hidden">
                    <svg class="flex-shrink-0 ml-5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        id="search-input"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari info rekayasa lalu lintas, cuaca ekstrem, atau acara lokal..."
                        class="flex-1 px-4 py-4 text-sm text-gray-700 bg-transparent border-none outline-none placeholder-gray-400"
                    >
                    <button
                        type="submit"
                        class="flex-shrink-0 m-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors duration-150"
                    >
                        Temukan
                    </button>
                </div>
            </form>

            {{-- Tampilkan info hasil pencarian --}}
            @if(request()->filled('search'))
                <p class="mt-4 text-sm text-gray-500">
                    Menampilkan hasil untuk: <span class="font-semibold text-gray-800">"{{ request('search') }}"</span>
                    — <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Hapus filter</a>
                </p>
            @endif

        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 3. NEWS CARD GRID (THE CORE ENGINE)                          --}}
    {{-- ============================================================ --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Section Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    @if(request()->filled('category'))
                        {{ $categories->where('slug', request('category'))->first()?->name ?? 'Kategori' }}
                    @elseif(request()->filled('search'))
                        Hasil Pencarian
                    @else
                        Berita Terbaru
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $articles->count() }} artikel ditemukan</p>
            </div>
        </div>

        {{-- Grid atau Empty State --}}
        @if($articles->isEmpty())
            <div class="text-center py-24">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak ada berita ditemukan</h3>
                <p class="text-sm text-gray-400 mb-6">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                    Lihat Semua Berita
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($articles as $article)
                    @php
                        $category = $article->source?->category;
                        $image    = $article->images->first();
                        $source   = $article->source;

                        // Warna badge berdasarkan nama/slug kategori
                        $badgeColors = [
                            'kuliner'          => 'bg-orange-100 text-orange-700',
                            'kuliner-jogja'    => 'bg-orange-100 text-orange-700',
                            'wisata'           => 'bg-green-100 text-green-700',
                            'wisata-jogja'     => 'bg-green-100 text-green-700',
                            'hiburan'          => 'bg-purple-100 text-purple-700',
                            'hiburan-film'     => 'bg-purple-100 text-purple-700',
                            'pemerintahan'     => 'bg-blue-100 text-blue-700',
                            'berita-utama-pemerintahan' => 'bg-red-100 text-red-700',
                        ];
                        $slug       = $category?->slug ?? '';
                        $badgeClass = 'bg-gray-100 text-gray-600'; // default
                        foreach ($badgeColors as $key => $color) {
                            if (str_contains($slug, $key)) {
                                $badgeClass = $color;
                                break;
                            }
                        }
                    @endphp

                    {{-- NEWS CARD --}}
                    <article class="news-card relative bg-white rounded-2xl overflow-hidden border border-gray-100 flex flex-col">

                        {{-- Thumbnail --}}
                        <div class="aspect-video overflow-hidden bg-gray-100">
                            @if($image)
                                <img
                                    src="{{ $image->image_url }}"
                                    alt="{{ $article->title }}"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    loading="lazy"
                                    onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gray-100\'><svg class=\'w-10 h-10 text-gray-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>'"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Card Content --}}
                        <div class="flex flex-col flex-1 p-5">

                            {{-- Category Badge --}}
                            @if($category)
                                <div class="mb-3">
                                    <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }}">
                                        {{ $category->name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Title dengan Semantic Click Hack --}}
                            {{-- Tag <a> hanya di judul, tapi after:absolute after:inset-0 membuat seluruh card clickable --}}
                            <h3 class="text-base font-bold text-gray-900 leading-snug mb-2 line-clamp-2">
                                <a
                                    href="{{ $article->link }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="hover:text-blue-600 transition-colors after:absolute after:inset-0"
                                >
                                    {{ $article->title }}
                                </a>
                            </h3>

                            {{-- Snippet --}}
                            @if($article->description)
                                <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 flex-1">
                                    {{ $article->description }}
                                </p>
                            @endif

                            {{-- Metadata (Sumber & Waktu) --}}
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <div class="w-4 h-4 bg-blue-600 rounded flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-black" style="font-size: 8px;">W</span>
                                    </div>
                                    <span class="text-xs text-gray-500 font-medium truncate">
                                        {{ $source?->name ?? 'Sumber tidak diketahui' }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400 flex-shrink-0 ml-2">
                                    {{ $article->published_at?->diffForHumans() ?? $article->created_at->diffForHumans() }}
                                </span>
                            </div>

                        </div>
                    </article>
                @endforeach
            </div>
        @endif

    </main>

    {{-- ============================================================ --}}
    {{-- 4. FOOTER                                                     --}}
    {{-- ============================================================ --}}
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <div class="w-6 h-6 bg-blue-600 rounded-md flex items-center justify-center">
                    <span class="text-white font-black text-xs">W</span>
                </div>
                <span class="text-sm font-bold text-gray-700">WartaJogja</span>
            </div>
            <p class="text-xs text-gray-400 max-w-xl mx-auto leading-relaxed">
                Sistem agregasi informasi otomatis. Seluruh hak cipta artikel dan gambar milik portal sumber masing-masing.
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Dikembangkan untuk Tujuan Akademik.
            </p>
        </div>
    </footer>

    {{-- ============================================================ --}}
    {{-- JAVASCRIPT MINIMAL                                           --}}
    {{-- ============================================================ --}}
    <script>
        // Toggle mobile menu
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        btn?.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Navbar scroll effect (tambah blur saat scroll)
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    </script>

</body>
</html>
