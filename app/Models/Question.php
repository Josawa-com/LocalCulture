<?php

namespace App\Models;

use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    protected $fillable = ['quest_id', 'question', 'options', 'correct_index', 'points', 'sort_order'];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_index' => 'integer',
            'points' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }
}
