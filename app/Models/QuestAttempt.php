<?php

namespace App\Models;

use Database\Factories\QuestAttemptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestAttempt extends Model
{
    /** @use HasFactory<QuestAttemptFactory> */
    use HasFactory;

    protected $fillable = ['quest_id', 'user_id', 'started_at', 'finished_at', 'total_score', 'correct_count', 'total_time_ms', 'status'];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'status' => 'integer',
            'total_score' => 'integer',
            'correct_count' => 'integer',
            'total_time_ms' => 'integer',
        ];
    }

    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }
}
