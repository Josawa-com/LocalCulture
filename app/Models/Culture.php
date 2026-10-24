<?php

namespace App\Models;

use Database\Factories\CultureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Culture extends Model
{
    /** @use HasFactory<CultureFactory> */
    use HasFactory;

    protected $fillable = [
        'regency_id', 'culture_category_id', 'name', 'slug', 'description', 'content', 'image_path', 'philosophy', 'sort_order',
    ];

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CultureCategory::class, 'culture_category_id');
    }
}
