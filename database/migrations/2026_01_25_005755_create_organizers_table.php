<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizers', function (Blueprint $table) {
            $table->id();

            /* =========================
               CORE LINK
            ========================= */
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /* =========================
               DISPLAY & BRANDING
            ========================= */
            $table->string('display_name')->nullable();
            $table->string('organization_name')->nullable();

            $table->string('avatar')->nullable();   // profile image
            $table->string('banner')->nullable();   // profile banner

            /* =========================
               CONTACT
            ========================= */
            $table->string('email')->nullable();          // public contact
            $table->string('contact_number', 20)->nullable();
            $table->string('discord_link')->nullable();

            /* =========================
               BIO & IDENTITY
            ========================= */
            $table->text('bio')->nullable();

            $table->enum('organizer_type', [
                'individual',
                'team',
                'community',
                'organization'
            ])->nullable();

            $table->string('region')->nullable();
            $table->string('timezone')->nullable();

            /* =========================
               SOCIAL LINKS (JSON)
            ========================= */
            $table->json('social_links')->nullable();
            /*
              {
                "youtube": "",
                "twitch": "",
                "instagram": "",
                "twitter": "",
                "discord": "",
                "website": ""
              }
            */

            /* =========================
               NOTIFICATION SETTINGS
            ========================= */
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('weekly_summary')->default(true);

            /* =========================
               PRIVACY SETTINGS
            ========================= */
            $table->boolean('show_earnings')->default(false);
            $table->boolean('allow_player_contact')->default(true);

            /* =========================
               TRUST & VERIFICATION
            ========================= */
            $table->decimal('rating', 3, 2)->default(0.00);

            $table->enum('verification_status', [
                'unverified',
                'pending',
                'verified'
            ])->default('unverified');

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizers');
    }
};
