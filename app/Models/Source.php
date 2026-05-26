<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id',
        'name',
        'rss_url',
        'is_active',
        'last_fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
            'last_fetched_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function logsSuccess(): HasMany
    {
        return $this->hasMany(LogSuccess::class);
    }

    public function logsFailed(): HasMany
    {
        return $this->hasMany(LogFailed::class);
    }

    public function dailyStats(): HasMany
    {
        return $this->hasMany(SourceDailyStat::class);
    }
}
