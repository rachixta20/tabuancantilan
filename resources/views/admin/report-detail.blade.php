@extends('layouts.dashboard')
@section('title', 'Review Report — Admin')
@section('page-title', 'Report Detail')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.reports') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600 mb-5">
        ← Back to Reports
    </a>

    {{-- Status Banner --}}
    @php $color = $report->status?->color() ?? 'gray'; @endphp
    <div class="border rounded-xl px-5 py-3 mb-6 bg-{{ $color }}-50 border-{{ $color }}-200 text-{{ $color }}-800 flex items-center justify-between">
        <span class="font-semibold">{{ $report->status?->label() }}</span>
        <span class="text-xs">Submitted {{ $report->created_at->format('M d, Y h:i A') }}</span>
    </div>

    {{-- Report Info --}}
    <div class="card p-5 mb-5 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Reported By</p>
                <div class="flex items-center gap-2">
                    <img src="{{ $report->reporter->avatar_url }}" class="w-8 h-8 rounded-lg" alt="">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $report->reporter->name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ $report->reporter->role }}</p>
                    </div>
                </div>
            </div>
            @if($report->reportedUser)
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Reported User</p>
                <div class="flex items-center gap-2">
                    <img src="{{ $report->reportedUser->avatar_url }}" class="w-8 h-8 rounded-lg" alt="">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $report->reportedUser->name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ $report->reportedUser->role }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.view', $report->reportedUser) }}" class="text-xs text-primary-600 hover:underline mt-1 inline-block">View profile →</a>
            </div>
            @endif
        </div>

        @if($report->reportedProduct)
        <div class="border-t border-gray-100 pt-4 text-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Reported Product</p>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    <img src="{{ $report->reportedProduct->image_url }}" class="w-full h-full object-cover" alt="">
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $report->reportedProduct->name }}</p>
                    <a href="{{ route('marketplace.show', $report->reportedProduct->slug) }}" target="_blank" class="text-xs text-primary-600 hover:underline">View listing →</a>
                </div>
            </div>
        </div>
        @endif

        @if($report->order)
        <div class="border-t border-gray-100 pt-4 text-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Related Order</p>
            <p class="font-mono font-semibold text-gray-800">{{ $report->order->order_number }}</p>
            <p class="text-xs text-gray-500">₱{{ number_format($report->order->total, 2) }} — Status: {{ $report->order->status?->value }}</p>
        </div>
        @endif

        <div class="border-t border-gray-100 pt-4 text-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Report Type</p>
            <span class="badge bg-red-50 text-red-700">{{ $report->type?->label() }}</span>
        </div>

        <div class="border-t border-gray-100 pt-4 text-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Description</p>
            <p class="text-gray-700 leading-relaxed">{{ $report->description }}</p>
        </div>

        @if($report->admin_notes)
        <div class="border-t border-gray-100 pt-4 text-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Admin Notes</p>
            <p class="text-gray-700">{{ $report->admin_notes }}</p>
            @if($report->resolver)
                <p class="text-xs text-gray-400 mt-1">Resolved by {{ $report->resolver->name }} on {{ $report->resolved_at?->format('M d, Y') }}</p>
            @endif
        </div>
        @endif
    </div>

    {{-- Action Panel --}}
    @if($report->isPending() || $report->status?->value === 'reviewed')
        <div class="card p-5" x-data="{ open: false }">
            <h4 class="font-semibold text-gray-800 mb-4">Take Action</h4>
            <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="space-y-4">
                @csrf @method('PATCH')

                <div class="grid grid-cols-3 gap-3">
                    <label class="flex flex-col items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 border-gray-200 hover:border-gray-300">
                        <input type="radio" name="action" value="reviewed" class="sr-only" {{ old('action') === 'reviewed' ? 'checked' : '' }}>
                        <span class="text-lg">👁</span>
                        <span class="text-xs font-semibold text-gray-700">Mark Reviewed</span>
                    </label>
                    <label class="flex flex-col items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors has-[:checked]:border-green-500 has-[:checked]:bg-green-50 border-gray-200 hover:border-gray-300">
                        <input type="radio" name="action" value="resolved" class="sr-only" {{ old('action') === 'resolved' ? 'checked' : '' }}>
                        <span class="text-lg">✅</span>
                        <span class="text-xs font-semibold text-gray-700">Resolve</span>
                    </label>
                    <label class="flex flex-col items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors has-[:checked]:border-gray-500 has-[:checked]:bg-gray-100 border-gray-200 hover:border-gray-300">
                        <input type="radio" name="action" value="dismissed" class="sr-only" {{ old('action') === 'dismissed' ? 'checked' : '' }}>
                        <span class="text-lg">🚫</span>
                        <span class="text-xs font-semibold text-gray-700">Dismiss</span>
                    </label>
                </div>

                <div>
                    <label class="label">Admin Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="admin_notes" rows="3" class="input resize-none text-sm"
                              placeholder="Describe the action taken or reason for dismissal...">{{ old('admin_notes') }}</textarea>
                </div>

                <button type="submit" class="btn-primary w-full py-2.5">Submit Decision</button>
            </form>
        </div>
    @else
        <div class="card p-5 bg-gray-50 text-center text-sm text-gray-500">
            This report has been {{ $report->status?->value }}.
            @if($report->resolver)
                Action taken by {{ $report->resolver->name }}.
            @endif
        </div>
    @endif
</div>
@endsection
