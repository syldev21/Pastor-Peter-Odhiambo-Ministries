@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-10">
        {{-- Page Title --}}
        <h1 class="text-3xl font-bold text-gray-800 mb-8">
            Order #{{ $order->id }}
        </h1>

        {{-- Order Meta --}}
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">
                        Placed on {{ $order->created_at->format('M d, Y') }}
                    </p>
                </div>
                <div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        @if($order->status === 'payment_initiated') bg-yellow-100 text-yellow-800 
                        @elseif($order->status === 'completed') bg-green-100 text-green-800 
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Delivery Info --}}
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Delivery Information</h2>
            <ul class="text-sm text-gray-600 space-y-2">
                <li><strong>Name:</strong> {{ $order->delivery_name }}</li>
                <li><strong>Phone:</strong> {{ $order->delivery_phone }}</li>
                <li><strong>Delivery Location:</strong> {{ $order->delivery_address }}</li>
            </ul>
        </div>

        {{-- Items --}}
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Items</h2>
            @forelse($order->items as $item)
                <div class="flex justify-between items-center border-b pb-3 mb-3">
                    <div>
                        <p class="text-gray-800 font-medium">{{ $item->book->title ?? 'Unknown Book' }}</p>
                        <p class="text-sm text-gray-600">Author: {{ $item->book->author ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Price</p>
                        <p class="text-gray-800 font-semibold">
                            KSh {{ number_format((float)($item->book->price ?? 0) * (int)$item->quantity, 2) }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No items found for this order.</p>
            @endforelse
        </div>

        {{-- Total --}}
        <div class="bg-white shadow rounded-lg p-6 text-right">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Amount</h2>
            <p class="text-2xl font-bold text-gray-800">
                KSh {{ number_format($order->total_amount) }}
            </p>
        </div>
    </div>
@endsection