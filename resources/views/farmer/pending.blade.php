@extends('layouts.app')
@section('title', 'Account Under Review — TABUAN')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg text-center">

        @if($user->isPending())
            @php $hasDocuments = $user->id_document || $user->selfie_photo; @endphp

            @if(!$hasDocuments)
            {{-- Missing documents — show upload form --}}
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-left">
                <div class="text-center mb-7">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">📋</div>
                    <h1 class="text-2xl font-bold text-gray-900">Complete Your Application</h1>
                    <p class="text-gray-500 mt-2 text-sm">Upload your verification documents so our team can review your seller application.</p>
                </div>

                @if(session('success'))
                    <div class="mb-5 px-4 py-3 bg-primary-50 border border-primary-200 text-primary-700 rounded-xl text-sm font-medium">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
                @endif

                <form action="{{ route('farmer.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    {{-- ID Type --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ID Type</label>
                        <select name="id_type" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="">Select your ID type</option>
                            @foreach(["PhilSys / National ID","Driver's License","Passport","SSS ID","GSIS ID","Voter's ID","Postal ID","PRC ID","Other"] as $idType)
                                <option value="{{ $idType }}" {{ old('id_type') === $idType ? 'selected' : '' }}>{{ $idType }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Government ID --}}
                    <div x-data="{ name: '' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Government ID
                            <span class="text-gray-400 font-normal ml-1">(front side, clearly visible)</span>
                        </label>
                        <label for="id_doc_input"
                               class="flex flex-col items-center justify-center gap-2 w-full border-2 border-dashed border-gray-300 rounded-xl p-5 cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-colors @error('id_document') border-red-400 bg-red-50 @enderror">
                            <span class="text-3xl">🪪</span>
                            <span class="text-sm font-medium text-gray-600" x-text="name || 'Click to choose file'"></span>
                            <span class="text-xs text-gray-400">JPG, PNG, PDF up to 4MB</span>
                            <input type="file" id="id_doc_input" name="id_document" accept="image/*,.pdf" class="hidden"
                                   @change="name = $event.target.files[0]?.name || ''">
                        </label>
                        @error('id_document') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Selfie --}}
                    <div x-data="{ name: '', preview: null }">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Selfie Holding Your ID
                            <span class="text-gray-400 font-normal ml-1">(face + ID must both be visible)</span>
                        </label>
                        <label for="selfie_input"
                               class="flex flex-col items-center justify-center gap-2 w-full border-2 border-dashed border-amber-300 rounded-xl p-5 cursor-pointer hover:border-amber-400 hover:bg-amber-50 transition-colors @error('selfie_photo') border-red-400 bg-red-50 @enderror">
                            <template x-if="preview">
                                <img :src="preview" class="h-24 object-contain rounded-lg">
                            </template>
                            <template x-if="!preview">
                                <span class="text-3xl">🤳</span>
                            </template>
                            <span class="text-sm font-medium text-gray-600" x-text="name || 'Click to choose photo'"></span>
                            <span class="text-xs text-gray-400">JPG or PNG up to 4MB</span>
                            <input type="file" id="selfie_input" name="selfie_photo" accept="image/*" class="hidden"
                                   @change="name = $event.target.files[0]?.name || ''; preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        </label>
                        @error('selfie_photo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Farm Document --}}
                    <div x-data="{ name: '' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Farm / Business Document
                            <span class="text-gray-400 font-normal ml-1">(optional — permit, certificate, clearance)</span>
                        </label>
                        <label for="farm_doc_input"
                               class="flex flex-col items-center justify-center gap-2 w-full border-2 border-dashed border-gray-200 rounded-xl p-5 cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-colors">
                            <span class="text-3xl">📄</span>
                            <span class="text-sm font-medium text-gray-600" x-text="name || 'Click to choose file (optional)'"></span>
                            <span class="text-xs text-gray-400">JPG, PNG, PDF up to 4MB</span>
                            <input type="file" id="farm_doc_input" name="farm_document" accept="image/*,.pdf" class="hidden"
                                   @change="name = $event.target.files[0]?.name || ''">
                        </label>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3 text-base font-semibold mt-2">
                        Submit Application Documents
                    </button>
                </form>

                <div class="mt-5 text-center border-t border-gray-100 pt-5">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                            Sign out and continue later →
                        </button>
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
