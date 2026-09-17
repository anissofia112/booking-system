<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSlotAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_booking_when_time_slot_is_full(): void
    {
        $date = '2026-10-15';
        $time = '09:00';

        foreach (range(1, 3) as $index) {
            Booking::create([
                'name' => 'Customer '.$index,
                'email' => 'customer'.$index.'@example.com',
                'phone' => '123456789',
                'service_name' => 'Consultation',
                'booking_date' => $date,
                'booking_time' => $time,
                'status' => 'pending',
            ]);
        }

        $response = $this->from('/book')->post('/book', [
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
            'phone' => '987654321',
            'service_name' => 'Consultation',
            'booking_date' => $date,
            'booking_time' => $time,
        ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertDatabaseCount('bookings', 3);
    }
}
