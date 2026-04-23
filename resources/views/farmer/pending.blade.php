@extends('layouts.app')
@section('title', 'Account Under Review — TABUAN')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg text-center">

        @if($user->isPending())
            @php $hasDocuments = $user->id_document || $user->selfie_photo; @endphp

            @if(!$hasDocuments)
            {{-- Missing documents — show upload form --}}
            <div class="card p-8 text-left">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">📋</div>
                    <h1 class="text-2xl font-bold text-gray-900">Complete Your Application</h1>
                    <p class="text-gray-500 mt-2 text-sm">Upload your verification documents to submit your seller application for review.</p>
                </div>

                @if(session('success'))
                    <div class="mb-4 px-4 py-3 bg-primary-50 border border-primary-200 text-primary-700 rounded-xl text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
                @endif

                <form action="{{ route('farmer.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="label">ID Type</label>
                        <select name="id_type" class="input">
                            <option value="">Select ID type</option>
                            @foreach(["PhilSys / National ID","Driver's License","Passport","SSS ID","GSIS ID","Voter's ID","Postal ID","PRC ID","Other"] as $idType)
                                <option value="{{ $idType }}" {{ old('id_type') === $idType ? 'selected' : '' }}>{{ $idType }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label">Government ID <span class="text-gray-400 font-normal text-xs">(front, clear photo)</span></label>
                        <input type="file" name="id_document" accept="image/*,.pdf" class="input py-2 @error('id_document') border-red-400 @enderror">
                        @error('id_document') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">Selfie with ID <span class="text-gray-400 font-normal text-xs">(hold your ID next to your face)</span></label>
                        <input type="file" name="selfie_photo" accept="image/*" class="input py-2 @error('selfie_photo') border-red-400 @enderror">
                        @error('selfie_photo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">Farm / Business Document <span class="text-gray-400 font-normal text-xs">(optional — permit, certificate, etc.)</span></label>
                        <input type="file" name="farm_document" accept="image/*,.pdf" class="input py-2 @error('farm_document') border-red-400 @enderror">
                        @error('farm_document') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full py-3">Submit Documents</button>
                </form>

                <div class="mt-4 flex justify-center">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-gray-400 hover:text-gray-600">Sign out — continue later</button>
                    </form>
                </div>
            </div>

            @else
            {{-- Documents submitted — waiting for review --}}
            <div class="card p-10">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">⏳</div>
                <h1 class="text-2xl font-bold text-gray-900 mb-3">Application Under Review</h1>
                <p class="text-gray-500 leading-relaxed mb-6">
                    Your documents have been submitted and are being reviewed by our team.
                    We will notify you once your application is approved.
                </p>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-left mb-6 space-y-2 text-sm">
                    <p class="font-semibold text-amber-800">📋 Documents submitted:</p>
                    <ul class="text-amber-700 space-y-1 ml-4">
                        <li>{{ $user->id_document ? '✅' : '⬜' }} Government ID</li>
                        <li>{{ $user->selfie_photo ? '✅' : '⬜' }} Selfie with ID</li>
                        <li>{{ $user->farm_document ? '✅' : '⬜' }} Farm document (optional)</li>
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
            @endif

        @elseif($user->isRejected())
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

        @elseif($user->isSuspended())
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
