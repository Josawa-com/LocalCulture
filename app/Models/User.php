<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'island_id',
        'province_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function reputation(): HasOne
    {
        return $this->hasOne(Reputation::class);
    }

    public function questAttempts(): HasMany
    {
        return $this->hasMany(QuestAttempt::class);
    }

    public function mysteryAttempts(): HasMany
    {
        return $this->hasMany(MysteryAttempt::class);
    }

    public function interestRequests(): HasMany
    {
        return $this->hasMany(InterestRequest::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
