<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournament_series', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
            $table->text('rules')->nullable()->after('description');
            $table->string('region')->nullable()->after('map');

            $table->string('reward_type')->default('free')->after('region');
            $table->boolean('is_paid')->default(false)->after('reward_type');
            $table->decimal('entry_fee', 10, 2)->default(0)->after('is_paid');
            $table->decimal('prize_pool', 10, 2)->default(0)->after('entry_fee');

            $table->string('upi_id')->nullable()->after('prize_pool');
            $table->string('upi_name')->nullable()->after('upi_id');
            $table->string('upi_qr')->nullable()->after('upi_name');
        });
    }

    public function down(): void
    {
        Schema::table('tournament_series', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle',
                'rules',
                'region',
                'reward_type',
                'is_paid',
                'entry_fee',
                'prize_pool',
                'upi_id',
                'upi_name',
                'upi_qr',
            ]);
        });
    }
};
