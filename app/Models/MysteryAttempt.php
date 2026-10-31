<?php

namespace App\Models;

use Database\Factories\MysteryAttemptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu sesi bermain Mystery Budaya untuk seorang user.
 *
 * status: 0 = berjalan, 1 = berhasil, 2 = gagal (salah 7x).
 */
class MysteryAttempt extends Model
{
    /** @use HasFactory<MysteryAttemptFactory> */
    use HasFactory;

    public const STATUS_PLAYING = 0;

    public const STATUS_SOLVED = 1;

    public const STATUS_FAILED = 2;

    protected $fillable = [
        'user_id',
        'mystery_id',
        'guess',
        'is_correct',
        'clues_used',
        'status',
        'clues_revealed',
        'wrong_guesses',
        'score',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'status' => 'integer',
            'clues_used' => 'integer',
            'clues_revealed' => 'integer',
            'wrong_guesses' => 'integer',
            'score' => 'integer',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mystery(): BelongsTo
    {
        return $this->belongsTo(Mystery::class);
    }

    public function isPlaying(): bool
    {
        return $this->status === self::STATUS_PLAYING;
    }

    public function isSolved(): bool
    {
        return $this->status === self::STATUS_SOLVED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
