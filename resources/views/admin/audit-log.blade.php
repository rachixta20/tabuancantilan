@extends('layouts.dashboard')
@section('title', 'Audit Log — Admin')
@section('page-title', 'Audit Log')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-175">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Admin</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Action</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Subject</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Notes</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ $log->admin?->avatar_url ?? '' }}" class="w-7 h-7 rounded-lg object-cover shrink-0" alt="">
                                <span class="text-sm font-medium text-gray-800">{{ $log->admin?->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge bg-gray-100 text-gray-700">{{ str_replace('_', ' ', $log->action) }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ $log->subject_type }} #{{ $log->subject_id }}
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-xs max-w-xs truncate">
                            {{ $log->notes ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-gray-400 text-xs">
                            {{ $log->created_at->format('M d, Y · h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">No audit log entries yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
