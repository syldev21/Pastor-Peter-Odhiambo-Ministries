@extends('layouts.app')

@section('content')
    <h2>Checkout</h2>
    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf
        <input name="delivery_name" placeholder="Full Name" required>
        <input name="delivery_phone" placeholder="Phone Number" required>
        <input name="delivery_address" placeholder="Delivery Address" required>

        <ul>
            @foreach($cartItems as $item)
                <li>{{ $item->book->title }} — Qty: {{ $item->quantity }}</li>
            @endforeach
        </ul>

        <button type="submit">Place Order</button>
    </form>
@endsection
