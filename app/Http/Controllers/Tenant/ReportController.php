<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Brand;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'revenue_this_month' => Payment::confirmed()->thisMonth()->sum('amount'),
            'outstanding' => Invoice::whereIn('status', ['sent','viewed','partially_paid','overdue'])->sum('amount_due'),
            'expenses_this_month' => Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
        ];

        return view('tenant.reports.index', compact('stats'));
    }

    public function revenue(Request $request)
    {
        $from = $request->date_from ? Carbon::parse($request->date_from) : now()->startOfMonth()->subMonths(5);
        $to   = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

        $payments = Payment::confirmed()
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->get();

        $byMonth = $payments->groupBy(fn($p) => $p->payment_date->format('Y-m'))
            ->map(fn($g) => $g->sum('amount'));

        $brandBreakdown = Brand::with(['invoices.payments' => function($q) use ($from, $to) {
            $q->confirmed()->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()]);
        }])->get()->map(function ($brand) {
            $sum = $brand->invoices->flatMap->payments->sum('amount');
            return ['brand' => $brand->name, 'revenue' => $sum];
        })->sortByDesc('revenue')->values();

        return view('tenant.reports.revenue', compact('from','to','byMonth','brandBreakdown'));
    }

    public function invoiceAging()
    {
        $invoices = Invoice::whereIn('status', ['sent','viewed','partially_paid','overdue'])
            ->orderBy('due_date')
            ->get();

        $buckets = [
            '0_30' => ['label' => '0–30 days', 'sum' => 0],
            '31_60' => ['label' => '31–60 days', 'sum' => 0],
            '61_90' => ['label' => '61–90 days', 'sum' => 0],
            '90_plus' => ['label' => '90+ days', 'sum' => 0],
        ];

        foreach ($invoices as $inv) {
            $days = now()->diffInDays($inv->due_date, false);
            $overdueDays = $days < 0 ? abs($days) : 0;

            if ($overdueDays <= 30) $buckets['0_30']['sum'] += $inv->amount_due;
            elseif ($overdueDays <= 60) $buckets['31_60']['sum'] += $inv->amount_due;
            elseif ($overdueDays <= 90) $buckets['61_90']['sum'] += $inv->amount_due;
            else $buckets['90_plus']['sum'] += $inv->amount_due;
        }

        return view('tenant.reports.invoice-aging', compact('invoices','buckets'));
    }

    public function paymentCollection(Request $request)
    {
        $from = $request->date_from ? Carbon::parse($request->date_from) : now()->startOfMonth()->subMonths(5);
        $to   = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

        $payments = Payment::confirmed()
            ->with('invoice.brand')
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->latest('payment_date')
            ->paginate(25)
            ->appends($request->query());

        $byMethod = Payment::confirmed()
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        return view('tenant.reports.payment-collection', compact('from','to','payments','byMethod'));
    }

    public function expenses(Request $request)
    {
        $from = $request->date_from ? Carbon::parse($request->date_from) : now()->startOfMonth()->subMonths(5);
        $to   = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->latest('expense_date')
            ->paginate(25)
            ->appends($request->query());

        $byCategory = Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('expense_category_id, SUM(amount) as total')
            ->groupBy('expense_category_id')
            ->with('category')
            ->get()
            ->map(fn($r) => ['category' => $r->category->name ?? 'Uncategorized', 'total' => $r->total])
            ->sortByDesc('total')
            ->values();

        return view('tenant.reports.expenses', compact('from','to','expenses','byCategory'));
    }

    public function profitLoss(Request $request)
    {
        $from = $request->date_from ? Carbon::parse($request->date_from) : now()->startOfMonth()->subMonths(5);
        $to   = $request->date_to ? Carbon::parse($request->date_to) : now()->endOfMonth();

        $months = [];
        $cursor = $from->copy()->startOfMonth();

        while ($cursor <= $to) {
            $mFrom = $cursor->copy()->startOfMonth();
            $mTo = $cursor->copy()->endOfMonth();

            $revenue = Payment::confirmed()->whereBetween('payment_date', [$mFrom->toDateString(), $mTo->toDateString()])->sum('amount');
            $expense = Expense::whereBetween('expense_date', [$mFrom->toDateString(), $mTo->toDateString()])->sum('amount');

            $months[] = [
                'month' => $cursor->format('M Y'),
                'revenue' => $revenue,
                'expense' => $expense,
                'profit' => $revenue - $expense,
            ];

            $cursor->addMonth();
        }

        $totals = [
            'revenue' => collect($months)->sum('revenue'),
            'expense' => collect($months)->sum('expense'),
            'profit' => collect($months)->sum('profit'),
        ];

        return view('tenant.reports.profit-loss', compact('from','to','months','totals'));
    }

    public function taxSummary(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);

        $invoices = Invoice::whereYear('issue_date', $year)->get();

        $gst = [
            'cgst' => $invoices->sum('cgst_amount'),
            'sgst' => $invoices->sum('sgst_amount'),
            'igst' => $invoices->sum('igst_amount'),
            'total' => $invoices->sum('total_tax'),
        ];

        $tds = [
            'total' => Invoice::whereYear('issue_date', $year)
                ->whereIn('status', ['paid','partially_paid'])
                ->sum('tds_amount'),
        ];

        return view('tenant.reports.tax-summary', compact('year','gst','tds'));
    }

    public function export(string $type, Request $request)
    {
        if ($type === 'revenue') {
            $rows = Payment::confirmed()->with('invoice.brand')->latest('payment_date')->take(5000)->get()
                ->map(fn($p) => [
                    'Payment Date' => $p->payment_date->toDateString(),
                    'Amount' => $p->amount,
                    'Method' => $p->payment_method,
                    'Invoice' => $p->invoice->invoice_number ?? '',
                    'Brand' => $p->invoice->brand->name ?? '',
                    'Reference' => $p->transaction_reference ?? '',
                ]);

            return $this->csv('revenue-payments', $rows);
        }

        if ($type === 'invoices') {
            $rows = Invoice::with('brand')->latest('issue_date')->take(5000)->get()
                ->map(fn($i) => [
                    'Invoice' => $i->invoice_number,
                    'Brand' => $i->brand->name ?? '',
                    'Issue Date' => $i->issue_date->toDateString(),
                    'Due Date' => $i->due_date->toDateString(),
                    'Status' => $i->status,
                    'Total' => $i->total_amount,
                    'TDS' => $i->tds_amount,
                    'Net Receivable' => $i->net_receivable,
                    'Paid' => $i->amount_paid,
                    'Due' => $i->amount_due,
                ]);

            return $this->csv('invoices', $rows);
        }

        if ($type === 'expenses') {
            $rows = Expense::with('category')->latest('expense_date')->take(5000)->get()
                ->map(fn($e) => [
                    'Date' => $e->expense_date->toDateString(),
                    'Title' => $e->title,
                    'Category' => $e->category->name ?? 'Uncategorized',
                    'Amount' => $e->amount,
                    'Tax Deductible' => $e->is_tax_deductible ? 'Yes' : 'No',
                ]);

            return $this->csv('expenses', $rows);
        }

        return back()->with('error', 'Invalid export type.');
    }

    private function csv(string $name, $rows)
    {
        $rows = collect($rows);

        if ($rows->isEmpty()) {
            return response("No data\n")
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$name}-" . date('Y-m-d') . ".csv\"");
        }

        $headers = array_keys($rows->first());

        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($v) {
                $v = (string) $v;
                $v = str_replace('"', '""', $v);
                return "\"{$v}\"";
            }, array_values($row))) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$name}-" . date('Y-m-d') . ".csv\"");
    }

    public function pdf(string $type, Request $request)
{
    $tenant = auth()->user()->tenant;

    if ($type === 'profit-loss') {
        // reuse same logic
        $viewData = $this->profitLoss($request)->getData();
        $pdf = Pdf::loadView('pdf.reports.profit-loss', array_merge($viewData, ['tenant' => $tenant]))
            ->setPaper('a4')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'enable_unicode' => true,
            ]);
        return $pdf->download('profit-loss-' . now()->format('Y-m-d') . '.pdf');
    }

    if ($type === 'tax-summary') {
        $viewData = $this->taxSummary($request)->getData();
        $pdf = Pdf::loadView('pdf.reports.tax-summary', array_merge($viewData, ['tenant' => $tenant]))
            ->setPaper('a4')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'enable_unicode' => true,
            ]);
        return $pdf->download('tax-summary-' . now()->format('Y-m-d') . '.pdf');
    }

    return back()->with('error', 'PDF not available for this report yet.');
}
}