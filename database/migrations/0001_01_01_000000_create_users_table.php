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
        // 1. Users Table (with Role support for Admin)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->default('client'); // 'client' or 'admin'
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Password Reset Tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Sessions Table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 4. Bookings Table (supports courts, 10-min temporary hold, and pricing)
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('service_name'); // Selected Court (e.g. 'Court 1 (Rubber Mat)')
            $table->date('booking_date');
            $table->time('booking_time');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('status')->default('reserved')->index(); // 'reserved', 'confirmed', 'cancelled', 'expired'
            $table->timestamp('hold_expires_at')->nullable()->index();
            $table->timestamps();
        });

        // 5. Payments Table (tracks gateway transaction IDs, status & raw payload)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('provider')->default('bayarcash');
            $table->string('payment_id')->nullable()->index(); // Gateway reference / transaction ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MYR');
            $table->string('status')->default('pending'); // 'pending', 'successful', 'failed', 'expired'
            $table->json('payload')->nullable();          // Raw webhook / callback JSON
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropped in reverse order of creation to satisfy foreign key constraints
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};