@extends('layouts.dashboard')
@section('title', 'Verifiers — Admin')
@section('page-title', 'Verifier Accounts')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-6">

    {{-- Create Verifier Form --}}
    <div class="lg:col-span-1">
        <div class="card p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Create Verifier Account</h3>
            <form action="{{ route('admin.verifiers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="input @error('name') border-red-400 @enderror" placeholder="e.g. Juan Dela Cruz">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="input @error('email') border-red-400 @enderror" placeholder="verifier@example.com">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Password</label>
                    <input type="password" name="password" required minlength="8"
                           class="input @error('password') border-red-400 @enderror" placeholder="Min. 8 characters">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="input" placeholder="Repeat password">
                </div>
                <button type="submit" class="btn-primary w-full py-2.5">Create Verifier</button>
            </form>
        </div>
    </div>

    {{-- Verifier List --}}
    <div class="lg:col-span-2">
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50">
                <h3 class="font-semibold text-gray-800">Active Verifiers ({{ $verifiers->count() }})</h3>
            </div>
            @forelse($verifiers as $v)
                <div class="px-5 py-4 border-b border-gray-50 last:border-0 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $v->avatar_url }}" class="w-10 h-10 rounded-lg object-cover shrink-0" alt="">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $v->name }}</p>
                            <p class="text-xs text-gray-400">{{ $v->email }}</p>
                            <p class="text-xs text-gray-400">Added {{ $v->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.verifiers.delete', $v) }}" method="POST"
                          onsubmit="return confirm('Remove {{ addslashes($v->name) }} as verifier?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Remove</button>
                    </form>
                </div>
            @empty
                <div class="px-5 py-12 text-center text-gray-400 text-sm">No verifier accounts yet.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
