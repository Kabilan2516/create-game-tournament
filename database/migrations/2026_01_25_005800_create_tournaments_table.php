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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();

            // Organizer
            $table->foreignId('organizer_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Basic Info
            $table->string('title');
            $table->enum('game', ['CODM', 'PUBG']);
            $table->enum('mode', ['solo', 'duo', 'squad']);

            // Match Settings (CODM Focus Ready)
            $table->string('map')->nullable();
            $table->string('match_type')->nullable(); // TDM, S&D, BR, MP

            // Fees & Rewards
            $table->enum('reward_type', [
                'free',
                'organizer_prize',
                'platform_points'
            ])->default('free');

            $table->decimal('entry_fee', 10, 2)->default(0);
            $table->decimal('prize_pool', 10, 2)->default(0);

            // Slots
            $table->integer('slots')->default(100);
            $table->integer('filled_slots')->default(0);

            // Time
            $table->dateTime('start_time');
            $table->dateTime('registration_close_time')->nullable();
            $table->string('region')->default('India');

            // Content
            $table->text('description')->nullable();
            $table->text('rules')->nullable();
            $table->boolean('auto_approve')->default(false);

            // Room Details (shared later)
            $table->string('room_id', 100)->nullable();
            $table->string('room_password', 255)->nullable();
            $table->boolean('is_paid')->default(false);

            $table->string('upi_id')->nullable();
            $table->string('upi_name')->nullable();
            $table->string('upi_qr')->nullable();

            $table->integer('first_prize')->nullable();
            $table->integer('second_prize')->nullable();
            $table->integer('third_prize')->nullable();
            $table->boolean('room_released')->default(false);
            // Promotion & Status
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['open', 'full', 'ongoing', 'completed', 'cancelled'])
                ->default('open');
            $table->index('status');
            $table->index('game');
            $table->index('organizer_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
