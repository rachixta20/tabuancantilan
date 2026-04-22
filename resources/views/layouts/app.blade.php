<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TABUAN') — Farm Fresh Marketplace</title>
    <script>
        (function () {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full flex flex-col dark:bg-gray-950" x-data="{ mobileMenu: false }">

{{-- NAVBAR --}}
<nav class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 sticky top-0 z-50 shadow-sm dark:shadow-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17 8C8 10 5.9 16.17 3.82 21c6.07-3.15 13.26-1.67 16.44-6C22 11 21 3 21 3c-1 2-4 4-4 5z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-primary-700 dark:text-primary-400 tracking-tight">TABUAN</span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}" class="nav-link">Home</a>
                <a href="{{ route('marketplace') }}" class="nav-link">Marketplace</a>
                <a href="{{ route('map') }}" class="nav-link flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Seller Map
                </a>
                @auth
                    @if(auth()->user()->isFarmer())
                        <a href="{{ route('farmer.dashboard') }}" class="nav-link">Dashboard</a>
                        <a href="{{ route('farmer.products') }}" class="nav-link">My Products</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a>
                    @else
                        <a href="{{ route('buyer.dashboard') }}" class="nav-link">Dashboard</a>
                    @endif
                    <a href="{{ route('messages.index') }}" class="nav-link relative">
                        Messages
                        @php $unread = auth()->user()->unreadMessagesCount(); @endphp
                        @if($unread > 0)
                            <span class="absolute -top-1.5 -right-2.5 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center">{{ $unread }}</span>
                        @endif
                    </a>
                @endauth
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-2">

                {{-- Dark mode toggle --}}
                <button @click="$store.theme.toggle()"
                        class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-800 transition-colors"
                        :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'">
                    <svg x-show="!$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                @auth
                    {{-- Cart --}}
                    @if(!auth()->user()->isFarmer() && !auth()->user()->isAdmin())
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @php $cartCount = auth()->user()->cartCount(); @endphp
                            @if($cartCount > 0)
                                <span class="absolute top-0 right-0 w-5 h-5 bg-primary-600 text-white text-[10px] rounded-full flex items-center justify-center font-bold">{{ $cartCount }}</span>
                            @endif
                        </a>
                    @endif

                    {{-- User Menu --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-lg object-cover" alt="">
                            <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                             class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 mb-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ auth()->user()->role }}</p>
                            </div>
                            @if(auth()->user()->isFarmer())
                                <a href="{{ route('farmer.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Dashboard</a>
                                <a href="{{ route('farmer.products') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">My Products</a>
                                <a href="{{ route('farmer.orders') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Orders</a>
                            @elseif(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Admin Panel</a>
                            @else
                                <a href="{{ route('buyer.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Dashboard</a>
                                <a href="{{ route('buyer.orders') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">My Orders</a>
                                <a href="{{ route('buyer.wishlist') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Wishlist</a>
                            @endif
                            <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary btn-sm text-sm px-4 py-2">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary btn-sm text-sm px-4 py-2">Register</a>
                @endauth

                {{-- Mobile menu btn --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-gray-600 dark:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenu" x-transition class="md:hidden border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 px-4 py-3 space-y-1">
        <a href="{{ route('home') }}" class="block py-2 text-sm text-gray-700 dark:text-gray-300">Home</a>
        <a href="{{ route('marketplace') }}" class="block py-2 text-sm text-gray-700 dark:text-gray-300">Marketplace</a>
        <a href="{{ route('map') }}" class="block py-2 text-sm text-gray-700 dark:text-gray-300">Seller Map</a>
        @auth
            <a href="{{ route('messages.index') }}" class="block py-2 text-sm text-gray-700 dark:text-gray-300">Messages</a>
        @endauth
    </div>
</nav>

{{-- Flash Messages --}}
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-20 right-4 z-50 flex items-center gap-3 bg-primary-600 text-white px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-20 right-4 z-50 flex items-center gap-3 bg-red-600 text-white px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

{{-- Main Content --}}
<main class="flex-1">
    @yield('content')
</main>

{{-- FOOTER --}}
<footer class="bg-primary-950 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17 8C8 10 5.9 16.17 3.82 21c6.07-3.15 13.26-1.67 16.44-6C22 11 21 3 21 3c-1 2-4 4-4 5z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight">TABUAN</span>
                </div>
                <p class="text-primary-200 text-sm leading-relaxed max-w-xs">
                    Connecting local farmers and entrepreneurs directly with buyers. Fresher products, fairer prices, stronger communities.
                </p>
            </div>
            <div>
                <h4 class="font-semibold text-sm uppercase tracking-wider text-primary-300 mb-4">Marketplace</h4>
                <ul class="space-y-2 text-sm text-primary-200">
                    <li><a href="{{ route('marketplace') }}" class="hover:text-white transition-colors">Browse Products</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Sell on TABUAN</a></li>
                    <li><a href="{{ route('marketplace') }}" class="hover:text-white transition-colors">Organic Produce</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-sm uppercase tracking-wider text-primary-300 mb-4">Support</h4>
                <ul class="space-y-2 text-sm text-primary-200">
                    <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Contact Us</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-primary-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-primary-400 text-sm">© {{ date('Y') }} TABUAN. All rights reserved.</p>
            <p class="text-primary-400 text-sm">Empowering local farmers 🌱</p>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
