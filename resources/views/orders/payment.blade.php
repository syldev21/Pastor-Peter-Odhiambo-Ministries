@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-lg rounded-xl p-8 mt-12">
    
    <!-- Heading -->
    <h2 class="text-3xl font-bold text-gray-900 mb-3 text-center">Complete Your Payment</h2>
    <p class="text-gray-600 mb-6 text-center">
        Choose your preferred M-Pesa payment method for 
        <span class="font-semibold text-gray-800">Order #{{ $order->id }}</span>
        <br>
        <span class="text-green-600 text-lg font-bold">KES {{ number_format($order->total_amount, 2) }}</span>
    </p>

    <!-- Payment Method Selection -->
    <div x-data="{ method: null }">
        
        <!-- Method Buttons -->
        <div class="flex justify-center space-x-4 mb-6">
            <button type="button" @click="method = 'paybill'"
                    :class="method === 'paybill' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200'"
                    class="px-5 py-3 rounded-lg font-semibold transition flex items-center space-x-2">
                <span>💳</span>
                <span>Paybill</span>
            </button>
            <button type="button" @click="method = 'stk'"
                    :class="method === 'stk' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200'"
                    class="px-5 py-3 rounded-lg font-semibold transition flex items-center space-x-2">
                <span>📲</span>
                <span>STK Push</span>
            </button>
        </div>

        <!-- Prompt if no method selected -->
        <div x-show="!method" class="text-center text-gray-500 italic py-4">
            Please select a payment method to proceed.
        </div>

        <!-- Paybill Option -->
        <div x-show="method === 'paybill'" x-transition class="space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="font-semibold text-green-700">Paybill: <span class="font-mono">123456</span></p>
                <p class="text-green-700">Account: <span class="font-mono">{{config('app.name')}}</span></p>
                <p class="text-green-700">Amount: KES <span class="font-mono">{{ number_format($order->total_amount, 2) }}</span></p>
            </div>

            <p class="text-gray-500 text-sm">Once you've completed the payment in your M-Pesa app, enter the transaction code below:</p>

            <form method="POST" action="{{ route('orders.payment.store', $order) }}" class="space-y-4">
                @csrf
                <input type="text" name="payment_ref" placeholder="M-Pesa Transaction Code"
                    class="w-full border rounded-lg p-3 focus:ring-green-500 focus:border-green-500" required>
                <button type="submit"
                        class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    Submit Payment Reference
                </button>
            </form>
        </div>

        <!-- STK Push Option -->
        <div x-show="method === 'stk'" x-transition class="space-y-4">
            <p class="text-gray-500 text-sm">Enter your M-Pesa number to receive an STK Push request for payment.</p>

            <form method="POST" action="{{ route('orders.payment.stk', $order) }}" class="space-y-4">
                @csrf
                <input type="text" name="phone" placeholder="07XXXXXXXX"
                       class="w-full border rounded-lg p-3 focus:ring-green-500 focus:border-green-500" required>
                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    Send STK Push
                </button>
            </form>
        </div>
    </div>
</div>
@endsection