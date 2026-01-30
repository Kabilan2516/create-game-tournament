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
        Schema::create('tournament_series_tournaments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tournament_series_id');
            $table->unsignedBigInteger('tournament_id');

            $table->timestamps();

            $table->unique(
                ['tournament_series_id', 'tournament_id'],
                'series_tournament_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_series_tournaments');
    }
};
