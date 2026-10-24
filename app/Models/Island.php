<?php

namespace App\Models;

use Database\Factories\IslandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Island extends Model
{
    /** @use HasFactory<IslandFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'image_path'];

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }

    public function regencies(): HasManyThrough
    {
        return $this->hasManyThrough(Regency::class, Province::class);
    }
}
