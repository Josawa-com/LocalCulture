<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jumlah kunjungan seorang user ke sebuah kabupaten/kota.
 *
 * Satu baris per pasangan user + wilayah; setiap kunjungan menambah `visits`.
 */
class RegencyVisit extends Model
{
    protected $fillable = ['user_id', 'regency_id', 'visits', 'last_visited_at'];

    protected function casts(): array
    {
        return [
            'visits' => 'integer',
            'last_visited_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    /** Catat satu kunjungan user ke wilayah. */
    public static function record(User $user, Regency $regency): self
    {
        $visit = self::firstOrCreate(
            ['user_id' => $user->id, 'regency_id' => $regency->id],
            ['visits' => 0]
        );

        $visit->increment('visits', 1, ['last_visited_at' => now()]);

        return $visit;
    }
}
