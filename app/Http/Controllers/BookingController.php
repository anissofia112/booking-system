<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\SlotService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $today = now()->toDateString();
        $courts = array_keys(config('booking.courts', [
            'Court 1 (Rubber Mat)'  => 40.00,
            'Court 2 (Rubber Mat)'  => 40.00,
            'Court 3 (VIP Parquet)' => 60.00,
        ]));
        
        $defaultCourt = $courts[0] ?? 'Court 1 (Rubber Mat)';
        $slots = SlotService::getCourtAvailability($today, $defaultCourt);

        // Only show successfully confirmed bookings on the public schedule
        $bookings = Booking::where('status', 'confirmed')
            ->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->get();

        return view('welcome', compact('slots', 'today', 'courts', 'defaultCourt', 'bookings'));
    }

    public function availability(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $court = $request->query('court', array_keys(config('booking.courts', []))[0] ?? '');

        return response()->json(SlotService::getCourtAvailability($date, $court));
    }

    // UPDATED: Inject PaymentService and calculate prices
    public function store(Request $request, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|max:100',
            'phone'        => 'required|string|max:20',
            'service_name' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
            'duration'     => 'required|integer|min:1|max:4',
        ]);

        $slots = config('booking.slots', []);
        $startIndex = array_search($validated['booking_time'], $slots);

        if ($startIndex === false || ($startIndex + $validated['duration']) > count($slots)) {
            return back()->withInput()->withErrors(['booking_time' => 'Selected duration exceeds operating hours.']);
        }

        $slotsToBook = array_slice($slots, $startIndex, $validated['duration']);

        foreach ($slotsToBook as $time) {
            if (SlotService::isSlotFull($validated['booking_date'], $time, $validated['service_name'])) {
                return back()->withInput()->withErrors(['booking_time' => "The slot at {$time} is unavailable."]);
            }
        }

        // 1. Calculate Total Amount based on Court Price (with strict type casting)
        $courtsConfig = config('booking.courts', []);

        // Extract the price safely in case the config returns a nested array
        $rawPrice = $courtsConfig[$validated['service_name']] ?? 40.00;
        $pricePerHour = is_array($rawPrice) ? (float) ($rawPrice['price'] ?? 40.00) : (float) $rawPrice;

        $duration = (int) $validated['duration'];
        $totalAmount = $pricePerHour * $duration;

        // 2. Create Bookings (Save each hour, attach the full price to the first hour)
        $primaryBooking = null;
        
        foreach ($slotsToBook as $index => $time) {
            $booking = Booking::create([
                'name'            => $validated['name'],
                'email'           => $validated['email'],
                'phone'           => $validated['phone'],
                'service_name'    => $validated['service_name'],
                'booking_date'    => $validated['booking_date'],
                'booking_time'    => $time,
                'status'          => 'reserved', 
                'hold_expires_at' => now()->addMinutes(10),
                'total_amount'    => $index === 0 ? $totalAmount : 0,
            ]);

            if ($index === 0) {
                $primaryBooking = $booking;
            }
        }

        // 3. Generate Payment Intent using PaymentService
        $payment = $paymentService->createPaymentIntent($primaryBooking, $totalAmount);

        // 4. Redirect to the Payment Checkout page
        return redirect()->route('payment.checkout', $payment->id);
    } 
}