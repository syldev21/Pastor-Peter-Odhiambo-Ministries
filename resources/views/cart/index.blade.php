@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto py-10 px-4">

        {{-- Flash message --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Page title --}}
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Your Cart</h2>

        {{-- Check if there are any cart items --}}
        @if($cartItems->count() > 0)
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100 text-left text-gray-700 font-semibold">
                    <tr>
                        <th scope="col" class="p-4">Book</th>
                        <th scope="col" class="p-4 w-32">Qty</th>
                        <th scope="col" class="p-4">Price</th>
                        <th scope="col" class="p-4 w-32">Actions</th>
                    </tr>
                    </thead>

                    <tbody class="text-gray-800">
                    @foreach($cartItems as $item)
                        <tr class="border-t hover:bg-gray-50 transition">
                            {{-- Book title --}}
                            <td class="p-4">
                                {{ auth()->check() ? $item->book->title : $item->title }}
                            </td>

                            {{-- Quantity update form --}}
                            <td class="p-4">
                                <form method="POST"
                                      action="{{ auth()->check()
                                          ? route('cart.update', $item->id)
                                          : route('cart.update.session', $item->book_id) }}"
                                      class="flex items-center gap-2">
                                    @csrf
                                    @if(auth()->check()) @method('PATCH') @endif
                                    <input type="number" name="quantity"
                                           value="{{ $item->quantity }}" min="1"
                                           class="w-16 border rounded p-1 text-center"
                                           aria-label="Quantity for {{ auth()->check() ? $item->book->title : $item->title }}">
                                    <button type="submit"
                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                        Update
                                    </button>
                                </form>
                            </td>

                            {{-- Price column --}}
                            <td class="p-4 font-medium">
                                KES {{ number_format(
                                    auth()->check()
                                        ? $item->book->price * $item->quantity
                                        : $item->price * $item->quantity,
                                    2
                                ) }}
                            </td>

                            {{-- Remove item --}}
                            <td class="p-4">
                                <form method="POST"
                                      action="{{ auth()->check()
                                          ? route('cart.destroy', $item->id)
                                          : route('cart.remove.session', $item->book_id) }}">
                                    @csrf
                                    @if(auth()->check()) @method('DELETE') @endif
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                            aria-label="Remove {{ auth()->check() ? $item->book->title : $item->title }} from cart">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Cart summary and checkout --}}
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h3 class="text-2xl font-bold text-gray-800">
                    Total: KES {{ number_format($total, 2) }}
                </h3>

                <a href="{{ route('checkout.show') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    Proceed to Checkout
                </a>
            </div>

        @else
            {{-- Empty cart --}}
            <p class="text-gray-600 text-lg">Your cart is empty.</p>
            <a href="{{ route('home') }}"
               class="inline-block mt-4 text-blue-600 hover:underline">
                Continue Shopping →
            </a>
        @endif

    </div>
@endsection
