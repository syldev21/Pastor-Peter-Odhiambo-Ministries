@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto py-12 px-6">

        {{-- Optional login prompt for guests --}}
        @if(!auth()->check())
            <div class="mb-8 p-5 bg-blue-50 border-l-4 border-blue-400 rounded shadow-sm text-blue-900">
                <p class="mb-2 font-semibold text-lg">Already have an account?</p>
                <a href="{{ route('login') }}"
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md shadow transition">
                    Log in to pre-fill your details →
                </a>
            </div>
        @endif

        {{-- Flash message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded shadow">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="space-y-8">
            @csrf

            {{-- Delivery Details --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Delivery Details</h2>

                <div class="space-y-4">
                    <input type="text" name="delivery_name"
                           value="{{ old('delivery_name', auth()->user()->name ?? '') }}"
                           placeholder="Full Name" required
                           class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">

                    <input type="text" name="delivery_phone"
                           value="{{ old('delivery_phone', auth()->user()->phone ?? '') }}"
                           placeholder="Phone Number" required
                           class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">

                    <input type="text" name="delivery_address"
                           value="{{ old('delivery_address', auth()->user()->address ?? '') }}"
                           placeholder="Delivery Location" required
                           class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Order Summary</h2>

                <div class="space-y-3">
                    @foreach($cartItems as $id => $item)
                        <div class="flex justify-between items-center border p-4 rounded-lg shadow-sm hover:shadow-md transition">
                            <div>
                                {{ auth()->check() ? $item->book->title : $item['title'] }}
                                <span class="text-gray-500 text-sm">x{{ auth()->check() ? $item->quantity : $item['quantity'] }}</span>
                            </div>
                            <div class="font-semibold text-gray-800">
                                KES {{ number_format(auth()->check()
                            ? $item->book->price * $item->quantity
                            : $item['price'] * $item['quantity'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-right text-lg font-bold text-gray-900">
                    Total: KES {{ number_format($total, 2) }}
                </div>
            </div>

            {{-- Confirm Order Button --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 mt-6 text-white px-8 py-3 rounded-lg shadow hover:bg-green-700 transition font-semibold">
                    Confirm Order
                </button>
        </div>
        </form>
    </div>
@endsection
