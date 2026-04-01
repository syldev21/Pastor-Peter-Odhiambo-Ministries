<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
     * Handle manual Paybill reference submission.
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

        Log::info('Manual payment recorded', [
            'order_id' => $order->id,
            'payment_ref' => $data['payment_ref'],
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
        Log::info('STK Push initiated', ['order_id' => $order->id]);

        $data = $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ]);
        Log::info('Validated phone', ['phone' => $data['phone']]);

        $timestamp = now()->format('YmdHis');
        $shortcode = env('MPESA_SHORTCODE');
        $passkey   = env('MPESA_PASSKEY');
        $password  = base64_encode($shortcode . $passkey . $timestamp);

        // Access token
        $accessTokenResponse = Http::withBasicAuth(
            env('MPESA_CONSUMER_KEY'),
            env('MPESA_CONSUMER_SECRET')
        )->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials')
        ->json();

        Log::info('Access Token Response', $accessTokenResponse);

        $accessToken = $accessTokenResponse['access_token'] ?? null;
        if (!$accessToken) {
            Log::error('Failed to obtain access token');
        }

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 8, // sandbox only accepts 1
            'PartyA' => $data['phone'],
            'PartyB' => $shortcode,
            'PhoneNumber' => $data['phone'],
            // 'CallBackURL' => 'https://manuela-renowned-lauri.ngrok-free.dev/api/mpesa/callback',
            'CallBackURL' => 'https://ons-lopez-battle-likely.trycloudflare.com/api/mpesa/callback',
            'AccountReference' => $order->id,
            'TransactionDesc' => 'Payment for Order #' . $order->id,
        ];
        Log::info('STK Push Payload', $payload);

        $response = Http::withToken($accessToken)
            ->post(env('MPESA_STK_URL'), $payload)
            ->json();

        Log::info('STK Push Response', $response);

        Log::info('CheckoutRequestID before saving', [
            'id' => $response['CheckoutRequestID'] ?? null,
        ]);

        $order->update([
            'status' => 'payment_initiated',
            'checkout_request_id' => $response['CheckoutRequestID'] ?? null,
        ]);

        Log::info('Order after STK Push update', $order->toArray());

        return redirect()->route('orders.thankyou')->with([
            'success' => 'STK Push sent to your phone. Please approve payment.',
        ]);
    }

    /**
     * Handle M-Pesa callback.
     */
    public function callback(Request $request)
    {
        Log::info('Callback HIT', $request->all());

        $stkCallback = $request->input('Body.stkCallback');
        if (!$stkCallback) {
            Log::error('Missing stkCallback in payload');
            return response()->json(['status' => 'missing stkCallback']);
        }

        Log::info('Parsed stkCallback', $stkCallback);

        $items = collect($stkCallback['CallbackMetadata']['Item'] ?? []);
        $receipt = optional($items->firstWhere('Name', 'MpesaReceiptNumber'))['Value'] ?? null;
        Log::info('Extracted receipt', ['receipt' => $receipt]);

        $checkoutId = $stkCallback['CheckoutRequestID'] ?? null;
        Log::info('CheckoutRequestID from callback', ['checkoutId' => $checkoutId]);

        $order = Order::where('checkout_request_id', $checkoutId)->first();

        if ($order) {
            Log::info('Order found for callback', ['order_id' => $order->id]);
            if ((int)$stkCallback['ResultCode'] === 0) {
                $order->update([
                    'status' => 'paid',
                    'payment_ref' => $receipt,
                ]);
                Log::info('Order updated to PAID', $order->toArray());
            } else {
                $order->update(['status' => 'failed']);
                Log::info('Order updated to FAILED', $order->toArray());
            }
        } else {
            Log::warning('Order not found for checkoutId', ['checkoutId' => $checkoutId]);
        }

        return response()->json([
            'status' => $order ? 'updated' : 'order not found',
            'order_id' => $order->id ?? null,
            'result_code' => $stkCallback['ResultCode'] ?? null,
        ]);
    }
}