<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gospel Grove</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <header>
        <!-- Navbar -->
    </header>

    <a href="{{ route('cart.index') }}" class="relative">
    🛒 Cart
        @php
            $cartCount = auth()->check()
                ? auth()->user()->cart()->count()
                : count(session('cart', []));
        @endphp
        <span class="ml-1 text-sm font-bold text-red-600">({{ $cartCount }})</span>
    </a>
<main class="p-6">
        @yield('content')
    </main>

    <footer class="text-center text-sm text-gray-500 mt-12">
        &copy; {{ date('Y') }} Gospel Grove
    </footer>
</body>
</html>
