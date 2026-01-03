@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6 shadow text-center">
            {{ session('success') }}
        </div>
    @endif

    <!-- HERO SECTION - HIGH CONTRAST -->
    <div class="relative bg-gradient-to-r from-blue-800 via-indigo-800 to-purple-900 text-white py-20 px-6 rounded-2xl shadow-xl mb-10 border border-blue-900">        <!-- Soft dark overlay -->
        <div class="absolute inset-0 bg-black opacity-50 rounded-2xl"></div>

        <!-- Content -->
        <div class="relative z-10 text-center backdrop-blur-sm">
            <h1 class="text-5xl font-extrabold drop-shadow-lg tracking-tight">
                Welcome to {{ config("app.name") }}
            </h1>
            <p class="mt-4 text-lg text-blue-200 max-w-2xl mx-auto leading-relaxed">
                Discover inspiring books and resources for your journey
            </p>
        </div>
        <!-- <x-hero-banner title="Welcome to {{ config('app.name') }}" subtitle="Discover inspiring books and resources for your journey" /> -->

    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($books as $book)
            <div class="border rounded-lg overflow-hidden shadow hover:shadow-xl transition transform hover:-translate-y-1 bg-white">
                <!-- Book Cover -->
                <img
                    src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/LOGO.png') }}" 
                    alt="{{ $book->title }}" 
                    class="w-full h-64 object-cover"
                />

                <div class="p-6">
                    <!-- Title -->
                    <h2 class="text-xl font-semibold text-gray-900 mb-2 truncate text-center">
                        {{ $book->title }}
                    </h2>

                    <!-- Price -->
                    <p class="text-lg font-bold text-green-600 mb-4 text-center">
                        KES {{ number_format($book->price, 2) }}
                    </p>

                    <!-- Add to Cart -->
                    <form action="{{ route('cart.add', $book) }}" method="POST" class="text-center">
                        @csrf
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition transform hover:scale-105 shadow">
                            ➕ 🛒 Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 col-span-full">No books available at the moment.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-10 flex justify-center">
        {{ $books->links() }}
    </div>

    <!-- View Cart Button (Bottom) -->
    <!-- <div class="mt-12 text-center">
        <a href="{{ route('cart.index') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition transform hover:scale-105 shadow">
            View Cart →
        </a>
        @if(session('cart_count') > 0)
            <span class="ml-2 text-sm text-gray-600">
                ({{ session('cart_count') }} items)
            </span>
        @endif
    </div> -->

    <!-- Sticky Cart Shortcut -->
    <div class="fixed bottom-6 right-6 z-50">
        <a href="{{ route('cart.index') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-full shadow-lg transition transform hover:scale-110 flex items-center space-x-2">
           <span>🛒</span>
           <span>View Cart →</span>
        </a>
    </div>
</div>
@endsection