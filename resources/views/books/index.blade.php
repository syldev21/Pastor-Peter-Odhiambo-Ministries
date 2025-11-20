@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <h1 class="text-4xl font-bold mb-8 text-center text-gray-800">
        Welcome to {{config("app.name")}}
    </h1>

    <div class="flex justify-end mb-4">
    <a href="{{ route('cart.index') }}"
       class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h13a1 1 0 001-1v-1M7 13h10M9 21a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z" />
        </svg>
        View Cart
    </a>
</div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($books as $book)
            <div class="border rounded-lg p-6 shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ $book->title }}
                </h2>

                <p class="text-lg font-medium text-green-700 mb-4">
                    KES {{ number_format($book->price, 2) }}
                </p>

                <form method="POST" action="{{ route('cart.add', $book) }}">
                    @csrf
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition">
                        Add to Cart
                    </button>
                </form>
            </div>
        @endforeach
    </div>
    <div class="mt-10 text-center">
        <a href="{{ route('cart.index') }}"
        class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
            View Cart →
        </a>
    </div>
    @if(session('cart_count') > 0)
    <span class="ml-2 text-sm text-gray-600">
        ({{ session('cart_count') }} items)
    </span>
    @endif
</div>
@endsection