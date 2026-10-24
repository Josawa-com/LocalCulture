<?php

namespace App\Models;

use Database\Factories\VenueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    /** @use HasFactory<VenueFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'address', 'regency_id', 'phone', 'description', 'image_path'];

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function interestRequests(): HasMany
    {
        return $this->hasMany(InterestRequest::class);
    }
}
