<div>
    {{-- ============================================================ --}}
    {{-- HEADER: Search Bar & Title                                   --}}
    {{-- ============================================================ --}}
    <header class="bg-canvas border-b border-hairline relative">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 {{ empty($search) ? 'py-14 sm:py-20' : 'py-6' }} text-center">
            @if(empty($search))
                <p class="font-sans font-bold text-xs tracking-widest uppercase text-muted mb-6">
                    ● SEPUTAR DAERAH ISTIMEWA YOGYAKARTA
                </p>

                <h1 class="font-serif text-4xl sm:text-6xl font-bold text-primary leading-tight mb-4">
                    Semua Kabar Jogja,<br>
                    <em class="font-serif font-normal not-italic">Satu Layar.</em>
                </h1>

                <p class="font-sans text-sm text-muted leading-relaxed mb-10 max-w-xl mx-auto">
                    Akses cepat untuk semua berita, peristiwa, dan pengumuman terbaru dari berbagai sumber terpercaya.
                </p>
            @endif

            {{-- LIVE SEARCH BAR (DEBOUNCE 500ms) --}}
            <div class="relative max-w-2xl mx-auto">
                <div class="flex items-center bg-canvas border border-primary overflow-hidden">
                    <svg class="flex-shrink-0 ml-4 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        wire:model.live.debounce.500ms="search"
                        type="text"
                        placeholder="Cari rekayasa lalu lintas, cuaca ekstrem, acara lokal..."
                        class="flex-1 px-4 py-3.5 font-sans text-sm text-primary bg-transparent border-none outline-none placeholder-muted"
                    >
                </div>
            </div>

            @if(!empty($search))
                <p class="mt-4 font-sans text-xs text-muted">
                    Hasil untuk: <span class="font-bold text-primary">"{{ $search }}"</span>
                    — <button wire:click="resetSearch" class="underline cursor-pointer">Hapus filter</button>
                </p>
            @endif
        </div>
    </header>

    {{-- ============================================================ --}}
    {{-- MAIN: News Grid & Load More                                  --}}
    {{-- ============================================================ --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="section-rule pt-4 mb-8 flex items-end justify-between">
            <div>
                <h2 class="font-sans font-bold text-xs tracking-widest uppercase text-muted mb-1">
                    @if(!empty($categorySlug))
                        Kategori
                    @elseif(!empty($search))
                        Hasil Pencarian
                    @else
                        Edisi Terbaru
                    @endif
                </h2>
                <p class="font-serif text-2xl font-bold text-primary">
                    @if(!empty($categorySlug))
                        {{ $categories->where('slug', $categorySlug)->first()?->name ?? 'Kategori' }}
                    @elseif(!empty($search))
                        "{{ $search }}"
                    @else
                        Berita Terkini
                    @endif
                </p>
            </div>
            <span class="font-sans text-xs text-muted">{{ $articles->total() }} artikel</span>
        </div>

        @if($articles->isEmpty())
            <div class="border border-hairline py-24 text-center">
                <p class="font-serif text-xl text-muted mb-2">Tidak ada berita ditemukan</p>
                <p class="font-sans text-xs text-muted mb-6 tracking-wide">Coba ubah kata kunci atau pilih kategori lain.</p>
                <button wire:click="resetSearch" class="inline-block px-6 py-3 bg-primary text-canvas font-sans font-bold text-xs tracking-widest uppercase hover:opacity-80 transition-opacity">
                    Lihat Semua Berita
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-0 border-t border-l border-hairline relative">
                @foreach ($articles as $article)
                    @php
                        $category = $article->source?->category;
                        $image    = $article->images->first();
                        $source   = $article->source;
                    @endphp

                    <article class="news-card relative bg-canvas border-b border-r border-hairline flex flex-col group">
                        {{-- LAZY LOAD & FALLBACK IMAGE --}}
                        <div class="aspect-video overflow-hidden bg-gray-100">
                            @if($image)
                                <img
                                    src="{{ $image->image_url }}"
                                    alt="{{ $article->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'thumb-placeholder w-full h-full\'><svg class=\'w-8 h-8\' style=\'color:#d0d0d0\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z\'/></svg></div>'"
                                >
                            @else
                                <div class="thumb-placeholder w-full h-full">
                                    <svg class="w-8 h-8" style="color:#d0d0d0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col flex-1 p-5">
                            @if($category)
                                <div class="mb-3 z-20">
                                    <button wire:click="setCategory('{{ $category->slug }}')" class="inline-block bg-primary text-canvas font-sans font-bold text-xs tracking-widest uppercase px-2 py-1 hover:opacity-80 transition-opacity">
                                        {{ $category->name }}
                                    </button>
                                </div>
                            @endif

                            <h3 class="font-serif text-base font-bold text-primary leading-snug mb-2 line-clamp-2">
                                {{-- RUTE TRACKING KUNJUNGAN (Klik → redirect ke sumber asli) --}}
                                {{-- Kelas news-title-link digunakan untuk CSS :visited agar  --}}
                                {{-- browser menandai berita yang sudah pernah dibuka         --}}
                                <a
                                    href="{{ route('article.go', $article->slug) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="news-title-link hover:underline after:absolute after:inset-0"
                                >
                                    {{ $article->title }}
                                </a>
                            </h3>

                            @if($article->description)
                                <p class="font-sans text-sm text-muted leading-relaxed line-clamp-3 flex-1">
                                    {{ $article->description }}
                                </p>
                            @endif

                            @if($article->tags && $article->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 mt-4">
                                    @foreach($article->tags->take(3) as $tag)
                                        <span class="text-[10px] font-sans font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-sm uppercase tracking-wider">#{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-hairline">
                                <span class="font-sans font-bold text-xs tracking-wide text-muted uppercase truncate">
                                    {{ $source?->name ?? '—' }}
                                </span>
                                <span class="font-sans text-xs text-muted flex-shrink-0 ml-2">
                                    {{ $article->published_at?->diffForHumans() ?? $article->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- HYBRID PAGINATION: LOAD MORE BUTTON --}}
            @if($articles->hasMorePages())
                <div class="mt-12 text-center">
                    <button wire:click="loadMore" class="inline-flex items-center justify-center px-8 py-4 bg-primary text-canvas font-sans font-bold text-xs tracking-widest uppercase hover:opacity-80 transition-opacity group">
                        <span wire:loading.remove wire:target="loadMore">Muat Lebih Banyak Berita</span>
                        <span wire:loading wire:target="loadMore" class="inline-block">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sedang Memuat...
                            </span>
                        </span>
                    </button>
                </div>
            @endif
        @endif
    </main>
</div>
