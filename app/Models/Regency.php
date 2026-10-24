<?php

namespace App\Models;

use Database\Factories\RegencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regency extends Model
{
    /** @use HasFactory<RegencyFactory> */
    use HasFactory;

    protected $fillable = ['province_id', 'name', 'slug', 'description'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function cultures(): HasMany
    {
        return $this->hasMany(Culture::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }
}
