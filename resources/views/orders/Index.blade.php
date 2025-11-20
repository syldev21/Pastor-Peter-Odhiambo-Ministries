@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">My Orders</h1>

        {{-- Success message --}}
        @if(session('success'))
            <div class="mb-6 bg-green-100 text-green-800 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Empty state --}}
        @if($orders->isEmpty())
            <div class="text-center bg-white shadow rounded-lg p-8">
                <!-- <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18M9 3v18m6-18v18M3 9h18" />
                </svg> -->
                <p class="mt-4 text-gray-600">You haven’t placed any orders yet.</p>
                
                <a href="{{ route('books.index') }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
                        <- Browse Books
                </a>

            </div>
        @else
            {{-- Orders grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($orders as $order)
                    <x-order-card :order="$order" />
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection