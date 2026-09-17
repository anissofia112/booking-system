<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Booking Management</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 30px auto; padding: 0 15px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .filters a { margin-right: 10px; text-decoration: none; padding: 6px 12px; background: #e2e8f0; border-radius: 4px; color: #333; }
        .filters a.active { background: #0284c7; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
        th { background: #f8fafc; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dcfce7; color: #166534; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        button { border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-confirm { background: #16a34a; color: white; }
        .btn-cancel { background: #f59e0b; color: white; }
        .btn-delete { background: #dc2626; color: white; }
        .alert { background: #dcfce7; color: #166534; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
    <h1>Admin Booking Panel</h1>
    <div style="display: flex; align-items: center; gap: 15px;">
        <span>Logged in as: <strong>{{ auth()->user()->name }}</strong></span>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" style="background: #64748b; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">Log Out</button>
        </form>
    </div>
</div>

    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    <div class="filters">
        <a href="{{ route('admin.bookings.index') }}" class="{{ empty($status) ? 'active' : '' }}">All</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}" class="{{ $status === 'confirmed' ? 'active' : '' }}">Confirmed</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="{{ $status === 'cancelled' ? 'active' : '' }}">Cancelled</a>
    </div>

    <table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Player</th>
            <th>Court</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bookings as $booking)
            @php
                // Parse the start time and calculate the end time (1 hour later per slot row)
                $start = \Carbon\Carbon::parse($booking->booking_time);
                $end = $start->copy()->addMinutes(60);
            @endphp
            <tr>
                <td>#{{ $booking->id }}</td>
                <td>
                    {{ $booking->name }}<br>
                    <small>{{ $booking->phone }}</small>
                </td>
                <td><strong>{{ $booking->service_name }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                
                <!-- Display Start and End Time logically -->
                <td>{{ $start->format('H:i') }}</td>
                <td>{{ $end->format('H:i') }}</td>
                
                <td>
                    <span class="badge badge-{{ strtolower($booking->status) }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                <td>
                    @if($booking->total_amount > 0)
                        RM {{ $booking->total_amount }}
                    @else
                        - <!-- Hide 0.00 for consecutive hours of the same booking -->
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No court bookings found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
</body>
</html>