@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <h1 class="text-4xl font-bold mb-8 text-center text-gray-800">
        Welcome to Gospel Grove
    </h1>

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
</div>
@endsection