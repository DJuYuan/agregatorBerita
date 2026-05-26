<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceDailyStat extends Model
{
    protected $fillable = ['source_id', 'date', 'total_articles', 'total_errors'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_articles' => 'integer',
            'total_errors' => 'integer',
        ];
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
