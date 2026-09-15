<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Appointment Portal</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 800px;
            padding: 20px;
            text-align: center;
        }
        h1 {
            color: #0f172a;
            font-size: 2.2rem;
            margin-bottom: 8px;
        }
        p.subtitle {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 40px;
        }
        .portal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        @media (max-width: 640px) {
            .portal-grid { grid-template-columns: 1fr; }
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 35px 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .icon-client { background-color: #e0f2fe; color: #0284c7; }
        .icon-admin { background-color: #fef3c7; color: #d97706; }
        .card h2 {
            margin: 0 0 10px;
            color: #1e293b;
            font-size: 1.4rem;
        }
        .card p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 25px;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .btn-client {
            background-color: #0284c7;
            color: #ffffff;
        }
        .btn-client:hover { background-color: #0369a1; }
        .btn-admin {
            background-color: #0f172a;
            color: #ffffff;
        }
        .btn-admin:hover { background-color: #1e293b; }
    </style>
</head>
<body>

<div class="container">
    <h1>Service Booking & Management</h1>
    <p class="subtitle">Please select your destination to continue</p>

    <div class="portal-grid">
        <!-- Client Portal Card -->
        <div class="card">
            <div>
                <div class="icon-box icon-client">&#128197;</div>
                <h2>Client Services</h2>
                <p>Reserve an appointment for consultations, packages, or general service sessions.</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="btn btn-client">Book an Appointment &rarr;</a>
        </div>

        <!-- Admin Portal Card -->
        <div class="card">
            <div>
                <div class="icon-box icon-admin">&#128274;</div>
                <h2>Staff & Admin</h2>
                <p>Manage existing reservations, verify customer requests, and update reservation statuses.</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-admin">Admin Dashboard &rarr;</a>
        </div>
    </div>
</div>

</body>
</html>