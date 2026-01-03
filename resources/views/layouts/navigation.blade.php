<nav x-data="{ open: false }" class="relative bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 text-white shadow">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black opacity-30"></div>

    <!-- Primary Navigation Menu -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        {{-- Logo image --}}
                        <img src="{{ asset(config('app.logo_path')) }}"
                            alt="Logo"
                            class="h-14 w-auto drop-shadow-lg" />

                        {{-- Project name + tagline --}}
                        <div class="flex flex-col leading-tight">
                            <span class="font-semibold text-yellow-300 drop-shadow text-lg">
                                {{ config('app.name') }}
                            </span>
                            <span class="text-sm text-yellow-200">
                                {{ config('app.tagline') }}
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('books.index')" :active="request()->routeIs('books.index')" class="text-white hover:text-yellow-300 drop-shadow">
                        {{ __('Books') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')" class="text-white hover:text-yellow-300 drop-shadow">
                            {{ __('My Orders') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-200 bg-transparent hover:text-yellow-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4 text-yellow-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-yellow-200 hover:text-yellow-300">Login</a>
                        <a href="{{ route('register') }}" class="text-sm text-yellow-200 hover:text-yellow-300">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-yellow-200 hover:text-yellow-300 hover:bg-blue-800 focus:outline-none focus:bg-blue-800 focus:text-yellow-300 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden relative bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 text-yellow-200">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black opacity-30"></div>

        <!-- Content -->
        <div class="relative z-10 pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('books.index')" :active="request()->routeIs('books.index')" class="hover:text-yellow-300">
                {{ __('Books') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')" class="hover:text-yellow-300">
                    {{ __('My Orders') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="relative z-10 pt-4 pb-1 border-t border-blue-700">
                <div class="px-4">
                    <div class="font-medium text-base text-yellow-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-yellow-300">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')" class="hover:text-yellow-300">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" class="hover:text-yellow-300"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="relative z-10 pt-4 pb-1 border-t border-blue-700">
                <div class="mt-3 space-y-1 px-4">
                    <x-responsive-nav-link :href="route('login')" class="hover:text-yellow-300">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')" class="hover:text-yellow-300">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>