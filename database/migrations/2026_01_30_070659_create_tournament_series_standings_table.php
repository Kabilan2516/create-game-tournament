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
        Schema::create('tournament_series_standings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tournament_series_id');

            // Player / Team identity
            $table->string('team_name')->nullable();
            $table->string('ign');

            // Stats
            $table->integer('matches_played')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('total_points')->default(0);

            $table->timestamps();

            $table->index('tournament_series_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_series_standings');
    }
};
