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
        Schema::create('wallet_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('points_used');

            $table->enum('reward_type', [
                'amazon_gift_card',
                'flipkart_gift_card'
            ]);

            $table->string('reward_value'); // ₹100, ₹250, etc

            $table->enum('status', [
                'pending',
                'approved',
                'sent',
                'rejected'
            ])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_redemptions');
    }
};
