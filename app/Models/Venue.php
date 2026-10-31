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

    protected $fillable = [
        'name', 'slug', 'type', 'category', 'address', 'latitude', 'longitude',
        'regency_id', 'phone', 'description', 'skills', 'image_path',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function interestRequests(): HasMany
    {
        return $this->hasMany(InterestRequest::class);
    }

    /**
     * Apakah koordinat tempat ini sudah terverifikasi.
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Tautan Google Maps: langsung mencari nama sanggar ini.
     */
    public function mapsUrl(): string
    {
        $query = $this->name;

        if ($this->regency) {
            // "Kota Jogja" -> "Kota Yogyakarta" agar hasil pencarian lebih tepat.
            $regencyName = $this->regency->name === 'Kota Jogja'
                ? 'Kota Yogyakarta'
                : $this->regency->name;

            $query .= ', '.$regencyName;
        }

        $query .= ', Daerah Istimewa Yogyakarta';

        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($query);
    }
}
