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

    protected $fillable = ['type', 'title', 'subtitle', 'question_count', 'description', 'available_on', 'ends_at', 'time_limit', 'max_attempts', 'is_active'];

    /** Empat jenis kuis yang ditampilkan di halaman /kuis, berurutan. */
    public const TYPES = [
        'daily' => 'Kuis Harian',
        'weekly' => 'Kuis Mingguan',
        'monthly' => 'Kuis Bulanan',
        'knowledge' => 'Kuis Pengetahuan',
    ];

    /** Deskripsi singkat tiap jenis (dipakai di halaman kuis). */
    public const TYPE_DESCRIPTIONS = [
        'daily' => 'Kuis singkat setiap hari untuk mengasah ingatan tentang budaya Nusantara.',
        'weekly' => 'Uji pemahamanmu dengan kuis yang berganti setiap minggu.',
        'monthly' => 'Kuis bulanan dengan materi lebih luas dan menantang.',
        'knowledge' => 'Menguji wawasanmu tentang budaya Indonesia secara menyeluruh.',
    ];

    protected function casts(): array
    {
        return [
            'available_on' => 'date',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'question_count' => 'integer',
            'time_limit' => 'integer',
            'max_attempts' => 'integer',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    /** Label jenis kuis yang ramah dibaca. */
    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /** Apakah soal sudah lengkap sesuai target jumlah soal? */
    public function isQuestionSetComplete(): bool
    {
        return $this->question_count > 0
            && $this->questions()->count() >= $this->question_count;
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
