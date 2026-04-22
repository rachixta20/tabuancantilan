@extends('layouts.dashboard')
@section('title', 'Review User — Admin')
@section('page-title', 'User Review')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600 mb-5">
        ← Back to Users
    </a>

    {{-- Status Banner --}}
    @php
        $banners = [
            'pending'   => ['bg-amber-50 border-amber-200 text-amber-800',  '⏳ Awaiting Review'],
            'approved'  => ['bg-primary-50 border-primary-200 text-primary-800', '✅ Approved Seller'],
            'rejected'  => ['bg-red-50 border-red-200 text-red-800',         '❌ Application Rejected'],
            'suspended' => ['bg-gray-100 border-gray-300 text-gray-700',     '🚫 Account Suspended'],
        ];
        $banner = $banners[$user->account_status ?? 'approved'];
    @endphp
    <div class="border rounded-xl px-5 py-3 mb-6 flex items-center justify-between {{ $banner[0] }}">
        <span class="font-semibold">{{ $banner[1] }}</span>
        @if($user->verified_at)
            <span class="text-xs">Approved {{ $user->verified_at->format('M d, Y') }}</span>
        @endif
        @if($user->rejection_reason)
            <span class="text-xs">Reason: {{ $user->rejection_reason }}</span>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        {{-- Profile --}}
        <div class="card p-5 text-center">
            <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-2xl object-cover mx-auto mb-3" alt="">
            <p class="font-bold text-gray-900">{{ $user->name }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</p>
            <p class="text-xs text-gray-400">{{ $user->phone ?? 'No phone' }}</p>
            <span class="badge bg-primary-100 text-primary-700 mt-2">{{ ucfirst($user->role) }}</span>
        </div>

        {{-- Farm Info --}}
        <div class="card p-5 md:col-span-2">
            <h4 class="font-semibold text-gray-800 mb-3">Farm Information</h4>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-gray-400 text-xs">Farm Name</p>
                    <p class="font-medium text-gray-800">{{ $user->farm_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Location</p>
                    <p class="font-medium text-gray-800">{{ $user->location ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Store Address</p>
                    <p class="font-medium text-gray-800">
                        @if($user->barangay)
                            {{ collect([$user->street, $user->purok ? 'Purok '.$user->purok : null, 'Brgy. '.$user->barangay])->filter()->implode(', ') }}
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">ID Type Submitted</p>
                    <p class="font-medium text-gray-800">{{ $user->id_type ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Registered</p>
                    <p class="font-medium text-gray-800">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
                @if($user->bio)
                    <div class="col-span-2">
                        <p class="text-gray-400 text-xs">Bio</p>
                        <p class="text-gray-700">{{ $user->bio }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Submitted Documents --}}
    <div class="card p-5 mb-6">
        <h4 class="font-semibold text-gray-800 mb-4">Submitted Documents</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Valid ID --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Valid ID</p>
                @if($user->id_document)
                    <a href="{{ asset('storage/' . $user->id_document) }}" target="_blank">
                        <img src="{{ asset('storage/' . $user->id_document) }}"
                             class="w-full h-36 object-cover rounded-xl border-2 border-gray-200 hover:border-primary-400 transition-colors cursor-zoom-in"
                             alt="Valid ID">
                    </a>
                    <p class="text-xs text-primary-600 mt-1 text-center">Click to view full size</p>
                @else
                    <div class="w-full h-36 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-sm border-2 border-dashed border-gray-200">
                        Not submitted
                    </div>
                @endif
            </div>

            {{-- Selfie --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Live Selfie Photo</p>
                @if($user->selfie_photo)
                    <a href="{{ asset('storage/' . $user->selfie_photo) }}" target="_blank">
                        <img src="{{ asset('storage/' . $user->selfie_photo) }}"
                             class="w-full h-36 object-cover rounded-xl border-2 border-gray-200 hover:border-primary-400 transition-colors cursor-zoom-in"
                             alt="Selfie">
                    </a>
                    <p class="text-xs text-primary-600 mt-1 text-center">Click to view full size</p>
                @else
                    <div class="w-full h-36 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-sm border-2 border-dashed border-gray-200">
                        Not submitted
                    </div>
                @endif
            </div>

            {{-- Farm Document --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Farm Document</p>
                @if($user->farm_document)
                    <a href="{{ asset('storage/' . $user->farm_document) }}" target="_blank">
                        <img src="{{ asset('storage/' . $user->farm_document) }}"
                             class="w-full h-36 object-cover rounded-xl border-2 border-gray-200 hover:border-primary-400 transition-colors cursor-zoom-in"
                             alt="Farm Document">
                    </a>
                    <p class="text-xs text-primary-600 mt-1 text-center">Click to view full size</p>
                @else
                    <div class="w-full h-36 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-sm border-2 border-dashed border-gray-200">
                        Not submitted
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Admin Notes --}}
    @if($user->admin_notes)
        <div class="card p-5 mb-6 bg-gray-50">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Admin Notes</p>
            <p class="text-sm text-gray-700">{{ $user->admin_notes }}</p>
        </div>
    @endif

    {{-- Action Buttons --}}
    @if($user->role !== 'admin')
        <div class="card p-5">
            <h4 class="font-semibold text-gray-800 mb-4">Admin Actions</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Approve --}}
                @if(in_array($user->account_status, ['pending', 'rejected']))
                    <div class="bg-primary-50 border border-primary-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-primary-800 mb-1">✅ Approve Seller</p>
                        <p class="text-xs text-primary-600 mb-3">Grant access to list and sell products.</p>
                        <form action="{{ route('admin.users.approve', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-primary w-full py-2 text-sm">Approve Account</button>
                        </form>
                    </div>
                @endif

                {{-- Reject --}}
                @if(in_array($user->account_status, ['pending', 'approved']))
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4" x-data="{ open: false }">
                        <p class="text-sm font-semibold text-red-800 mb-1">❌ Reject Application</p>
                        <p class="text-xs text-red-600 mb-3">Deny seller access with a reason.</p>
                        <button @click="open = !open" class="btn-danger w-full py-2 text-sm">Reject Account</button>
                        <div x-show="open" x-transition class="mt-3">
                            <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="space-y-2">
                                @csrf
                                <textarea name="rejection_reason" rows="3" required
                                          class="input text-sm resize-none"
                                          placeholder="State the reason for rejection (e.g. unclear ID, fake documents, incomplete info)..."></textarea>
                                <button type="submit" class="btn-danger w-full py-2 text-sm">Confirm Rejection</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Suspend --}}
                @if($user->account_status === 'approved')
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4" x-data="{ open: false }">
                        <p class="text-sm font-semibold text-gray-800 mb-1">🚫 Suspend Account</p>
                        <p class="text-xs text-gray-500 mb-3">Temporarily block this user from the platform.</p>
                        <button @click="open = !open" class="btn-secondary w-full py-2 text-sm">Suspend User</button>
                        <div x-show="open" x-transition class="mt-3">
                            <form action="{{ route('admin.users.suspend', $user) }}" method="POST" class="space-y-2">
                                @csrf
                                <textarea name="admin_notes" rows="3"
                                          class="input text-sm resize-none"
                                          placeholder="Reason for suspension (e.g. fraud report, scam complaints)..."></textarea>
                                <button type="submit" class="btn-danger w-full py-2 text-sm">Confirm Suspension</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Reinstate --}}
                @if($user->account_status === 'suspended')
                    <div class="bg-primary-50 border border-primary-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-primary-800 mb-1">🔓 Reinstate Account</p>
                        <p class="text-xs text-primary-600 mb-3">Restore access to the platform.</p>
                        <form action="{{ route('admin.users.reinstate', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-primary w-full py-2 text-sm">Reinstate Account</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
