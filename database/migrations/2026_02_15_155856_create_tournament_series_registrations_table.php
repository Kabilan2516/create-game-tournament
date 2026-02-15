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
        Schema::create('tournament_series_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_series_id')
                ->constrained('tournament_series')
                ->cascadeOnDelete();
            $table->foreignId('organizer_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('join_code', 50)->unique();
            $table->string('team_name')->nullable();
            $table->string('captain_ign');
            $table->string('captain_game_id');
            $table->string('email');
            $table->string('phone');
            $table->enum('mode', ['solo', 'duo', 'squad']);
            $table->unsignedInteger('substitute_count')->default(0);
            $table->json('roster')->nullable();

            $table->boolean('is_paid')->default(false);
            $table->decimal('entry_fee', 10, 2)->default(0);
            $table->string('payment_status')->default('not_required');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->index(['tournament_series_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_series_registrations');
    }
};
