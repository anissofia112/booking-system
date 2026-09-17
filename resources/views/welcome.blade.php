<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badminton Court Booking System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; padding: 0 15px; }
        form { display: grid; gap: 12px; max-width: 450px; margin-bottom: 30px; }
        input, select, button { padding: 9px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .error-text { color: #dc3545; font-size: 12px; margin-top: -6px; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-confirmed { background: #c3e6cb; color: #155724; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>🏸 Badminton Court Booking System</h1>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-danger">
            <strong>Please check the errors below:</strong>
        </div>
    @endif

    <!-- POST: Send booking request to the backend -->
    <form action="{{ route('bookings.store') }}" method="POST">
        @csrf

        <div>
            <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required style="width: 100%; box-sizing: border-box;">
            @error('name') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div>
            <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required style="width: 100%; box-sizing: border-box;">
            @error('email') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div>
            <input type="tel" name="phone" placeholder="Phone Number / WhatsApp" value="{{ old('phone') }}" required style="width: 100%; box-sizing: border-box;">
            @error('phone') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <!-- 1. Court Selector -->
        <div>
            <label for="court_select" style="font-size: 12px; color: #555;">Choose Court:</label>
            <select id="court_select" name="service_name" required style="width: 100%; box-sizing: border-box;">
                @foreach($courts as $court)
                    <option value="{{ $court }}" {{ old('service_name', $defaultCourt ?? '') == $court ? 'selected' : '' }}>
                        {{ $court }}
                    </option>
                @endforeach
            </select>
            @error('service_name') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <!-- 2. Booking Date -->
        <div>
            <label for="booking_date" style="font-size: 12px; color: #555;">Booking Date:</label>
            <input 
                type="date" 
                id="booking_date" 
                name="booking_date" 
                value="{{ old('booking_date', $today ?? date('Y-m-d')) }}" 
                min="{{ date('Y-m-d') }}" 
                required 
                style="width: 100%; box-sizing: border-box;"
            >
            @error('booking_date') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <!-- 3. Duration Selector (1 to 4 Consecutive Hours) -->
        <div>
            <label for="duration" style="font-size: 12px; color: #555;">Duration (Hours):</label>
            <select id="duration" name="duration" required style="width: 100%; box-sizing: border-box;">
                <option value="1" {{ old('duration') == '1' ? 'selected' : '' }}>1 Hour</option>
                <option value="2" {{ old('duration') == '2' ? 'selected' : '' }}>2 Hours</option>
                <option value="3" {{ old('duration') == '3' ? 'selected' : '' }}>3 Hours</option>
                <option value="4" {{ old('duration') == '4' ? 'selected' : '' }}>4 Hours</option>
            </select>
            @error('duration') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <!-- 4. Dynamic Time Slot Selector -->
        <div>
            <label for="booking_time" style="font-size: 12px; color: #555;">Start Time:</label>
            <select id="booking_time" name="booking_time" required style="width: 100%; box-sizing: border-box;">
                @if(isset($slots) && count($slots) > 0)
                    @foreach($slots as $slot)
                        <option 
                            value="{{ $slot['time'] }}" 
                            {{ $slot['is_full'] ? 'disabled' : '' }}
                            {{ old('booking_time') == $slot['time'] ? 'selected' : '' }}
                        >
                            {{ $slot['time'] }} ({{ $slot['is_full'] ? 'Booked' : 'Available' }})
                        </option>
                    @endforeach
                @else
                    <option value="">Select date and court to see slots</option>
                @endif
            </select>
            @error('booking_time') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Confirm Court Reservation</button>
    </form>

    <!-- GET: Real-time Schedule Table -->
    <h2>Court Schedule Status</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Court</th>
                <th>Date</th>
                <th>Slot</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->name }}</td>
                    <td><strong>{{ $booking->service_name }}</strong></td>
                    <td>{{ $booking->booking_date }}</td>
                    <td>{{ substr($booking->booking_time, 0, 5) }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($booking->status) }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No court bookings found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Client Script: Dynamically Fetch Slots & Enforce Consecutive Hours -->
    <script>
        const dateInput = document.getElementById('booking_date');
        const courtSelect = document.getElementById('court_select');
        const durationSelect = document.getElementById('duration');
        const timeDropdown = document.getElementById('booking_time');

        function fetchAvailableSlots() {
            const date = dateInput.value;
            const court = courtSelect.value;
            const duration = parseInt(durationSelect.value, 10);

            if (!date || !court) return;

            timeDropdown.innerHTML = '<option value="">Checking court slots...</option>';
            timeDropdown.disabled = true;

            fetch(`{{ route('slots.availability') }}?date=${encodeURIComponent(date)}&court=${encodeURIComponent(court)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(slots => {
                    timeDropdown.innerHTML = '';
                    timeDropdown.disabled = false;

                    if (slots.length === 0) {
                        timeDropdown.innerHTML = '<option value="">No operating slots found</option>';
                        return;
                    }

                    slots.forEach((slot, index) => {
                        let canFitDuration = true;

                        // Check if duration exceeds closing time
                        if (index + duration > slots.length) {
                            canFitDuration = false;
                        } else {
                            // Check if every consecutive hour needed is free
                            for (let i = 0; i < duration; i++) {
                                if (slots[index + i].is_full) {
                                    canFitDuration = false;
                                    break;
                                }
                            }
                        }

                        const opt = document.createElement('option');
                        opt.value = slot.time;

                        if (slot.is_full) {
                            opt.textContent = `${slot.time} (Booked)`;
                            opt.disabled = true;
                        } else if (!canFitDuration) {
                            opt.textContent = `${slot.time} (Not enough consecutive hours)`;
                            opt.disabled = true;
                        } else {
                            opt.textContent = `${slot.time} (Available)`;
                        }

                        timeDropdown.appendChild(opt);
                    });
                })
                .catch(() => {
                    timeDropdown.innerHTML = '<option value="">Failed to load available slots</option>';
                    timeDropdown.disabled = false;
                });
        }

        dateInput.addEventListener('change', fetchAvailableSlots);
        courtSelect.addEventListener('change', fetchAvailableSlots);
        durationSelect.addEventListener('change', fetchAvailableSlots);

        // Initial check on page load to apply duration validation
        fetchAvailableSlots();
    </script>
</body>
</html>