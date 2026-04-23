<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\AdminAuditLog;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users'             => User::count(),
            'farmers'           => User::where('role', 'farmer')->count(),
            'buyers'            => User::where('role', 'buyer')->count(),
            'products'          => Product::count(),
            'orders'            => Order::count(),
            'gmv'               => Order::where('status', 'delivered')->sum('total'),
            'platform_revenue'  => Order::where('status', 'delivered')->sum('platform_fee'),
            'commission_rate'   => config('marketplace.commission_rate', 5),
            'pending_products'  => Product::where('status', 'pending')->count(),
            'pending_sellers'   => User::where('role', 'farmer')->where('account_status', 'pending')->count(),
        ];
        $recentOrders    = Order::with(['buyer', 'seller'])->latest()->take(5)->get();
        $pendingProducts = Product::with(['seller', 'category'])->where('status', 'pending')->take(5)->get();
        $pendingSellers  = User::where('role', 'farmer')->where('account_status', 'pending')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'pendingProducts', 'pendingSellers'));
    }

    public function users(Request $request)
    {
        $query = User::query();
        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('status')) $query->where('account_status', $request->status);
        if ($request->filled('search')) {
            $s = substr($request->search, 0, 100); // limit search string length
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        $users = $query->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function viewUser(User $user)
    {
        return view('admin.user-detail', compact('user'));
    }

    public function approveSeller(User $user)
    {
        $before = $user->only(['account_status', 'is_verified', 'verified_at']);
        $user->update([
            'account_status'   => 'approved',
            'is_verified'      => true,
            'verified_at'      => now(),
            'rejection_reason' => null,
        ]);
        AdminAuditLog::record('approve_seller', $user, $before, $user->fresh()->only(['account_status', 'is_verified']));
        return back()->with('success', "{$user->name} has been approved as a seller.");
    }

    public function rejectSeller(Request $request, User $user)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $before = $user->only(['account_status', 'is_verified']);
        $user->update([
            'account_status'   => 'rejected',
            'is_verified'      => false,
            'rejection_reason' => $request->rejection_reason,
        ]);
        AdminAuditLog::record('reject_seller', $user, $before, $user->fresh()->only(['account_status']), $request->rejection_reason);
        return back()->with('success', "{$user->name}'s application has been rejected.");
    }

    public function suspendUser(Request $request, User $user)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:500']);
        $before = $user->only(['account_status', 'is_active']);
        $user->update([
            'account_status' => 'suspended',
            'is_active'      => false,
            'admin_notes'    => $request->admin_notes,
        ]);
        AdminAuditLog::record('suspend_user', $user, $before, $user->fresh()->only(['account_status', 'is_active']), $request->admin_notes);
        return back()->with('success', "{$user->name} has been suspended.");
    }

    public function reinstateUser(User $user)
    {
        $before = $user->only(['account_status', 'is_active']);
        $user->update([
            'account_status' => 'approved',
            'is_active'      => true,
            'admin_notes'    => null,
        ]);
        AdminAuditLog::record('reinstate_user', $user, $before, $user->fresh()->only(['account_status', 'is_active']));
        return back()->with('success', "{$user->name} has been reinstated.");
    }

    public function toggleUser(User $user)
    {
        $before = $user->only(['is_active']);
        $user->update(['is_active' => !$user->is_active]);
        AdminAuditLog::record('toggle_user', $user, $before, $user->fresh()->only(['is_active']));
        return back()->with('success', 'User status updated.');
    }

    public function verifyUser(User $user)
    {
        $before = $user->only(['is_verified']);
        $user->update(['is_verified' => true]);
        AdminAuditLog::record('verify_user', $user, $before, ['is_verified' => true]);
        return back()->with('success', 'User verified.');
    }

    public function products(Request $request)
    {
        $query = Product::with(['seller', 'category']);
        if ($request->filled('status')) $query->where('status', $request->status);
        $products = $query->latest()->paginate(15);
        return view('admin.products', compact('products'));
    }

    public function approveProduct(Product $product)
    {
        $before = $product->only(['status']);
        $product->update(['status' => 'active']);
        AdminAuditLog::record('approve_product', $product, $before, ['status' => 'active']);
        return back()->with('success', 'Product approved.');
    }

    public function rejectProduct(Product $product)
    {
        $before = $product->only(['status']);
        $product->update(['status' => 'inactive']);
        AdminAuditLog::record('reject_product', $product, $before, ['status' => 'inactive']);
        return back()->with('success', 'Product rejected.');
    }

    public function categories()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'icon'        => 'nullable|string|max:50',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);
        $data['slug'] = Str::slug($data['name']);
        Category::create($data);
        return back()->with('success', 'Category created.');
    }

    public function orders()
    {
        $orders = Order::with(['buyer', 'seller'])->latest()->paginate(15);
        return view('admin.orders', compact('orders'));
    }

    public function auditLog()
    {
        $logs = AdminAuditLog::with('admin')->latest()->paginate(25);
        return view('admin.audit-log', compact('logs'));
    }

    public function reports(Request $request)
    {
        $query = Report::with(['reporter', 'reportedUser', 'reportedProduct', 'order']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type'))   $query->where('type', $request->type);
        $reports      = $query->latest()->paginate(15);
        $pendingCount = Report::where('status', ReportStatus::Pending)->count();
        return view('admin.reports', compact('reports', 'pendingCount'));
    }

    public function viewReport(Report $report)
    {
        $report->load(['reporter', 'reportedUser', 'reportedProduct', 'order', 'resolver']);
        return view('admin.report-detail', compact('report'));
    }

    public function verifiers()
    {
        $verifiers = User::where('role', 'verifier')->latest()->get();
        return view('admin.verifiers', compact('verifiers'));
    }

    public function storeVerifier(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => $data['password'],
            'role'           => 'verifier',
            'account_status' => 'approved',
            'is_verified'    => true,
            'is_active'      => true,
        ]);

        AdminAuditLog::record('create_verifier', $user, [], ['role' => 'verifier']);
        return back()->with('success', "Verifier account for {$user->name} created successfully.");
    }

    public function deleteVerifier(User $user)
    {
        abort_if($user->role !== 'verifier', 403);
        AdminAuditLog::record('delete_verifier', $user, ['role' => 'verifier'], []);
        $user->delete();
        return back()->with('success', "{$user->name}'s verifier account has been removed.");
    }

    public function settings()
    {
        return view('admin.settings', [
            'commission_rate' => Setting::get('commission_rate', config('marketplace.commission_rate')),
            'delivery_fee'    => Setting::get('delivery_fee', config('marketplace.delivery_fee')),
            'location'        => Setting::get('location', config('marketplace.location')),
            'city'            => Setting::get('city', config('marketplace.city')),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'delivery_fee'    => 'required|numeric|min:0',
            'location'        => 'required|string|max:100',
            'city'            => 'required|string|max:150',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    public function resolveReport(Request $request, Report $report)
    {
        $request->validate([
            'action'      => 'required|in:reviewed,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status'      => $request->action,
            'admin_notes' => $request->admin_notes,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Report has been ' . $request->action . '.');
    }
}
