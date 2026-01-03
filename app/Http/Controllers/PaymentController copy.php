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

        $accessToken = Http::withBasicAuth(
            env('MPESA_CONSUMER_KEY'),
            env('MPESA_CONSUMER_SECRET')
        )->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials')
        ->json()['access_token'] ?? null;

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1, // sandbox only accepts 1
            'PartyA' => $data['phone'],
            'PartyB' => $shortcode,
            'PhoneNumber' => $data['phone'],
            'CallBackURL' => 'https://manuela-renowned-lauri.ngrok-free.dev/mpesa/callback',
            'AccountReference' => $order->id,
            'TransactionDesc' => 'Payment for Order #' . $order->id,
        ];

        Http::withToken($accessToken)->post(env('MPESA_STK_URL'), $payload);

        $order->update([
            'status' => 'payment_initiated',
        ]);

        return redirect()->route('orders.thankyou')->with([
            'success' => 'STK Push sent to your phone. Please approve payment to finalize your order.',
        ]);
    }

    /**
     * Handle M-Pesa callback.
     */
    public function callback(Request $request)
    {
        $stkCallback = $request->input('Body.stkCallback');

        if (!$stkCallback) {
            return response()->json(['status' => 'missing stkCallback']);
        }

        $items = collect($stkCallback['CallbackMetadata']['Item'] ?? []);
        $receipt = optional($items->firstWhere('Name', 'MpesaReceiptNumber'))['Value'] ?? null;
        $accountRef = optional($items->firstWhere('Name', 'AccountReference'))['Value'] ?? null;

        $order = Order::find($accountRef);

        if ($order) {
            if ($stkCallback['ResultCode'] === 0) {
                $order->update([
                    'status' => 'paid',
                    'payment_ref' => $receipt,
                ]);
            } else {
                $order->update([
                    'status' => 'failed',
                ]);
            }
        }

        Log::info('M-Pesa Callback', [
            'Order ID' => $accountRef,
            'ResultCode' => $stkCallback['ResultCode'] ?? null,
            'ResultDesc' => $stkCallback['ResultDesc'] ?? null,
            'Receipt' => $receipt,
        ]);

        return response()->json([
            'status' => $order ? 'updated' : 'order not found',
            'order_id' => $accountRef,
            'result_code' => $stkCallback['ResultCode'] ?? null,
        ]);
    }
}