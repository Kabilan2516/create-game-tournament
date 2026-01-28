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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Internal name
            $table->string('position'); // header, sidebar, in_content, footer
            $table->string('page')->nullable();
            // welcome, tournaments, blog, organizer, all

            $table->enum('user_type', ['all', 'guest', 'user', 'organizer'])->default('all');

            $table->enum('device', ['all', 'desktop', 'mobile'])->default('all');

            $table->boolean('is_active')->default(true);

            $table->text('code'); // AdSense or any ad HTML/JS

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
