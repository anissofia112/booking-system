<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 30px auto; padding: 0 15px; }
        form { display: grid; gap: 10px; max-width: 400px; margin-bottom: 30px; }
        input, select, button { padding: 8px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Booking System</h1>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <!-- POST: Send form data to the backend -->
    <form action="{{ route('bookings.store') }}" method="POST">
        @csrf <!-- Required CSRF protection in Laravel -->
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="tel" name="phone" placeholder="Phone Number" required>
        <select name="service_name" required>
            <option value="">-- Select Service --</option>
            <option value="Consultation">Consultation</option>
            <option value="General Service">General Service</option>
        </select>
        <input type="date" name="booking_date" required>
        <input type="time" name="booking_time" required>
        <button type="submit">Submit Reservation</button>
    </form>

    <!-- GET: Display records from database -->
    <h2>Existing Reservations</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->id }}</td>
                    <td>{{ $booking->name }}</td>
                    <td>{{ $booking->email }}</td>
                    <td>{{ $booking->service_name }}</td>
                    <td>{{ $booking->booking_date }}</td>
                    <td>{{ $booking->booking_time }}</td>
                    <td>{{ $booking->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No bookings found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>