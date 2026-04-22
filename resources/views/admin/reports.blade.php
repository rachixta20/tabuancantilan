@extends('layouts.dashboard')
@section('title', 'Reports — Admin')
@section('page-title', 'User Reports')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')

{{-- Filters --}}
<div class="card p-4 mb-5">
    <form action="{{ route('admin.reports') }}" method="GET" class="flex flex-wrap gap-3">
        <select name="status" class="input w-40 text-sm py-2" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach(\App\Enums\ReportStatus::cases() as $s)
                <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
            @endforeach
        </select>
        <select name="type" class="input w-48 text-sm py-2" onchange="this.form.submit()">
            <option value="">All Types</option>
            @foreach(\App\Enums\ReportType::cases() as $t)
                <option value="{{ $t->value }}" {{ request('type') === $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
            @endforeach
        </select>
        @if(request('status') || request('type'))
            <a href="{{ route('admin.reports') }}" class="btn-secondary text-sm py-2 px-4">Clear</a>
        @endif
        @if($pendingCount > 0)
            <span class="ml-auto flex items-center gap-2 text-sm text-amber-700 font-medium">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $pendingCount }} pending {{ Str::plural('report', $pendingCount) }}
            </span>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-5 py-3.5 font-semibold text-gray-600">Reporter</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Reported</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Type</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Date</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($reports as $report)
                <tr class="hover:bg-gray-50 {{ $report->isPending() ? 'bg-amber-50/40' : '' }}">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ $report->reporter->avatar_url }}" class="w-7 h-7 rounded-lg" alt="">
                            <div>
                                <p class="font-medium text-gray-800 text-xs">{{ $report->reporter->name }}</p>
                                <p class="text-gray-400 text-xs capitalize">{{ $report->reporter->role }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-600">
                        @if($report->reportedUser)
                            <span class="font-medium text-gray-800">{{ $report->reportedUser->name }}</span>
                            <span class="text-gray-400 capitalize"> ({{ $report->reportedUser->role }})</span>
                        @endif
                        @if($report->reportedProduct)
                            <p class="text-gray-500 mt-0.5">re: {{ Str::limit($report->reportedProduct->name, 25) }}</p>
                        @endif
                        @if(!$report->reportedUser && !$report->reportedProduct)
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="badge bg-red-50 text-red-700 text-xs">{{ $report->type?->label() ?? $report->type?->value }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @php $color = $report->status?->color() ?? 'gray'; @endphp
                        <span class="badge bg-{{ $color }}-100 text-{{ $color }}-700 capitalize">
                            {{ $report->status?->label() ?? $report->status?->value }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-xs">{{ $report->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.reports.view', $report) }}" class="text-xs text-blue-600 hover:underline font-medium">Review</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">No reports found</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($reports->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $reports->links() }}</div>
    @endif
</div>
@endsection
