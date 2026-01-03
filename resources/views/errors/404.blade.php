@extends('layouts.app')
@section('content')
<div class="flex flex-col items-center justify-center py-20">
    <!-- Big 404 -->
    <h1 class="text-6xl font-extrabold text-blue-600 mb-4">404</h1>

    <!-- Message -->
    <p class="text-lg text-gray-600 mb-6">
        Oops! The page you are looking for was not found.
    </p>

    <!-- Return Home Button -->
    <a href="{{ url('/') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow transition transform hover:scale-105">
        ⬅ Return Home
    </a>
</div>
@endsection