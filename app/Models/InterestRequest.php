<?php

namespace App\Models;

use Database\Factories\InterestRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterestRequest extends Model
{
    /** @use HasFactory<InterestRequestFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'venue_id', 'status', 'admin_note', 'responded_at'];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
