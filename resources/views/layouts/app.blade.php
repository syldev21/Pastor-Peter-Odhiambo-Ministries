<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">

        {{-- Top Navigation --}}
        <nav class="flex-shrink-0 bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 text-white shadow">
            @include('layouts.navigation')
        </nav>

        {{-- Page Heading --}}
        @isset($header)
            <header class="bg-yellow-400 shadow flex-shrink-0">
                <div class="max-w-7xl mx-auto py-4 px-6 flex justify-between items-center">
                    <div class="text-xl font-semibold text-gray-900">
                        {{ $header }}
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-800">{{ Auth::user()->name ?? 'Guest' }}</span>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Guest') }}" 
                             class="h-9 w-9 rounded-full border-2 border-blue-600 shadow" alt="User Avatar">
                    </div>
                </div>
            </header>
        @endisset

        {{-- Main Layout --}}
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <aside class="relative w-64 bg-gradient-to-b from-blue-700 to-blue-900 text-white flex-shrink-0 overflow-y-auto shadow-lg">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black opacity-40"></div>

                <!-- Content -->
                <div class="relative z-10 p-6">
                    <h2 class="text-lg font-bold text-yellow-300 mb-6 tracking-wide drop-shadow">
                        {{ config('app.name') }}
                    </h2>
                    <nav class="space-y-3 relative bg-gradient-to-r from-brandBlue via-indigo-800 to-brandPurple text-white py-20 px-6 rounded-2xl shadow-xl mb-10 border border-brandBlue">
                        <a href="{{ route('home') }}" class="flex items-center px-3 py-2 rounded hover:bg-blue-800 transition">
                            🏠 <span class="ml-2">Home</span>
                        </a>
                        <a href="{{ route('books.index') }}" class="flex items-center px-3 py-2 rounded hover:bg-blue-800 transition">
                            📚 <span class="ml-2">Books</span>
                        </a>
                        <a href="{{ route('cart.index') }}" class="flex items-center px-3 py-2 rounded hover:bg-blue-800 transition">
                            🛒 <span class="ml-2">Cart</span>
                        </a>
                        @auth
                            <a href="{{ route('orders.index') }}" class="flex items-center px-3 py-2 rounded hover:bg-blue-800 transition">
                                📦 <span class="ml-2">My Orders</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 rounded hover:bg-blue-800 transition">
                                👤 <span class="ml-2">Profile</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center px-3 py-2 rounded hover:bg-blue-800 transition">
                                🔑 <span class="ml-2">Login</span>
                            </a>
                            <a href="{{ route('register') }}" class="flex items-center px-3 py-2 rounded hover:bg-blue-800 transition">
                                📝 <span class="ml-2">Register</span>
                            </a>
                        @endauth
                    </nav>
                </div>
            </aside>

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="max-w-7xl mx-auto py-10 px-6">
                    @yield('content')
                </div>
            </main>
        </div>

        {{-- Footer --}}
        <footer class="relative bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 text-yellow-300 text-sm text-center py-4 shadow-inner">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black opacity-40"></div>

            <!-- Content -->
            <div class="relative z-10">
                <p class="drop-shadow">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <p>Designed & Developed by <span class="text-white font-semibold drop-shadow">CODE WAVE TECHNOLOGIES</span></p>
            </div>
        </footer>
    </div>
</body>
</html>
