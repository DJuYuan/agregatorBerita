<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uji Coba Mesin Ekstraksi Berita</title>
    <!-- Menggunakan Tailwind CDN untuk tampilan cepat (throw-away UI) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="antialiased text-slate-800">

<div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Mesin Agregator Berita</h1>
            <p class="mt-2 text-sm text-slate-600">Throw-away UI untuk menguji fungsi penarikan data dari RSS feed lokal.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <!-- Form untuk mengeksekusi Command Artisan -->
            <form action="{{ route('test-engine.fetch') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 cursor-pointer">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Tarik Berita Sekarang
                </button>
            </form>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session('success'))
        <div class="rounded-lg bg-green-50 p-4 mb-8 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabel Data Berita -->
    <div class="bg-white shadow-sm overflow-hidden sm:rounded-xl border border-slate-200">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Gambar</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul & Potongan Deskripsi</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Sumber</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse ($articles as $article)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($article->images->isNotEmpty())
                                <img src="{{ $article->images->first()->image_url }}" alt="Thumbnail" class="h-20 w-32 object-cover rounded-lg shadow-sm border border-slate-100">
                            @else
                                <div class="h-20 w-32 bg-slate-100 flex items-center justify-center rounded-lg border border-slate-200">
                                    <span class="text-xs text-slate-400">No Image</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ $article->link }}" target="_blank" class="text-lg font-semibold text-blue-600 hover:text-blue-800 transition-colors duration-150">
                                {{ $article->title }}
                            </a>
                            <p class="mt-2 text-sm text-slate-600 line-clamp-2">
                                {{ Str::limit($article->description, 150) }}
                            </p>
                            <div class="mt-2 flex items-center text-xs text-slate-400">
                                <span>Dipublikasikan: {{ $article->published_at ? $article->published_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $article->source->name ?? 'Unknown' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-slate-500">
                            Belum ada berita yang ditarik. Silakan klik tombol "Tarik Berita Sekarang" di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
