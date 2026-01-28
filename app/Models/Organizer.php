<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organizer extends Model
{
    protected $fillable = [
        'user_id',
        'organization_name',
        'contact_number',
        'discord_link',
        'rating',
        'verified',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'rating'   => 'float',
    ];

    // Owner user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
