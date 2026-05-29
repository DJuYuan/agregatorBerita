<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'keywords',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('categories_nav');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('categories_nav');
        });
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }
}
