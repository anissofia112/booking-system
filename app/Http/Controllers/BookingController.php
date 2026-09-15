<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // GET: Fetch records from the database table and pass to welcome.blade.php
    public function index()
    {
        $bookings = Booking::orderBy('booking_date', 'asc')
                           ->orderBy('booking_time', 'asc')
                           ->get();

        return view('welcome', compact('bookings'));
    }

    // POST: Insert the form submission into the database table
    public function store(Request $request)
{
    $validated = $request->validate([
        'name'         => 'required|string|max:100',
        'email'        => 'required|email|max:100',
        'phone'        => 'required|string|max:20',
        'service_name' => 'required|string|max:100',
        'booking_date' => 'required|date',
        'booking_time' => 'required',
    ]);

    Booking::create($validated);

    return redirect()->route('bookings.create')->with('success', 'Reservation submitted successfully!');
}
}