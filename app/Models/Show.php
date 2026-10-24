<?php

namespace App\Models;

use Database\Factories\ShowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Show extends Model
{
    /** @use HasFactory<ShowFactory> */
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'starts_at', 'location_name', 'regency_id', 'ticket_price', 'organizer', 'image_path'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ticket_price' => 'decimal:2',
        ];
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }
}
