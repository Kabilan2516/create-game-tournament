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
        Schema::create('tournament_series', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('organizer_id');

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('mode', ['solo', 'duo', 'squad']);

            $table->date('start_date');
            $table->date('end_date');

            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index('organizer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_series');
    }
};
