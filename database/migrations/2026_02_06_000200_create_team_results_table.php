<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('match_result_id')
                ->constrained('match_results')
                ->cascadeOnDelete();

            $table->foreignId('tournament_join_id')
                ->constrained('tournament_joins')
                ->cascadeOnDelete();

            $table->string('team_name')->nullable();
            $table->integer('rank')->nullable();

            $table->integer('mp')->default(1);
            $table->integer('kp')->default(0);
            $table->integer('pp')->default(0);
            $table->integer('tt')->default(0);
            $table->integer('cd')->default(0);

            $table->timestamps();

            $table->index(['match_result_id', 'tournament_join_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_results');
    }
};
