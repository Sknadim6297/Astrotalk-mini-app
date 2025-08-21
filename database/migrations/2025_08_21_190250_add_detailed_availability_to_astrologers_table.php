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
        Schema::table('astrologers', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('is_online');
            $table->json('weekly_availability')->nullable()->after('availability');
            $table->json('today_availability')->nullable()->after('weekly_availability');
            $table->boolean('is_available_now')->default(false)->after('today_availability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astrologers', function (Blueprint $table) {
            $table->dropColumn(['last_seen_at', 'weekly_availability', 'today_availability', 'is_available_now']);
        });
    }
};
