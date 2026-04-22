<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — TABUAN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-gray-50" x-data="{ sidebarOpen: false }">

<div class="flex h-full">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-gray-100 fixed inset-y-0 z-30">
        {{-- Logo --}}
        <div class="flex items-center gap-2 px-6 py-5 border-b border-gray-100">
            <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 8C8 10 5.9 16.17 3.82 21c6.07-3.15 13.26-1.67 16.44-6C22 11 21 3 21 3c-1 2-4 4-4 5z"/>
                </svg>
            </div>
            <span class="text-xl font-bold text-primary-700 tracking-tight">TABUAN</span>
        </div>

        {{-- User Info --}}
        <div class="px-4 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" alt="">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @yield('sidebar-nav')
        </nav>

        {{-- Bottom --}}
        <div class="px-3 py-4 border-t border-gray-100">
            <a href="{{ route('marketplace') }}" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Marketplace
            </a>
            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left text-red-500 hover:text-red-600 hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-20 lg:hidden"></div>
    <aside x-show="sidebarOpen" x-transition:enter="transform transition-transform duration-300"
           x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transform transition-transform duration-300"
           x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
           class="lg:hidden flex flex-col w-64 bg-white fixed inset-y-0 left-0 z-30 shadow-xl">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <span class="text-xl font-bold text-primary-700">TABUAN</span>
            <button @click="sidebarOpen = false" class="p-1 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1">
            @yield('sidebar-nav')
        </nav>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
        {{-- Top Bar --}}
        <header class="bg-white border-b border-gray-100 px-4 sm:px-6 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title')</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('messages.index') }}" class="relative p-2 text-gray-500 hover:text-primary-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    @php $unread = auth()->user()->unreadMessagesCount(); @endphp
                    @if($unread > 0)
                        <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center">{{ $unread }}</span>
                    @endif
                </a>
                <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-lg object-cover" alt="">
            </div>
        </header>

        {{-- Flash --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-4 sm:mx-6 mt-4 flex items-center gap-3 bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 rounded-xl">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-4 sm:mx-6 mt-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
