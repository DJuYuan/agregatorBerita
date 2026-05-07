<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'article_id',
        'image_url',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
