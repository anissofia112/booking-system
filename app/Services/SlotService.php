<?php

namespace App\Services;

use App\Models\Booking;

class SlotService
{
    // Maximum allowed reservations per time slot
    public const MAX_PER_SLOT = 3;

    // Available operating slots
    public const DAILY_TIMES = [
        '09:00',
        '10:30',
        '13:00',
        '14:30',
        '16:00',
    ];

    /**
     * Get availability breakdown for a specific date.
     */
    public static function getAvailability(string $date): array
    {
        // Count active bookings (exclude cancelled ones)
        $bookedCounts = Booking::where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('booking_time, count(*) as count')
            ->groupBy('booking_time')
            ->pluck('count', 'booking_time')
            ->toArray();

        $slots = [];
        foreach (self::DAILY_TIMES as $time) {
            // Match H:i or H:i:s
            $booked = 0;
            foreach ($bookedCounts as $key => $count) {
                if (substr($key, 0, 5) === $time) {
                    $booked = $count;
                    break;
                }
            }

            $remaining = max(0, self::MAX_PER_SLOT - $booked);

            $slots[] = [
                'time' => $time,
                'booked' => $booked,
                'remaining' => $remaining,
                'is_full' => $remaining <= 0,
            ];
        }

        return $slots;
    }
}