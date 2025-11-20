<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class PaymentController extends Controller
{
    /**
     * Show the payment page.
     */
    public function show(Order $order)
    {
        return view('orders.payment', compact('order'));
    }

    /**
     * Handle Paybill reference submission.
     */
    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_ref' => 'required|string|max:50',
        ]);

        $order->update([
            'payment_ref' => $data['payment_ref'],
            'status' => 'paid',
        ]);

        return redirect()
            ->route('orders.thankyou')
            ->with('success', 'Payment reference recorded successfully!');
    }

    /**
     * Handle STK Push request.
     */
    public function stkPush(Request $request, Order $order)
    {
        $data = $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ]);

        $timestamp = now()->format('YmdHis');
        $shortcode = env('MPESA_SHORTCODE');
        $passkey   = env('MPESA_PASSKEY');
        $password  = base64_encode($shortcode . $passkey . $timestamp);

        // ✅ Generate access token dynamically
        $accessToken = Http::withBasicAuth(
            env('MPESA_CONSUMER_KEY'),
            env('MPESA_CONSUMER_SECRET')
        )->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials')
        ->json()['access_token'] ?? null;

        // ✅ Send STK Push request using dynamic token
        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1, // sandbox only accepts 1
            'PartyA' => $data['phone'],
            'PartyB' => $shortcode,
            'PhoneNumber' => $data['phone'],
            #'CallBackURL' => route('mpesa.callback'),
            'CallBackURL' => 'https://your-ngrok-url.ngrok.io/mpesa/callback',
            'AccountReference' => $order->id,
            'TransactionDesc' => 'Payment for Order #' . $order->id,
        ];

        $response = Http::withToken($accessToken)
            ->post(env('MPESA_STK_URL'), $payload);
        // return $response->json();

        // ✅ Update order status
        $order->update([
            'status' => 'payment_initiated',
        ]);

        // return redirect()
        //     ->route('orders.thankyou')
        //     ->with('success', 'STK Push sent to your phone. Please approve payment to finalize your order.');
        return redirect()->route('orders.thankyou')
            ->with([
                'email' => $order->email,
                'phone' => $order->phone,
                'checkout_id' => $order->checkout_id,
                'success' => 'Payment received successfully!',
            ]);

    }
    /**
     * Handle M-Pesa callback.
     */
    public function callback(Request $request)
    {
        $data = $request->all();
        \Log::info('M-Pesa Callback', $data);

        $stk = $data['Body']['stkCallback'] ?? null;
        if (!$stk) {
            return response()->json(['status' => 'missing stkCallback']);
        }

        $resultCode = $stk['ResultCode'];
        $items = $stk['CallbackMetadata']['Item'] ?? [];

        $orderId = null;
        $paymentRef = null;

        foreach ($items as $item) {
            if ($item['Name'] === 'AccountReference') {
                $orderId = $item['Value'];
            }
            if ($item['Name'] === 'MpesaReceiptNumber') {
                $paymentRef = $item['Value'];
            }
        }

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                if ($resultCode == 0) {
                    $order->update([
                        'status' => 'paid', // or Order::STATUS_PAID if defined
                        'payment_ref' => $paymentRef,
                    ]);
                } else {
                    $order->update([
                        'status' => 'failed',
                    ]);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}