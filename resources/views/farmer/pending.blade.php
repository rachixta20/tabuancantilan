@extends('layouts.app')
@section('title', 'Account Under Review — TABUAN')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg text-center">

        @if($user->account_status === 'pending')
            <div class="card p-10">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">⏳</div>
                <h1 class="text-2xl font-bold text-gray-900 mb-3">Application Under Review</h1>
                <p class="text-gray-500 leading-relaxed mb-6">
                    Thank you for registering as a seller on TABUAN! Your application is currently being reviewed by our admin team.
                    We will verify your submitted documents and notify you once approved.
                </p>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-left mb-6 space-y-2 text-sm">
                    <p class="font-semibold text-amber-800">📋 What we're checking:</p>
                    <ul class="text-amber-700 space-y-1 ml-4">
                        <li>✓ Valid government ID authenticity</li>
                        <li>✓ Selfie matches ID photo</li>
                        <li>✓ Farm/business legitimacy</li>
                        <li>✓ Location within Cantilan, Surigao del Sur</li>
                    </ul>
                </div>
                <p class="text-xs text-gray-400 mb-6">Typical review time: <strong>1–2 business days</strong></p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('marketplace') }}" class="btn-outline">Browse Marketplace</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary w-full sm:w-auto">Sign Out</button>
                    </form>
                </div>
            </div>

        @elseif($user->account_status === 'rejected')
            <div class="card p-10">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">❌</div>
                <h1 class="text-2xl font-bold text-gray-900 mb-3">Application Not Approved</h1>
                <p class="text-gray-500 mb-4">Unfortunately, your seller application was not approved.</p>
                @if($user->rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-left mb-6">
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wide mb-1">Reason</p>
                        <p class="text-sm text-red-800">{{ $user->rejection_reason }}</p>
                    </div>
                @endif
                <p class="text-sm text-gray-500 mb-6">
                    You may contact our support team or re-register with the correct documents.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('register') }}" class="btn-primary">Re-apply</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary w-full sm:w-auto">Sign Out</button>
                    </form>
                </div>
            </div>

        @elseif($user->account_status === 'suspended')
            <div class="card p-10">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">🚫</div>
                <h1 class="text-2xl font-bold text-gray-900 mb-3">Account Suspended</h1>
                <p class="text-gray-500 mb-4">Your account has been suspended by the admin.</p>
                @if($user->admin_notes)
                    <div class="bg-gray-100 border border-gray-200 rounded-xl p-4 text-left mb-6">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Reason</p>
                        <p class="text-sm text-gray-700">{{ $user->admin_notes }}</p>
                    </div>
                @endif
                <p class="text-sm text-gray-500 mb-6">Please contact our support team to appeal this decision.</p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-secondary">Sign Out</button>
                </form>
            </div>
        @endif

    </div>
</div>
@endsection
