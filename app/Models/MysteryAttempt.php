<?php

namespace App\Models;

use Database\Factories\MysteryAttemptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MysteryAttempt extends Model
{
    /** @use HasFactory<MysteryAttemptFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'mystery_id', 'guess', 'is_correct', 'clues_used', 'score'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mystery(): BelongsTo
    {
        return $this->belongsTo(Mystery::class);
    }
}
