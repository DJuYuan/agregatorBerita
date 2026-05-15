<x-admin-layout>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.sources.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Sumber RSS Baru</h1>
            <p class="mt-0.5 text-sm text-gray-500">Daftarkan portal berita baru beserta URL RSS-nya ke dalam sistem.</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

            <form method="POST" action="{{ route('admin.sources.store') }}" novalidate>
                @csrf

                <!-- Nama Sumber -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Portal Berita <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Tribun Jogja"
                        class="block w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:border-transparent transition"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Kategori Dropdown -->
                <div class="mb-5">
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select
                            id="category_id"
                            name="category_id"
                            class="block w-full px-4 py-2.5 border {{ $errors->has('category_id') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:border-transparent transition appearance-none bg-white"
                        >
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    @error('category_id')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- URL RSS -->
                <div class="mb-6">
                    <label for="rss_url" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        URL RSS Feed <span class="text-red-500">*</span>
                    </label>
                    <div class="flex rounded-lg shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7M6 17a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </span>
                        <input
                            id="rss_url"
                            name="rss_url"
                            type="url"
                            value="{{ old('rss_url') }}"
                            placeholder="https://contoh.com/rss"
                            class="flex-1 block w-full px-4 py-2.5 border-y border-r {{ $errors->has('rss_url') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-r-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition"
                        >
                    </div>
                    @error('rss_url')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-400">Wajib diisi. Format: https://portal.com/rss atau /feed. URL ini harus unik.</p>
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <p class="text-xs text-amber-700">
                            <strong>Catatan:</strong> Setelah sumber RSS berhasil ditambahkan, sistem harus menjalankan perintah <code class="bg-amber-100 px-1 rounded">php artisan fetch:news</code> secara manual atau menunggu jadwal otomatis untuk mengambil berita pertama kali.
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Sumber RSS
                    </button>
                    <a href="{{ route('admin.sources.index') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>
</x-admin-layout>
