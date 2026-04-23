@extends('layouts.dashboard')
@section('title', 'Settings — Admin')
@section('page-title', 'Platform Settings')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    @if(session('success'))
        <div class="mb-5 px-4 py-3 bg-primary-50 border border-primary-200 text-primary-700 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Revenue Settings --}}
        <div class="card p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Revenue & Fees</h3>
            <div class="space-y-4">
                <div>
                    <label class="label">Commission Rate (%)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="commission_rate" value="{{ old('commission_rate', $commission_rate) }}"
                               min="0" max="100" step="0.1"
                               class="input w-32 @error('commission_rate') border-red-400 @enderror">
                        <span class="text-sm text-gray-500">% of order total taken as platform fee</span>
                    </div>
                    @error('commission_rate')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label">Delivery Fee (₱)</label>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 text-sm">₱</span>
                        <input type="number" name="delivery_fee" value="{{ old('delivery_fee', $delivery_fee) }}"
                               min="0" step="0.01"
                               class="input w-32 @error('delivery_fee') border-red-400 @enderror">
                        <span class="text-sm text-gray-500">flat fee charged per order</span>
                    </div>
                    @error('delivery_fee')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Location Settings --}}
        <div class="card p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Marketplace Location</h3>
            <div class="space-y-4">
                <div>
                    <label class="label">Short Location Name</label>
                    <input type="text" name="location" value="{{ old('location', $location) }}"
                           maxlength="100" placeholder="e.g. Cantilan"
                           class="input @error('location') border-red-400 @enderror">
                    @error('location')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label">Full City Name</label>
                    <input type="text" name="city" value="{{ old('city', $city) }}"
                           maxlength="150" placeholder="e.g. Cantilan, Surigao del Sur"
                           class="input @error('city') border-red-400 @enderror">
                    @error('city')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Current Values Preview --}}
        <div class="card p-5 bg-gray-50 border-dashed">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Current Live Values</p>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Commission</span>
                    <span class="font-semibold text-gray-800">{{ $commission_rate }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Delivery Fee</span>
                    <span class="font-semibold text-gray-800">₱{{ number_format($delivery_fee, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Location</span>
                    <span class="font-semibold text-gray-800">{{ $location }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">City</span>
                    <span class="font-semibold text-gray-800">{{ $city }}</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary px-6 py-2.5">Save Settings</button>
        </div>
    </form>
</div>
@endsection
