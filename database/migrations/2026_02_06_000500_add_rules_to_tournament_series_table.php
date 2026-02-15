<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournament_series', function (Blueprint $table) {
            $table->string('game')->nullable()->after('organizer_id');
            $table->string('match_type')->nullable()->after('mode');
            $table->string('map')->nullable()->after('match_type');
            $table->integer('kill_point')->default(1)->after('map');
            $table->json('placement_points')->nullable()->after('kill_point');
        });
    }

    public function down(): void
    {
        Schema::table('tournament_series', function (Blueprint $table) {
            $table->dropColumn(['game', 'match_type', 'map', 'kill_point', 'placement_points']);
        });
    }
};
