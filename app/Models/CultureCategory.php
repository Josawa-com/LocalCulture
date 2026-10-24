<?php

namespace App\Models;

use Database\Factories\CultureCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CultureCategory extends Model
{
    /** @use HasFactory<CultureCategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon'];

    public function cultures(): HasMany
    {
        return $this->hasMany(Culture::class);
    }
}
