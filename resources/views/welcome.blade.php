<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MuaraJogja</title>
    <meta name="description" content="MuaraJogja adalah portal agregasi berita lokal Yogyakarta yang mengumpulkan informasi terkini dari berbagai sumber terpercaya di sekitar DIY.">
    <meta name="keywords" content="Berita Jogja, Yogyakarta, MuaraJogja, Berita Lokal, Info Jogja, DIY">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="MuaraJogja">
    <meta property="og:description" content="Portal agregasi berita lokal Yogyakarta dari berbagai sumber terpercaya.">
    <meta property="og:image" content="{{ asset('default-og.jpg') }}">

    {{-- Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="MuaraJogja">
    <meta property="twitter:description" content="Portal agregasi berita lokal Yogyakarta dari berbagai sumber terpercaya.">
    <meta property="twitter:image" content="{{ asset('default-og.jpg') }}">
    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans:  ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary:  '#000000',
                        canvas:   '#ffffff',
                        hairline: '#e0e0e0',
                        muted:    '#6b6b6b',
                    },
                }
            }
        }
    </script>

    {{-- Google Fonts: Playfair Display (serif editorial) + Inter (sans fungsional) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

    <style>
        /* ── Global reset tipografi ───────────────────────── */
        body { font-family: 'Inter', system-ui, sans-serif; background-color: #fff; color: #000; }

        /* ── Utilitas line-clamp ──────────────────────────── */
        .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .line-clamp-3 { display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }

        /* ── Kartu berita: hover via garis, bukan bayangan ── */
        .news-card {
            transition: border-color 0.15s ease;
        }
        .news-card:hover {
            border-color: #000 !important;
        }

        /* ── Placeholder thumbnail ────────────────────────── */
        .thumb-placeholder {
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Garis dekoratif pada judul seksi ─────────────── */
        .section-rule {
            border-top: 3px solid #000;
        }
    </style>
</head>
<body class="bg-canvas text-primary antialiased">

    {{-- ============================================================ --}}
    {{-- 1. TOP NAVBAR                                                 --}}
    {{-- ============================================================ --}}
    <nav id="navbar" class="sticky top-0 z-50 bg-canvas border-b border-hairline">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">

                {{-- Wordmark --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0">
                    {{-- Kotak hitam tajam sebagai penanda merek --}}
                    <div class="w-7 h-7 bg-primary flex items-center justify-center flex-shrink-0">
                        <span class="text-canvas font-sans font-bold text-xs tracking-widest">M</span>
                    </div>
                    <span class="font-serif text-lg font-bold text-primary tracking-tight leading-none">
                        Muara<span class="font-serif font-normal">Jogja</span>
                    </span>
                </a>

                {{-- Navigation Links (Desktop) --}}
                <div class="hidden md:flex items-center gap-0">
                    <a href="{{ route('home') }}"
                       class="px-4 py-5 font-sans font-bold text-xs tracking-widest uppercase border-b-2 transition-colors duration-150
                              {{ !request()->filled('category') && !request()->filled('search')
                                 ? 'border-primary text-primary'
                                 : 'border-transparent text-muted hover:text-primary' }}">
                        Semua
                    </a>

                    @foreach ($categories as $category)
                        <a href="{{ route('home', ['category' => $category->slug]) }}"
                           class="px-4 py-5 font-sans font-bold text-xs tracking-widest uppercase border-b-2 transition-colors duration-150
                                  {{ request('category') === $category->slug
                                     ? 'border-primary text-primary'
                                     : 'border-transparent text-muted hover:text-primary' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>

                {{-- Mobile Toggle --}}
                <button id="mobile-menu-btn" class="md:hidden p-2 text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

            </div>

            {{-- Mobile Dropdown --}}
            <div id="mobile-menu" class="hidden md:hidden border-t border-hairline">
                <div class="flex flex-col">
                    <a href="{{ route('home') }}"
                       class="px-0 py-3 font-sans font-bold text-xs tracking-widest uppercase border-b border-hairline
                              {{ !request()->filled('category') ? 'text-primary' : 'text-muted' }}">
                        Semua
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('home', ['category' => $category->slug]) }}"
                           class="px-0 py-3 font-sans font-bold text-xs tracking-widest uppercase border-b border-hairline
                                  {{ request('category') === $category->slug ? 'text-primary' : 'text-muted' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>

    {{-- ============================================================ --}}
    {{-- 2 & 3. LIVEWIRE NEWS GRID (Header Search + Main Grid)        --}}
    {{-- ============================================================ --}}
    <livewire:public.news-grid />    {{-- ============================================================ --}}
    {{-- 4. FOOTER                                                     --}}
    {{-- ============================================================ --}}
    <footer class="bg-canvas border-t-2 border-primary mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                {{-- Wordmark footer --}}
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 bg-primary flex items-center justify-center">
                        <span class="text-canvas font-sans font-bold text-xs">M</span>
                    </div>
                    <span class="font-serif text-base font-bold text-primary">MuaraJogja</span>
                </div>
                {{-- Info --}}
                <div class="text-right">
                    <p class="font-sans text-xs text-muted leading-relaxed">
                        Sistem agregasi informasi otomatis. Seluruh hak cipta artikel &amp; gambar milik portal sumber masing-masing.
                    </p>
                    <p class="font-sans text-xs text-muted mt-1">Dikembangkan untuk Tujuan Akademik.</p>
                </div>
            </div>
        </div>
    </footer>

    {{-- ============================================================ --}}
    {{-- JAVASCRIPT MINIMAL                                            --}}
    {{-- ============================================================ --}}
    <script>
        // Toggle mobile menu
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

</body>
</html>
