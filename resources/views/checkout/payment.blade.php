@extends('layouts.app')

@section('content')
    <h2>Submit Payment Reference</h2>

    <form method="POST" action="{{ route('orders.payment.submit', $order) }}">
        @csrf
        <label for="payment_ref">Payment Reference (M-Pesa or PayPal):</label>
        <input type="text" name="payment_ref" id="payment_ref" required>
        <button type="submit">Submit</button>
    </form>
@endsection
