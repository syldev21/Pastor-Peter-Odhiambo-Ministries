@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto text-center py-16 px-6">

    <!-- Success Icon -->
    <div class="mx-auto mb-6 w-16 h-16 flex items-center justify-center rounded-full bg-green-100">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5 13l4 4L19 7" />
        </svg>
    </div>

    <!-- Heading -->
    <h2 class="text-3xl font-bold text-gray-800 mb-4">Thank you for your order!</h2>
    <p class="text-gray-600 mb-8">
        We’ll be in touch shortly with your delivery details. Your support helps {{ config('app.name') }} spread the Word.
    </p>

    <!-- STK Push Confirmation -->
    @if(session('success'))
        <div class="mb-6 px-6 py-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
            <p class="font-semibold">{{ session('success') }}</p>

            @if(session('checkout_id'))
                <p class="text-sm mt-2">
                    <strong>Transaction Reference:</strong> {{ session('checkout_id') }}
                </p>
            @endif
        </div>
    @endif

    <!-- CTA for Guests -->
    @if(!auth()->check())
        <div class="mt-8 p-6 bg-gray-50 border rounded-lg shadow-sm text-gray-700">
            <p class="mb-3 font-semibold text-lg">Want to track your order and save your details?</p>
            <a href="{{ route('register') }}"
               class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition">
                Create an account →
            </a>
        </div>
    @endif

    <!-- Back to Shop -->
    <div class="mt-10">
        <a href="{{ route('books.index') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
            Continue Shopping
        </a>
    </div>
</div>
@endsection