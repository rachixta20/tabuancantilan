<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class VerifierController extends Controller
{
    public function dashboard()
    {
        $pending = User::whereIn('role', ['farmer', 'buyer'])
            ->where('account_status', 'pending')
            ->latest()
            ->paginate(15);

        $stats = [
            'pending'        => User::where('account_status', 'pending')->count(),
            'approved_today' => User::where('account_status', 'approved')
                ->whereDate('verified_at', today())->count(),
        ];

        return view('verifier.dashboard', compact('pending', 'stats'));
    }

    public function approve(User $user)
    {
        $before = $user->only(['account_status', 'is_verified']);
        $user->update([
            'account_status'   => 'approved',
            'is_verified'      => true,
            'verified_at'      => now(),
            'rejection_reason' => null,
        ]);
        AdminAuditLog::record('approve_user', $user, $before, $user->fresh()->only(['account_status', 'is_verified']));
        return back()->with('success', "{$user->name} has been approved.");
    }

    public function reject(Request $request, User $user)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $before = $user->only(['account_status', 'is_verified']);
        $user->update([
            'account_status'   => 'rejected',
            'is_verified'      => false,
            'rejection_reason' => $request->rejection_reason,
        ]);
        AdminAuditLog::record('reject_user', $user, $before, $user->fresh()->only(['account_status']), $request->rejection_reason);
        return back()->with('success', "{$user->name}'s account has been rejected.");
    }
}
