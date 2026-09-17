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
        // 1. Create the payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('provider')->default('bayarcash');
            $table->string('payment_id')->nullable()->index(); // BayarCash transaction/order ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MYR');
            $table->string('status')->default('pending'); // pending, successful, failed, expired
            $table->json('payload')->nullable();          // Raw gateway responses
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 2. Extend the bookings table for slot hold and payment tracking
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0.00)->after('booking_time');
            }

            if (!Schema::hasColumn('bookings', 'hold_expires_at')) {
                $table->timestamp('hold_expires_at')->nullable()->after('status')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop payments table first to respect foreign key constraint
        Schema::dropIfExists('payments');

        // Drop the added columns from bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('bookings', 'total_amount')) {
                $columnsToDrop[] = 'total_amount';
            }

            if (Schema::hasColumn('bookings', 'hold_expires_at')) {
                $columnsToDrop[] = 'hold_expires_at';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};