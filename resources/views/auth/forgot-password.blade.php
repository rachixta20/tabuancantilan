@extends('layouts.app')
@section('title', 'Forgot Password — TABUAN')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-green-100 py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Forgot Password?</h1>
            <p class="text-gray-500 mt-2">Enter your email and we'll send a reset link.</p>
        </div>

        <div class="card p-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input-field w-full @error('email') border-red-400 @enderror"
                           placeholder="you@example.com">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" :disabled="loading" class="btn-primary w-full py-3" x-text="loading ? 'Sending...' : 'Send Reset Link'"></button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Remembered it? <a href="{{ route('login') }}" class="text-primary-600 font-medium hover:underline">Back to login</a>
            </p>
        </div>
    </div>
</div>
@endsection
