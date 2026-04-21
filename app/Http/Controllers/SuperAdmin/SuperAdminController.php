<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PricingComponent;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    /**
     * Dashboard utama super admin
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('is_super_admin', false)->count(),
            'active_subscriptions' => Subscription::where('expires_at', '>', now())->count(),
            'total_payments' => Payment::where('status', 'success')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'success')->sum('amount'),
            'activity_logs' => ActivityLog::latest()->take(10)->get(),
        ];

        return view('superadmin.dashboard', $stats);
    }

    /**
     * Halaman manajemen pricing
     */
    public function pricingIndex()
    {
        $pricing = PricingComponent::orderBy('key')->paginate(10);
        return view('superadmin.pricing.index', compact('pricing'));
    }

    /**
     * Edit pricing component
     */
    public function pricingEdit(PricingComponent $pricing)
    {
        return view('superadmin.pricing.edit', compact('pricing'));
    }

    /**
     * Update pricing component
     */
    public function pricingUpdate(Request $request, PricingComponent $pricing)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $pricing->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_pricing',
            'description' => "Update pricing component: {$pricing->key} - {$pricing->name} to Rp " . number_format($pricing->price),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('superadmin.pricing.index')
            ->with('success', 'Pricing component berhasil diupdate');
    }

    /**
     * Halaman activity log
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter berdasarkan action jika ada
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter berdasarkan user jika ada
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan date range jika ada
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);
        $users = User::where('is_super_admin', false)->pluck('name', 'id');

        return view('superadmin.activity-logs', compact('logs', 'users'));
    }

    /**
     * Halaman monitoring user & subscription
     */
    public function users(Request $request)
    {
        $query = User::where('is_super_admin', false)->latest();

        // Search by name/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with('activeSubscription', 'payments')->paginate(20);

        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Detail user
     */
    public function userShow(User $user)
    {
        if ($user->is_super_admin) {
            abort(403, 'Tidak bisa akses data super admin');
        }

        $user->load('activeSubscription', 'payments', 'groupMembers.group');
        $payments = Payment::where('user_id', $user->id)->latest()->paginate(10);

        return view('superadmin.users.show', compact('user', 'payments'));
    }

    /**
     * Halaman laporan revenue
     */
    public function revenue(Request $request)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now()->endOfMonth();

        $payments = Payment::where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(50);

        $totalRevenue = Payment::where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $statistics = [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $payments->total(),
            'average_transaction' => $payments->count() > 0 ? $totalRevenue / $payments->count() : 0,
        ];

        return view('superadmin.revenue', compact('payments', 'statistics', 'startDate', 'endDate'));
    }
}
