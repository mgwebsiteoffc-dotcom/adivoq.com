<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Tenant growth (monthly for last 12 months)
        $tenantGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $tenantGrowth[] = [
                'month' => $date->format('M Y'),
                'count' => Tenant::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'cumulative' => Tenant::where('created_at', '<=', $date->endOfMonth())->count(),
            ];
        }

        // Invoice volume (monthly)
        $invoiceVolume = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $invoiceVolume[] = [
                'month' => $date->format('M Y'),
                'count' => Invoice::withoutGlobalScopes()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'amount' => Invoice::withoutGlobalScopes()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_amount'),
            ];
        }

        // Revenue by plan
        $revenueByPlan = [];
        foreach (['free', 'starter', 'professional', 'enterprise'] as $plan) {
            $tenantIds = Tenant::where('plan', $plan)->pluck('id');
            $revenueByPlan[$plan] = [
                'tenants' => $tenantIds->count(),
                'invoices' => Invoice::withoutGlobalScopes()->whereIn('tenant_id', $tenantIds)->count(),
                'revenue' => Payment::withoutGlobalScopes()->whereIn('tenant_id', $tenantIds)->where('status', 'confirmed')->sum('amount'),
            ];
        }

        // Top tenants by revenue
        $topTenants = Tenant::select('tenants.*')
            ->selectSub(
                Payment::selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('payments.tenant_id', 'tenants.id')
                    ->where('status', 'confirmed'),
                'total_revenue'
            )
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        return view('admin.analytics.index', compact('tenantGrowth', 'invoiceVolume', 'revenueByPlan', 'topTenants'));
    }
}