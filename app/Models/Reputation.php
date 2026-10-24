<?php

namespace App\Models;

use Database\Factories\ReputationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reputation extends Model
{
    /** @use HasFactory<ReputationFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'total_score', 'quests_completed', 'mysteries_solved', 'streak'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
