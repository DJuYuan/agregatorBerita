<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use Livewire\Component;

class SettingsManager extends Component
{
    public $settings = [];

    protected $rules = [
        'settings.*.value' => 'required|string',
    ];

    public function mount()
    {
        // Ambil semua setting dari database
        $this->settings = SystemSetting::all()->toArray();
    }

    public function save()
    {
        foreach ($this->settings as $settingData) {
            $setting = SystemSetting::find($settingData['id']);
            if ($setting) {
                // Lakukan validasi sederhana per tipe data jika diperlukan
                if ($setting->type === 'number' && !is_numeric($settingData['value'])) {
                    session()->flash('error', "Nilai untuk \"{$setting->label}\" harus berupa angka.");
                    return;
                }
                
                $setting->update([
                    'value' => $settingData['value'],
                ]);
            }
        }

        session()->flash('success', 'Seluruh konfigurasi sistem berhasil diperbarui.');
    }

    public function render()
    {
        // Kelompokkan data setting berdasarkan kolom 'group' untuk UI yang rapi
        $groupedSettings = collect($this->settings)->groupBy('group');

        return view('livewire.admin.settings-manager', [
            'groupedSettings' => $groupedSettings,
        ])->layout('components.admin-layout');
    }
}
