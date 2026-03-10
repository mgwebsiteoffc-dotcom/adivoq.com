<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Campaign;
use App\Models\Milestone;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        // Revenue stats
        $revenueThisMonth = Payment::confirmed()->thisMonth()->sum('amount');
        $revenueThisYear = Payment::confirmed()
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        $revenueLastMonth = Payment::confirmed()
            ->whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->sum('amount');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // Invoice stats
        $outstandingAmount = Invoice::whereIn('status', ['sent', 'viewed', 'partially_paid', 'overdue'])->sum('amount_due');
        $overdueAmount = Invoice::overdue()->sum('amount_due');
        $overdueCount = Invoice::overdue()->count();
        $pendingPayments = Invoice::whereIn('status', ['sent', 'viewed'])->count();

        // Campaign stats
        $activeCampaigns = Campaign::where('status', 'active')->count();

        // Recent invoices
        $recentInvoices = Invoice::with('brand')
            ->latest()
            ->take(8)
            ->get();

        // Upcoming milestones
        $upcomingMilestones = Milestone::with(['campaign.brand'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Revenue chart (last 6 months)
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenueChart[] = [
                'month' => $date->format('M'),
                'revenue' => Payment::confirmed()
                    ->whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date', $date->year)
                    ->sum('amount'),
                'invoiced' => Invoice::whereMonth('issue_date', $date->month)
                    ->whereYear('issue_date', $date->year)
                    ->sum('total_amount'),
            ];
        }

        // Invoice status breakdown
        $invoiceBreakdown = [
            'draft' => Invoice::where('status', 'draft')->count(),
            'sent' => Invoice::where('status', 'sent')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::overdue()->count(),
            'partially_paid' => Invoice::where('status', 'partially_paid')->count(),
        ];

        return view('tenant.dashboard', compact(
            'tenant', 'revenueThisMonth', 'revenueThisYear', 'revenueLastMonth',
            'revenueGrowth', 'outstandingAmount', 'overdueAmount', 'overdueCount',
            'pendingPayments', 'activeCampaigns', 'recentInvoices',
            'upcomingMilestones', 'revenueChart', 'invoiceBreakdown'
        ));
    }
}