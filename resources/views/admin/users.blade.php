@extends('layouts.dashboard')
@section('title', 'Users — Admin')
@section('page-title', 'Manage Users')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')

{{-- Filters --}}
<div class="card p-4 mb-5">
    <form action="{{ route('admin.users') }}" method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               class="input w-48 text-sm py-2" placeholder="Search name or email...">
        <select name="role" class="input w-32 text-sm py-2" onchange="this.form.submit()">
            <option value="">All Roles</option>
            @foreach(['admin','farmer','buyer'] as $r)
                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        <select name="status" class="input w-36 text-sm py-2" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach(['pending','approved','rejected','suspended'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary text-sm py-2 px-4">Search</button>
        <a href="{{ route('admin.users') }}" class="btn-secondary text-sm py-2 px-4">Clear</a>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-5 py-3.5 font-semibold text-gray-600">User</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Role</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Farm / Location</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Account Status</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Joined</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $u->avatar_url }}" class="w-9 h-9 rounded-lg object-cover shrink-0" alt="">
                            <div>
                                <p class="font-medium text-gray-800 flex items-center gap-1">
                                    {{ $u->name }}
                                    @if($u->is_verified)
                                        <span class="text-primary-600 text-xs" title="Verified">✓</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="badge bg-gray-100 text-gray-700 capitalize">{{ $u->role }}</span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500">
                        @if($u->farm_name)
                            <p class="font-medium text-gray-700">{{ $u->farm_name }}</p>
                        @endif
                        {{ $u->location ?? '—' }}
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $colors = ['approved'=>'primary','pending'=>'amber','rejected'=>'red','suspended'=>'gray'];
                            $statusVal = $u->account_status?->value ?? 'approved';
                        @endphp
                        <span class="badge bg-{{ $colors[$statusVal] ?? 'gray' }}-100 text-{{ $colors[$statusVal] ?? 'gray' }}-700 capitalize">
                            {{ $statusVal }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-xs">{{ $u->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.view', $u) }}"
                               class="text-xs text-blue-600 hover:underline font-medium">View</a>

                            @if($u->isPending() && $u->role === 'farmer')
                                <form action="{{ route('admin.users.approve', $u) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-primary btn-sm text-xs py-1 px-2.5">Approve</button>
                                </form>
                            @endif

                            @if(($u->isPending() || $u->isApproved()) && $u->role !== 'admin')
                                <button onclick="document.getElementById('reject-{{ $u->id }}').classList.toggle('hidden')"
                                        class="btn-danger btn-sm text-xs py-1 px-2.5">Reject</button>
                            @endif

                            @if($u->isSuspended())
                                <form action="{{ route('admin.users.reinstate', $u) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-outline btn-sm text-xs py-1 px-2.5">Reinstate</button>
                                </form>
                            @elseif($u->isApproved() && $u->role !== 'admin')
                                <button onclick="document.getElementById('suspend-{{ $u->id }}').classList.toggle('hidden')"
                                        class="text-xs text-gray-500 hover:text-red-600 font-medium transition-colors">Suspend</button>
                            @endif
                        </div>

                        {{-- Reject Form (hidden) --}}
                        <div id="reject-{{ $u->id }}" class="hidden mt-2">
                            <form action="{{ route('admin.users.reject', $u) }}" method="POST" class="flex flex-col gap-1.5">
                                @csrf
                                <textarea name="rejection_reason" rows="2" required
                                          class="input text-xs resize-none"
                                          placeholder="Reason for rejection..."></textarea>
                                <button type="submit" class="btn-danger btn-sm text-xs py-1">Confirm Reject</button>
                            </form>
                        </div>

                        {{-- Suspend Form (hidden) --}}
                        <div id="suspend-{{ $u->id }}" class="hidden mt-2">
                            <form action="{{ route('admin.users.suspend', $u) }}" method="POST" class="flex flex-col gap-1.5">
                                @csrf
                                <textarea name="admin_notes" rows="2"
                                          class="input text-xs resize-none"
                                          placeholder="Reason for suspension (optional)..."></textarea>
                                <button type="submit" class="btn-danger btn-sm text-xs py-1">Confirm Suspend</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">No users found</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $users->links() }}</div>
    @endif
</div>
@endsection
