<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    // Display all bookings with quick filter support
    public function index(Request $request)
    {
        $status = $request->query('status');

        $bookings = Booking::when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->orderBy('booking_date', 'asc')
        ->orderBy('booking_time', 'asc')
        ->get();

        return view('admin.bookings', compact('bookings', 'status'));
    }

    // Admin updates status (confirm, cancel, complete)
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update(['status' => $validated['status']]);

        return redirect()->route('admin.bookings.index')
                         ->with('success', "Booking #{$booking->id} marked as {$validated['status']}.");
    }

    // Delete a record
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')
                         ->with('success', "Booking #{$booking->id} removed.");
    }
}