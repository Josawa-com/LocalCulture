<?php

namespace App\Models;

use Database\Factories\ProvinceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    /** @use HasFactory<ProvinceFactory> */
    use HasFactory;

    protected $fillable = ['island_id', 'name', 'slug', 'description', 'image_path'];

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class);
    }
}
