<?php

namespace App\Models;

use Database\Factories\AttemptAnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    /** @use HasFactory<AttemptAnswerFactory> */
    use HasFactory;

    protected $fillable = ['quest_attempt_id', 'question_id', 'selected_index', 'is_correct', 'time_ms', 'score', 'shown_at', 'answered_at'];

    protected function casts(): array
    {
        return [
            'selected_index' => 'integer',
            'is_correct' => 'boolean',
            'shown_at' => 'datetime',
            'answered_at' => 'datetime',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
