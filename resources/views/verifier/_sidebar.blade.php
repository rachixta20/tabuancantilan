<a href="{{ route('verifier.dashboard') }}" class="sidebar-link {{ request()->routeIs('verifier.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Pending Approvals
    @php try { $pendingCount = \App\Models\User::where('account_status','pending')->count(); } catch (\Exception $e) { $pendingCount = 0; } @endphp
    @if($pendingCount > 0)
        <span class="ml-auto badge bg-amber-100 text-amber-700">{{ $pendingCount }}</span>
    @endif
</a>
