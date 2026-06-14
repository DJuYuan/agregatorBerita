<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterSource = '';
    public $filterCategory = '';

    // Reset paginasi jika ada perubahan pada pencarian/filter
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSource()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }


    public function render()
    {
        $query = Article::with(['source.category', 'tags']);

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterSource)) {
            $query->where('source_id', $this->filterSource);
        }

        if (!empty($this->filterCategory)) {
            // Kita cari artikel yang terhubung dengan source yang memiliki category_id ini
            $query->whereHas('source', function ($q) {
                $q->where('category_id', $this->filterCategory);
            });
        }

        $articles = $query->orderBy('published_at', 'desc')->paginate(15);
        $sources = Source::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.article-manager', [
            'articles' => $articles,
            'sources' => $sources,
            'categories' => $categories,
        ])->layout('components.admin-layout');
    }
}
