<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BayarCash (Sandbox Simulator)</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; }
        .amount { font-size: 32px; font-weight: bold; color: #2c3e50; margin: 20px 0; }
        button { background: #27ae60; color: white; border: none; padding: 12px 20px; font-size: 16px; border-radius: 5px; cursor: pointer; width: 100%; margin-bottom: 10px; }
        button.cancel { background: #e74c3c; }
    </style>
</head>
<body>

    <div class="card">
        <h2>🔒 Secure Checkout (Sandbox)</h2>
        <p>Paying for: <strong>{{ $payment->booking->service_name }}</strong></p>
        <p>Date: {{ $payment->booking->booking_date }}</p>
        <div class="amount">RM {{ $payment->amount }}</div>
        
        <form action="{{ route('payment.webhook') }}" method="POST" id="payment-form">
            @csrf
            <!-- Simulate the data BayarCash would send back -->
            <input type="hidden" name="payment_record_id" value="{{ $payment->id }}">
            <input type="hidden" name="status" value="paid">
            
            <button type="submit" onclick="simulatePayment(event)">Simulate Successful Payment</button>
        </form>

        <a href="{{ route('bookings.create') }}">
            <button class="cancel">Cancel & Go Back</button>
        </a>
    </div>

    <script>
        function simulatePayment(e) {
            e.preventDefault();
            const form = document.getElementById('payment-form');
            const btn = e.target;
            
            btn.textContent = "Processing...";
            btn.style.background = "#95a5a6";

            // 1. Fire webhook to update database
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            }).then(() => {
                // 2. Redirect user back to booking page
                window.location.href = "{{ route('payment.return') }}";
            });
        }
    </script>

</body>
</html>