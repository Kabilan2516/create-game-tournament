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
        Schema::table('tournaments', function (Blueprint $table) {
            $table->unsignedInteger('substitute_count')->default(0)->after('slots');
        });

        Schema::table('tournament_series', function (Blueprint $table) {
            $table->unsignedInteger('substitute_count')->default(0)->after('mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('substitute_count');
        });

        Schema::table('tournament_series', function (Blueprint $table) {
            $table->dropColumn('substitute_count');
        });
    }
};
