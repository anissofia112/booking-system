<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Reserve slot and prepare pending payment record.
     */
    public function createPaymentIntent(Booking $booking, float $amount): Payment
    {
        return DB::transaction(function () use ($booking, $amount) {
            // 1. Lock the booking for 10 minutes
            $booking->update([
                'status'          => 'reserved',
                'hold_expires_at' => now()->addMinutes(10),
                'total_amount'    => $amount,
            ]);

            // 2. Create the associated pending payment
            return $booking->payments()->create([
                'provider' => 'bayarcash',
                'amount'   => $amount,
                'currency' => 'MYR',
                'status'   => 'pending',
            ]);
        });
    }

    /**
     * Finalize booking upon webhook confirmation.
     */
    public function markAsSuccessful(Payment $payment, string $gatewayTransactionId, array $rawPayload): void
    {
        DB::transaction(function () use ($payment, $gatewayTransactionId, $rawPayload) {
            // 1. Mark payment as paid
            $payment->update([
                'status'     => 'successful',
                'payment_id' => $gatewayTransactionId,
                'payload'    => $rawPayload,
                'paid_at'    => now(),
            ]);

            // 2. Confirm the booking and remove the expiration hold
            $payment->booking->update([
                'status'          => 'confirmed',
                'hold_expires_at' => null,
            ]);
        });
    }

    /**
     * Expire stale holds (run this via Laravel Scheduler later).
     */
    public function releaseExpiredHolds(): int
    {
        return Booking::where('status', 'reserved')
            ->where('hold_expires_at', '<=', now())
            ->update(['status' => 'expired']);
    }
}