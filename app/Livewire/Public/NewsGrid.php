<?php

namespace App\Livewire\Public;

use App\Models\Article;
use App\Models\Category;
use Livewire\Component;

class NewsGrid extends Component
{
    public $search = '';
    public $categorySlug = null;
    public $perPage = 15;

    // Listen to query string for search and category
    protected $queryString = [
        'search' => ['except' => ''],
        'categorySlug' => ['except' => '', 'as' => 'category'],
    ];

    public function loadMore()
    {
        $this->perPage += 15;
    }

    public function setCategory($slug)
    {
        $this->categorySlug = $slug;
        $this->perPage = 15; // Reset paginasi saat ganti kategori
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->categorySlug = null;
        $this->perPage = 15;
    }

    public function render()
    {
        $categories = Category::all();

        $query = Article::query()
            ->with(['source.category', 'images', 'tags']) // Eager load
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->categorySlug)) {
            $query->whereHas('source.category', function ($q) {
                $q->where('slug', $this->categorySlug);
            });
        }

        $articles = $query->paginate($this->perPage);

        return view('livewire.public.news-grid', [
            'categories' => $categories,
            'articles' => $articles,
        ]);
    }
}
