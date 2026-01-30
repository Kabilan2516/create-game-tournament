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
       Schema::create('match_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tournament_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organizer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // State
            $table->boolean('is_locked')->default(false);
            $table->timestamp('published_at')->nullable();

            // Optional notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Only ONE result per tournament
            $table->unique('tournament_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_results');
    }
};
