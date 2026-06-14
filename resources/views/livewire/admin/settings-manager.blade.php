<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Sistem</h1>
            <p class="mt-1 text-sm text-gray-500">Konfigurasi dinamis seluruh modul agregator berita tanpa menyentuh file kode.</p>
        </div>
    </div>

    {{-- Tampilkan error validasi --}}
    @if ($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Livewire 3: gunakan wire:submit. Tambahkan x-on:submit.prevent sebagai pengaman ganda jika Livewire gagal load --}}
    <form wire:submit="save" x-on:submit.prevent="console.log('Form disubmit via Alpine!');">
        <div class="space-y-8">
            @foreach($groupedSettings as $groupName => $items)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    {{-- Header Group --}}
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">{{ $groupName }}</h2>
                    </div>

                    {{-- Form Fields --}}
                    <div class="p-6 divide-y divide-gray-100 space-y-6">
                        @foreach($items as $item)
                            <div class="pt-6 first:pt-0 grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800">{{ $item->label }}</label>
                                    <p class="mt-1 text-xs text-gray-400">{{ $item->description ?? '' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    @if($item->type === 'number')
                                        <input
                                            wire:model="settingValues.{{ $item->id }}"
                                            type="number"
                                            placeholder="Kosongkan jika tidak ingin diubah (Saat ini: {{ $item->value }})"
                                            class="w-full max-w-md rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        >
                                    @elseif($item->type === 'boolean')
                                        <select wire:model="settingValues.{{ $item->id }}" class="w-full max-w-md rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="">-- Biarkan (Saat ini: {{ $item->value === 'true' ? 'Aktif' : 'Nonaktif' }}) --</option>
                                            <option value="true">Aktif</option>
                                            <option value="false">Nonaktif</option>
                                        </select>
                                    @else
                                        <input
                                            wire:model="settingValues.{{ $item->id }}"
                                            type="text"
                                            placeholder="Kosongkan jika tidak ingin diubah"
                                            class="w-full max-w-md rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        >
                                    @endif

                                    {{-- Informasi Data Saat Ini --}}
                                    <div class="mt-3 flex items-center p-2.5 bg-blue-50/50 rounded-md border border-blue-100 max-w-md">
                                        <svg class="w-4 h-4 text-blue-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-sm text-blue-800">
                                            @if($item->key === 'crawler_user_agent')
                                                Nama bot saat ini: <strong>{{ $item->value }}</strong>
                                            @elseif($item->key === 'active_retention_days')
                                                Masa aktif saat ini: <strong>{{ $item->value }}</strong> hari
                                            @elseif($item->key === 'quarantine_retention_days')
                                                Masa karantina saat ini: <strong>{{ $item->value }}</strong> hari
                                            @else
                                                Saat ini: <strong>{{ $item->value }}</strong>
                                            @endif
                                        </span>
                                    </div>

                                    @error("settingValues.{$item->id}")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Submit --}}
            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-sm">
                    <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">Simpan Konfigurasi</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>
</div>


