<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gospel Grove</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-green-700">
                Gospel Grove
            </a>

            <nav class="space-x-6">
                <a href="{{ route('books.index') }}" class="text-gray-700 hover:text-green-700">Books</a>
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center text-gray-700 hover:text-green-700">
                    🛒 Cart
                    @php
                        $cartCount = auth()->check()
                            ? auth()->user()->cart()->count()
                            : count(session('cart', []));
                    @endphp
                    @if($cartCount > 0)
                        <span class="ml-2 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-green-700">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-green-700">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-700">Login</a>
                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-green-700">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-6 py-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t text-center text-sm text-gray-500 py-6">
        &copy; {{ date('Y') }} Gospel Grove · Built with ❤️ for ministry and community
    </footer>
</body>
</html>