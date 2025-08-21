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
            $table->text('bio')->nullable()->after('wallet_balance');
            $table->string('education')->nullable()->after('bio');
            $table->text('certifications')->nullable()->after('education');
            $table->json('availability')->nullable()->after('certifications');
            $table->string('timezone')->default('Asia/Kolkata')->after('availability');
            $table->boolean('is_online')->default(true)->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astrologers', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'education', 
                'certifications',
                'availability',
                'timezone',
                'is_online'
            ]);
        });
    }
};
