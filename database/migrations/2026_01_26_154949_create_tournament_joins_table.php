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
        Schema::create('tournament_joins', function (Blueprint $table) {
            $table->id();
            $table->string('join_code', 50)->unique();

            // Tournament & Organizer
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();

            // Player (nullable because guest join allowed)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Team / Player Info
            $table->string('team_name')->nullable();          // For squad / duo
            $table->string('captain_ign');
            $table->string('captain_game_id');

            // Contact
            $table->string('email');
            $table->string('phone');

            // Mode
            $table->enum('mode', ['solo', 'duo', 'squad']);

            // Payment
            $table->boolean('is_paid')->default(false);
            $table->decimal('entry_fee', 10, 2)->default(0);

            // Payment proof (media system)
            $table->string('payment_status')->default('not_required');
            // not_required | pending | verified | rejected

            // Join Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                ->default('pending');

            // Notes
            $table->text('notes')->nullable();
            $table->text('reject_reason')->nullable();

            // Room visibility flags
            $table->boolean('room_visible')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_joins');
    }
};
