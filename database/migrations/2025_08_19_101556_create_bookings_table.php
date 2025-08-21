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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // customer
            $table->foreignId('astrologer_id')->constrained('users')->onDelete('cascade'); // astrologer
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled', 'insufficient_balance'])->default('pending');
            $table->decimal('per_minute_rate', 8, 2); // rate at time of booking
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->default(0); // calculated duration
            $table->decimal('total_amount', 10, 2)->default(0); // total amount deducted
            $table->timestamp('last_deduction_at')->nullable(); // last time money was deducted
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'started_at']);
            $table->index(['user_id', 'status']);
            $table->index(['astrologer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
