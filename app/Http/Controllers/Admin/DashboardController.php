<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ContactMessage;
use App\Models\WaitlistEntry;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'trial_tenants' => Tenant::where('plan_status', 'trial')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'new_signups_today' => Tenant::whereDate('created_at', today())->count(),
            'new_signups_week' => Tenant::where('created_at', '>=', now()->subWeek())->count(),
            'new_signups_month' => Tenant::where('created_at', '>=', now()->subMonth())->count(),
            'total_invoices' => Invoice::withoutGlobalScopes()->count(),
            'total_revenue' => Payment::withoutGlobalScopes()->where('status', 'confirmed')->sum('amount'),
            'unread_messages' => ContactMessage::where('status', 'unread')->count(),
            'waitlist_count' => WaitlistEntry::where('status', 'waiting')->count(),
            'total_users' => User::withoutGlobalScopes()->count(),
        ];

        // MRR calculation (simplified)
        $planPrices = ['free' => 0, 'starter' => 499, 'professional' => 999, 'enterprise' => 2499];
        $mrr = 0;
        foreach ($planPrices as $plan => $price) {
            $mrr += Tenant::where('plan', $plan)->where('plan_status', 'active')->count() * $price;
        }
        $stats['mrr'] = $mrr;
        $stats['arr'] = $mrr * 12;

        // Plan distribution
        $planDistribution = Tenant::selectRaw('plan, count(*) as count')
            ->groupBy('plan')
            ->pluck('count', 'plan')
            ->toArray();

        // Recent activity
        $recentActivity = ActivityLog::with('tenant')
            ->latest('created_at')
            ->take(15)
            ->get();

        // Recent tenants
        $recentTenants = Tenant::with('owner')
            ->latest()
            ->take(10)
            ->get();

        // Signup chart data (last 30 days)
        $signupChart = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $signupChart[] = [
                'date' => Carbon::parse($date)->format('M d'),
                'count' => Tenant::whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.dashboard', compact('stats', 'planDistribution', 'recentActivity', 'recentTenants', 'signupChart'));
    }
}