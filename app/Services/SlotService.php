<?php

namespace App\Services;

use App\Models\Booking;

class SlotService
{
    public static function getCourtAvailability(string $date, string $court): array
    {
        $slotTimes = config('booking.slots', [
            '09:00', '10:00', '11:00', '14:00', '15:00', '16:00',
            '17:00', '18:00', '19:00', '20:00', '21:00', '22:00',
        ]);
        $maxPerSlot = config('booking.max_per_slot', 1);

        // UPDATED: Use the blocksSlot() scope
        $bookedTimes = Booking::where('booking_date', $date)
            ->where('service_name', $court)
            ->blocksSlot()
            ->pluck('booking_time')
            ->map(fn($time) => substr($time, 0, 5))
            ->toArray();

        $slots = [];

        foreach ($slotTimes as $time) {
            $isBooked = in_array($time, $bookedTimes);
            $remaining = $isBooked ? 0 : $maxPerSlot;

            $slots[] = [
                'time'      => $time,
                'is_full'   => $isBooked,
                'remaining' => $remaining,
            ];
        }

        return $slots;
    }

    public static function isSlotFull(string $date, string $time, string $court): bool
    {
        // UPDATED: Use the blocksSlot() scope
        return Booking::where('booking_date', $date)
            ->where('service_name', $court)
            ->where('booking_time', 'like', $time . '%')
            ->blocksSlot()
            ->exists();
    }
}