<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class QuarantineManager extends Component
{
    use WithPagination;

    public string $search = '';

    // Reset paginasi setiap kali pencarian berubah
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Kosongkan seluruh artikel yang ada di karantina (Force Delete).
     * Ini adalah fungsi maintenance IT, bukan fungsi kurasi editorial.
     */
    public function emptyQuarantine(): void
    {
        $count = Article::onlyTrashed()->count();
        Article::onlyTrashed()->forceDelete();

        Log::info("  [QUARANTINE ADMIN] Karantina dikosongkan. {$count} artikel dihapus permanen.");
        session()->flash('success', "Seluruh artikel di karantina ({$count} artikel) berhasil dihapus permanen.");
    }

    public function render()
    {
        // Ambil hanya artikel yang sedang dalam karantina (deleted_at terisi)
        $query = Article::onlyTrashed()
            ->with(['source.category'])
            ->orderBy('deleted_at', 'desc'); // Tampilkan yang paling baru dikarantina di atas

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        $articles       = $query->paginate(15);
        $totalQuarantine = Article::onlyTrashed()->count();
        
        $activeRetention = \App\Models\SystemSetting::getValue('active_retention_days', 30);
        $quarantineRetention = \App\Models\SystemSetting::getValue('quarantine_retention_days', 90);

        return view('livewire.admin.quarantine-manager', [
            'articles'        => $articles,
            'totalQuarantine' => $totalQuarantine,
            'activeRetention' => $activeRetention,
            'quarantineRetention' => $quarantineRetention,
        ])->layout('components.admin-layout');
    }
}
