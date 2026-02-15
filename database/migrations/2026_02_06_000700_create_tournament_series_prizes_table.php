<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_series_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_series_id')
                ->constrained('tournament_series')
                ->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['tournament_series_id', 'position'], 'series_prize_unique');
            $table->index('tournament_series_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_series_prizes');
    }
};
