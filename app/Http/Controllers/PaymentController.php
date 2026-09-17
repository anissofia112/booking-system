<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * 1. Send the user to the real Payment Gateway
     */
    public function checkout(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->route('bookings.create')
                ->withErrors('This payment has already been processed or expired.');
        }

        // Call the Payment Gateway API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.payment.key'),
            'Accept'        => 'application/json',
        ])->post(config('services.payment.url'), [
            'order_id'     => $payment->id,
            'amount'       => $payment->amount,
            'currency'     => 'MYR',
            'customer'     => [
                'name'  => $payment->booking->name,
                'email' => $payment->booking->email,
                'phone' => $payment->booking->phone,
            ],
            'description'  => 'Court Booking: ' . $payment->booking->service_name,
            'redirect_url' => route('payment.return'),
            'webhook_url'  => route('payment.webhook'),
        ]);

        // If the API successfully generates a checkout link, redirect the user there
        if ($response->successful() && isset($response['payment_url'])) {
            return redirect()->away($response['payment_url']);
        }

        // Log the error for debugging if the API rejects the request
        Log::error('Payment Gateway Error', ['response' => $response->json()]);

        return redirect()->route('bookings.create')
            ->withErrors('Unable to connect to the payment gateway. Please try again later.');
    }

    /**
     * 2. User returns to this page after paying
     */
    public function handleReturn(Request $request)
    {
        return redirect()->route('bookings.create')
            ->with('success', 'Payment complete! Your court reservation is now officially confirmed.');
    }

    /**
     * 3. Real Background Webhook (Server-to-Server)
     */
    public function handleWebhook(Request $request, PaymentService $paymentService)
    {
        // 1. Get the order ID and status sent by the gateway
        $paymentId = $request->input('order_id');
        $status = $request->input('status'); // e.g., 'paid', 'successful'
        $transactionId = $request->input('transaction_id', 'TXN_' . rand(1000, 9999));

        $payment = Payment::find($paymentId);

        // 2. Verify payment is real and hasn't been processed yet
        if ($payment && $payment->status === 'pending' && in_array($status, ['paid', 'successful', 'completed'])) {
            $paymentService->markAsSuccessful($payment, $transactionId, $request->all());
        }

        // 3. Always return a 200 OK so the gateway knows you received the webhook
        return response()->json(['status' => 'success']);
    }
}