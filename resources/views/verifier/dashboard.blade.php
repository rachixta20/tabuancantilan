@extends('layouts.dashboard')
@section('title', 'Verifier Dashboard')
@section('page-title', 'Pending Approvals')

@section('sidebar-nav')
    @include('verifier._sidebar')
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Pending Review</p>
        <p class="text-3xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Approved Today</p>
        <p class="text-3xl font-bold text-primary-600">{{ $stats['approved_today'] }}</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-50">
        <h3 class="font-semibold text-gray-800">Accounts Awaiting Approval</h3>
    </div>

    @forelse($pending as $u)
        <div class="px-5 py-5 border-b border-gray-50 last:border-0">
            <div class="flex items-start gap-4">
                <img src="{{ $u->avatar_url }}" class="w-12 h-12 rounded-xl object-cover shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-gray-800">{{ $u->name }}</p>
                        <span class="badge bg-gray-100 text-gray-600 capitalize text-xs">{{ $u->role }}</span>
                        @if($u->farm_name)
                            <span class="text-xs text-gray-500">· {{ $u->farm_name }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $u->email }} @if($u->phone) · {{ $u->phone }} @endif</p>
                    @if($u->location)
                        <p class="text-xs text-gray-400 mt-0.5">📍 {{ $u->location }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-0.5">Registered {{ $u->created_at->diffForHumans() }}</p>

                    {{-- Documents --}}
                    @if($u->id_document || $u->selfie_photo || $u->farm_document)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($u->id_document)
                                <a href="{{ asset('storage/' . $u->id_document) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
                                    View ID
                                </a>
                            @endif
                            @if($u->selfie_photo)
                                <a href="{{ asset('storage/' . $u->selfie_photo) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs bg-purple-50 text-purple-700 px-3 py-1.5 rounded-lg hover:bg-purple-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    View Selfie
                                </a>
                            @endif
                            @if($u->farm_document)
                                <a href="{{ asset('storage/' . $u->farm_document) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs bg-green-50 text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Farm Document
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="mt-4 flex items-start gap-3 flex-wrap">
                        <form action="{{ route('verifier.users.approve', $u) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-primary text-sm py-1.5 px-4">Approve</button>
                        </form>

                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="btn-danger text-sm py-1.5 px-4">Reject</button>
                            <div x-show="open" x-cloak class="mt-2 flex flex-col gap-2 max-w-sm">
                                <form action="{{ route('verifier.users.reject', $u) }}" method="POST">
                                    @csrf
                                    <textarea name="rejection_reason" rows="2" required
                                              class="input text-sm resize-none w-full"
                                              placeholder="Reason for rejection..."></textarea>
                                    <button type="submit" class="btn-danger text-sm py-1.5 px-4 mt-2 w-full">Confirm Reject</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="px-5 py-16 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="font-medium">No pending accounts</p>
            <p class="text-sm mt-1">All registrations have been reviewed.</p>
        </div>
    @endforelse

    @if($pending->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $pending->links() }}</div>
    @endif
</div>
@endsection
