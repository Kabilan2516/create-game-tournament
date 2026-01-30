<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'organizer_id',
        'is_locked',
        'published_at',
        'notes',
    ];

    protected $casts = [
        'is_locked'    => 'boolean',
        'published_at'=> 'datetime',
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    // Tournament this result belongs to
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    // Organizer who uploaded the result
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    // All player result entries
    public function entries()
    {
        return $this->hasMany(MatchResultEntry::class);
    }
}
