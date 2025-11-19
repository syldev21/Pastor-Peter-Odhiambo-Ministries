<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class CheckoutController extends Controller
{
    public function show()
    {
        if (auth()->check()) {
            $cartItems = auth()->user()->cart()->with('book')->get();
            $total = $cartItems->sum(fn($item) => $item->book->price * $item->quantity);
        } else {
            $cartItems = session()->get('cart', []);
            $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        }

        return view('checkout.show', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'delivery_name' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:255',
        ]);

        if (auth()->check()) {
            // Signed-in user: use database cart
            $cartItems = auth()->user()->cart()->with('book')->get();

            $totalAmount = $cartItems->sum(fn ($item) => $item->quantity * $item->book->price);

            $order = auth()->user()->orders()->create([
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_ref' => null,
                'delivery_name' => $data['delivery_name'],
                'delivery_phone' => $data['delivery_phone'],
                'delivery_address' => $data['delivery_address'],
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->price,
                ]);
            }

            auth()->user()->cart()->delete();
        } else {
            // Guest user: use session cart
            $cartItems = session()->get('cart', []);
            $totalAmount = collect($cartItems)->sum(fn ($item) => $item['price'] * $item['quantity']);

            $order = \App\Models\Order::create([
                'user_id' => null,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_ref' => null,
                'delivery_name' => $data['delivery_name'],
                'delivery_phone' => $data['delivery_phone'],
                'delivery_address' => $data['delivery_address'],
            ]);

            foreach ($cartItems as $bookId => $item) {
                $order->items()->create([
                    'book_id' => $bookId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            session()->forget('cart');
        }

        return redirect()->route('orders.payment.show', $order);
    }
    public function paymentForm(Order $order)
    {
        $this->authorize('update', $order); // optional: ensure user owns the order
        return view('checkout.payment', compact('order'));
    }

    public function submitPayment(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'payment_ref' => 'required|string|max:255',
        ]);

        $order->update([
            'payment_ref' => $request->payment_ref,
            'status' => 'paid',
        ]);

        return redirect()->route('dashboard')->with('success', 'Payment reference submitted.');
    }
}