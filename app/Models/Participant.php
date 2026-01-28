<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    protected $fillable = [
        'tournament_id',
        'user_id',
        'status',
    ];

    // Tournament relation
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    // Player (User)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
