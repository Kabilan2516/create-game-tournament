<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_result_entries', function (Blueprint $table) {
            $table->integer('kp')->default(0)->after('points');
            $table->integer('pp')->default(0)->after('kp');
            $table->integer('tt')->default(0)->after('pp');
            $table->integer('cd')->default(0)->after('tt');
        });
    }

    public function down(): void
    {
        Schema::table('match_result_entries', function (Blueprint $table) {
            $table->dropColumn(['kp', 'pp', 'tt', 'cd']);
        });
    }
};
