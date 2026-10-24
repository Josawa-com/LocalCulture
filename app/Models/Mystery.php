<?php

namespace App\Models;

use Database\Factories\MysteryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mystery extends Model
{
    /** @use HasFactory<MysteryFactory> */
    use HasFactory;

    protected $fillable = ['answer', 'origin', 'clues', 'max_clues', 'time_limit', 'is_active'];

    protected function casts(): array
    {
        return [
            'clues' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
