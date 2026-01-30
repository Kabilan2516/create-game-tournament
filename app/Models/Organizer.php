<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Organizer extends Model
{
    /* =========================
       MASS ASSIGNMENT
    ========================= */
    protected $fillable = [

        // Core
        'user_id',

        // Display & Identity
        'display_name',
        'organization_name',
        'bio',
        'organizer_type',
        'region',
        'timezone',

        // Contact
        'email',
        'contact_number',
        'discord_link',

        // Social
        'social_links',

        // Notifications
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'weekly_summary',

        // Privacy
        'show_earnings',
        'allow_player_contact',

        // Trust
        'rating',
        'verification_status',
        'verified_at',
    ];

    /* =========================
       CASTS
    ========================= */
    protected $casts = [
        'social_links' => 'array',

        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'weekly_summary' => 'boolean',

        'show_earnings' => 'boolean',
        'allow_player_contact' => 'boolean',

        'rating' => 'float',
        'verified_at' => 'datetime',
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    // Organizer belongs to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // All media (banner, avatar, etc.)
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    // Helpers (VERY IMPORTANT)
    public function bannerMedia()
    {
        return $this->media()->where('collection', 'banner')->latest()->first();
    }

    public function avatarMedia()
    {
        return $this->media()->where('collection', 'avatar')->latest()->first();
    }
    // Profile avatar
    public function avatar()
    {
        return $this->media()
            ->where('collection', 'avatar')
            ->latest()
            ->first();
    }

    // Profile banner
    public function banner()
    {
        return $this->media()
            ->where('collection', 'banner')
            ->latest()
            ->first();
    }

    /* =========================
       ACCESSORS / HELPERS
    ========================= */

    public function getDisplayNameAttribute(): string
    {
        return $this->attributes['display_name']
            ?? $this->attributes['organization_name']
            ?? $this->user->name;
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar()
            ? asset('storage/' . $this->avatar()->file_path)
            : null;
    }

    public function bannerUrl(): ?string
    {
        return $this->banner()
            ? asset('storage/' . $this->banner()->file_path)
            : null;
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function social(string $key): ?string
    {
        return $this->social_links[$key] ?? null;
    }
    // Tournaments created by organizer
    public function tournaments()
    {
        return $this->hasMany(Tournament::class, 'organizer_id');
    }

    // Series created by organizer
    public function series()
    {
        return $this->hasMany(TournamentSeries::class, 'organizer_id');
    }
}
