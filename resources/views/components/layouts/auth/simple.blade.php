<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-200 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 text-gray-800 dark:text-gray-200 antialiased">

    <div class="flex min-h-svh flex-col items-center justify-center p-6 md:p-10">

        <div class="w-full max-w-md rounded-2xl bg-white/95 dark:bg-gray-800/90 shadow-2xl 
                    backdrop-blur-xl border border-gray-100/50 dark:border-gray-700/40
                    p-10 space-y-8 transition-all duration-300">

            <!-- Logo + Branding -->
            <a href="{{ route('home') }}" 
                class="flex flex-col items-center space-y-4 mb-3 hover:opacity-90 transition">

                    {{-- Bigger Logo --}}
                    <img src="{{ asset(config('app.logo_path')) }}"
                        alt="Logo"
                        class="h-20 w-auto drop-shadow-xl" />

                    {{-- Project name + tagline --}}
                    <div class="leading-tight text-center">
                        <span class="font-extrabold text-2xl tracking-wide 
                                    text-gray-900 dark:text-yellow-300">
                            {{ config('app.name') }}
                        </span>

                        <p class="text-base text-gray-600 dark:text-yellow-200/80 mt-2">
                            {{ config('app.tagline') }}
                        </p>
                    </div>
                </a>

            <!-- Divider Line -->
            <div class="border-t border-gray-200 dark:border-gray-700/60"></div>

            <!-- Page Content -->
            <div class="flex flex-col gap-6 animate-fadeIn">
                {{ $slot }}
            </div>

        </div>
    </div>

    @fluxScripts
</body>
</html>