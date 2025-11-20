<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name')}}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">

        {{-- Top Navigation --}}
        @include('layouts.navigation')

        {{-- Page Heading --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div class="text-xl font-semibold text-gray-800">
                        {{ $header }}
                    </div>
                    {{-- Optional user dropdown --}}
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ Auth::user()->name ?? '' }}</span>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Guest') }}" 
                             class="h-8 w-8 rounded-full" alt="User Avatar">
                    </div>
                </div>
            </header>
        @endisset

        {{-- Page Content --}}
        <main class="flex-1">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-white shadow mt-auto">
            <div class="max-w-7xl mx-auto py-4 px-6 text-center text-sm text-gray-500">
                &copy; {{ config('app.name') }}. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>