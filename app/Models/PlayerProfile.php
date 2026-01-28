<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'ign',
        'rank_level',
        'stats',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    // Owner user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
