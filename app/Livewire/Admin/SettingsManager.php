<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use Livewire\Component;

class SettingsManager extends Component
{
    // Array murni [id => value] — paling stabil untuk binding di Livewire
    public array $settingValues = [];

    protected array $rules = [
        'settingValues.*' => 'nullable|string',
    ];

    public function mount(): void
    {
        // Self-Healing: pastikan semua key pengaturan ada di database
        SystemSetting::initializeDefaults();
        
        // Form dibiarkan kosong agar placeholder tampil
        $this->settingValues = [];
    }

    public function save(): void
    {
        \Illuminate\Support\Facades\Log::info('SettingsManager::save dipanggil', [
            'incoming_values' => $this->settingValues
        ]);

        // Hanya validasi struktur data array
        $this->validate();

        $updatedCount = 0;

        foreach ($this->settingValues as $id => $val) {
            $val = trim($val);
            if ($val === '') continue; // Abaikan jika input kosong

            $setting = SystemSetting::find($id);
            if (!$setting) continue;

            // Validasi tipe number
            if ($setting->type === 'number' && !is_numeric($val)) {
                $this->dispatch('notify', message: "Nilai untuk \"{$setting->label}\" harus berupa angka.", type: 'error');
                return;
            }

            $setting->update(['value' => $val]);
            $updatedCount++;
        }

        if ($updatedCount > 0) {
            $this->dispatch('notify', message: "$updatedCount konfigurasi berhasil diperbarui.", type: 'success');
        } else {
            $this->dispatch('notify', message: "Tidak ada konfigurasi yang diubah.", type: 'info');
        }

        // Reset form setelah simpan
        $this->settingValues = [];
    }

    public function render()
    {
        // Ambil ulang dari DB agar data selalu segar dan kelompokkan untuk UI
        $groupedSettings = SystemSetting::all()->groupBy('group');

        return view('livewire.admin.settings-manager', [
            'groupedSettings' => $groupedSettings,
        ])->layout('components.admin-layout');
    }
}
