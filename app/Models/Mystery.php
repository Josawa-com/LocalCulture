<?php

namespace App\Models;

use Database\Factories\MysteryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mystery extends Model
{
    /** @use HasFactory<MysteryFactory> */
    use HasFactory;

    protected $fillable = [
        'answer',
        'origin',
        'statement',
        'clues',
        'max_clues',
        'points',
        'clue_penalty',
        'max_wrong_guesses',
        'time_limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'clues' => 'array',
            'is_active' => 'boolean',
            'max_clues' => 'integer',
            'points' => 'integer',
            'clue_penalty' => 'integer',
            'max_wrong_guesses' => 'integer',
            'time_limit' => 'integer',
        ];
    }

    /** Daftar clue yang benar-benar terisi (maksimal max_clues). */
    public function availableClues(): array
    {
        $clues = collect($this->clues ?? [])
            ->map(fn ($c) => trim((string) $c))
            ->filter()
            ->values();

        $limit = $this->max_clues > 0 ? $this->max_clues : 3;

        return $clues->take($limit)->all();
    }

    /**
     * Poin bila berhasil menebak dengan sejumlah clue terbuka.
     * Contoh: points 300, penalty 50 -> 0 clue = 300, 1 clue = 250,
     * 2 clue = 200, 3 clue = 150. Minimal 50.
     */
    public function scoreFor(int $cluesRevealed): int
    {
        $score = $this->points - ($cluesRevealed * $this->clue_penalty);

        return max(50, $score);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(MysteryAttempt::class);
    }
}
