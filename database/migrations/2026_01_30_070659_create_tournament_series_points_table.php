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
        Schema::create('tournament_series_points', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tournament_series_id');

            $table->integer('position'); // 1,2,3...
            $table->integer('points');   // 10,7,5...

            $table->timestamps();

            $table->unique(
                ['tournament_series_id', 'position'],
                'series_position_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_series_points');
    }
};
