<?php

namespace App\Models;

use Database\Factories\QuestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quest extends Model
{
    /** @use HasFactory<QuestFactory> */
    use HasFactory;

    protected $fillable = ['type', 'title', 'description', 'available_on', 'ends_at', 'time_limit', 'max_attempts', 'is_active'];

    protected function casts(): array
    {
        return [
            'available_on' => 'date',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuestAttempt::class);
    }

    /** Scope quests that are available today. */
    public function scopeAvailable($query, $date = null)
    {
        return $query->where('is_active', true)->where('available_on', '<=', $date ?? today())
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()));
    }
}
