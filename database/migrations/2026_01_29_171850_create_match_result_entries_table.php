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
        Schema::create('match_result_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('match_result_id')
                ->constrained('match_results')
                ->cascadeOnDelete();

            $table->foreignId('tournament_join_id')
                ->constrained('tournament_joins')
                ->cascadeOnDelete();

            // Player snapshot (important!)
            $table->string('player_ign');
            $table->string('player_game_id')->nullable();

            // Team context
            $table->string('team_name')->nullable();

            // Results
            $table->unsignedInteger('rank')->nullable();
            $table->unsignedInteger('kills')->default(0);
            $table->unsignedInteger('points')->default(0);

            // Winners
            $table->enum('winner_position', ['1', '2', '3'])->nullable();

            $table->timestamps();

            // Prevent duplicate player entry per match
            $table->unique(
                ['match_result_id', 'tournament_join_id', 'player_ign'],
                'uniq_match_result_player'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_result_entries');
    }
};
