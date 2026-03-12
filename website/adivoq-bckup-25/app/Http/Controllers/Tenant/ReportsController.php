<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Brand;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ReportsController extends Controller
{
    public function index(Request $request)
    {
        
$start = Carbon::parse($request->start_date ?? now()->subMonths(6))->startOfDay();
$end = Carbon::parse($request->end_date ?? now())->endOfDay();


        $payments = Payment::whereBetween('payment_date', [$start, $end])->get();
        $expenses = Expense::whereBetween('expense_date', [$start, $end])->get();
        $invoices = Invoice::whereBetween('issue_date', [$start, $end])->get();
$allInvoices = Invoice::all();
       $report = [
    'total_revenue' => $invoices->sum('total_amount'),
    'total_expenses' => $expenses->sum('amount'),
    'profit' => $invoices->sum('total_amount') - $expenses->sum('amount'),
    'total_invoices' => $invoices->count(),

'pending_amount' => $allInvoices
    ->whereNotIn('status',['paid','cancelled'])
    ->sum('balance_due'),
];


$brandRevenue = $invoices
    ->groupBy(fn($inv) => $inv->brand->name ?? 'Unknown')
    ->map(fn($group) => $group->sum('total_amount'));
    
    $agingDetails = [];

foreach (\App\Models\Invoice::where('status','!=','paid')->get() as $invoice) {

    if (!$invoice->due_date) continue;

    $daysOverdue = now()->diffInDays($invoice->due_date, false);

    $agingDetails[] = [
        'invoice_number' => $invoice->invoice_number,
        'brand' => $invoice->brand->name ?? '',
        'due_date' => $invoice->due_date,
        'days' => $daysOverdue,
        'balance' => $invoice->balance_due,
    ];
}

        return view('tenant.reports.index', compact(
            'report',
            'brandRevenue',
            'start',
            'end',
            'agingDetails'
        ));
    }
    
    
    public function exportCsv(Request $request)
{
    $start = Carbon::parse($request->start_date ?? now()->subMonths(6))->startOfDay();
    $end = Carbon::parse($request->end_date ?? now())->endOfDay();

    // ✅ Use INVOICE-based revenue (same as report page)
    $invoices = \App\Models\Invoice::whereBetween('issue_date', [$start, $end])
        ->with('brand')
        ->get();

  $expenses = \App\Models\Expense::all();

    $totalRevenue = $invoices->sum('total_amount');
    $totalExpenses = $expenses->sum('amount');
    $profit = $totalRevenue - $totalExpenses;

    $filename = "financial_report_{$start->format('Ymd')}_to_{$end->format('Ymd')}.csv";

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function () use ($invoices, $expenses, $totalRevenue, $totalExpenses, $profit) {

        $file = fopen('php://output', 'w');

        // ✅ Summary Section
        fputcsv($file, ['Summary']);
        fputcsv($file, ['Total Revenue', $totalRevenue]);
        fputcsv($file, ['Total Expenses', $totalExpenses]);
        fputcsv($file, ['Net Profit', $profit]);
        fputcsv($file, []); // blank row

        // ✅ Invoice Section
        fputcsv($file, ['Invoices']);
        fputcsv($file, ['Invoice Number', 'Brand', 'Issue Date', 'Total Amount', 'Balance Due', 'Status']);

        foreach ($invoices as $invoice) {
            fputcsv($file, [
                $invoice->invoice_number,
                $invoice->brand->name ?? '',
                $invoice->issue_date,
                $invoice->total_amount,
                $invoice->balance_due,
                $invoice->status,
            ]);
        }

        fputcsv($file, []); // blank row

        // ✅ Expense Section
        fputcsv($file, ['Expenses']);
        fputcsv($file, ['Description', 'Category', 'Date', 'Amount']);

        foreach ($expenses as $expense) {
            fputcsv($file, [
                $expense->description,
                $expense->category,
                $expense->expense_date,
                $expense->amount,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function exportPnLPdf(Request $request)
{
    $start = Carbon::parse($request->start_date ?? now()->subMonths(6))->startOfDay();
    $end = Carbon::parse($request->end_date ?? now())->endOfDay();

    $invoices = \App\Models\Invoice::whereBetween('issue_date', [
        $start->format('Y-m-d'),
        $end->format('Y-m-d')
    ])->with('brand')->get();

      $expenses = \App\Models\Expense::all();

    $totalRevenue = $invoices->sum('total_amount');
    $totalExpenses = $expenses->sum('amount');
    $profit = $totalRevenue - $totalExpenses;

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'pdf.pnl',
        compact('invoices','expenses','totalRevenue','totalExpenses','profit','start','end')
    );

    return $pdf->download("P&L_{$start->format('Ymd')}_to_{$end->format('Ymd')}.pdf");
}



public function gstSummary(Request $request)
{
    $start = Carbon::parse($request->start_date ?? now()->startOfYear())->startOfDay();
    $end = Carbon::parse($request->end_date ?? now())->endOfDay();

    $invoices = \App\Models\Invoice::whereBetween('issue_date', [
        $start->format('Y-m-d'),
        $end->format('Y-m-d')
    ])->get();

    $expenses = \App\Models\Expense::whereBetween('expense_date', [
        $start->format('Y-m-d'),
        $end->format('Y-m-d')
    ])->get();

    // ✅ GST Collected
    $cgst = $invoices->sum('cgst_amount');
    $sgst = $invoices->sum('sgst_amount');
    $igst = $invoices->sum('igst_amount');

    $gstCollected = $cgst + $sgst + $igst;

    // ✅ TDS
    $tdsTotal = $invoices->sum('tds_amount');

    // ✅ Revenue (Invoice based)
    $totalRevenue = $invoices->sum('total_amount');
    $totalExpenses = $expenses->sum('amount');
    $netIncome = $totalRevenue - $totalExpenses;

    // ✅ Estimated Income Tax (New Regime Slabs)
    $estimatedIncomeTax = 0;

    if ($netIncome > 300000) {

        if ($netIncome <= 700000) {
            $estimatedIncomeTax = ($netIncome - 300000) * 0.05;
        } elseif ($netIncome <= 1000000) {
            $estimatedIncomeTax = 20000 + ($netIncome - 700000) * 0.10;
        } elseif ($netIncome <= 1200000) {
            $estimatedIncomeTax = 50000 + ($netIncome - 1000000) * 0.15;
        } elseif ($netIncome <= 1500000) {
            $estimatedIncomeTax = 80000 + ($netIncome - 1200000) * 0.20;
        } else {
            $estimatedIncomeTax = 140000 + ($netIncome - 1500000) * 0.30;
        }
    }

    // ✅ Advance Tax (Quarterly)
    $advanceTax = [
        'June 15 (15%)' => $estimatedIncomeTax * 0.15,
        'Sept 15 (45%)' => $estimatedIncomeTax * 0.45,
        'Dec 15 (75%)' => $estimatedIncomeTax * 0.75,
        'Mar 15 (100%)' => $estimatedIncomeTax,
    ];

    // ✅ Net Tax Liability
    $netTaxLiability = $estimatedIncomeTax - $tdsTotal;

    return view('tenant.reports.tax-summary', compact(
        'cgst',
        'sgst',
        'igst',
        'gstCollected',
        'tdsTotal',
        'totalRevenue',
        'totalExpenses',
        'netIncome',
        'estimatedIncomeTax',
        'advanceTax',
        'netTaxLiability',
        'start',
        'end'
    ));
}

public function exportGstCsv(Request $request)
{
    $start = Carbon::parse($request->start_date ?? now()->startOfMonth())->startOfDay();
    $end = Carbon::parse($request->end_date ?? now())->endOfDay();

    $invoices = \App\Models\Invoice::whereBetween('issue_date', [
        $start->format('Y-m-d'),
        $end->format('Y-m-d')
    ])->with('brand')->get();

    $filename = "GST_Report_{$start->format('Ymd')}_to_{$end->format('Ymd')}.csv";

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function () use ($invoices) {

        $file = fopen('php://output', 'w');

        fputcsv($file, [
            'Invoice Number',
            'Issue Date',
            'Brand',
            'Taxable Amount',
            'CGST',
            'SGST',
            'IGST',
            'Total Tax',
            'Invoice Total'
        ]);

        foreach ($invoices as $invoice) {
            fputcsv($file, [
                $invoice->invoice_number,
                $invoice->issue_date,
                $invoice->brand->name ?? '',
                $invoice->taxable_amount,
                $invoice->cgst_amount,
                $invoice->sgst_amount,
                $invoice->igst_amount,
                $invoice->tax_amount,
                $invoice->total_amount
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function simpleTaxSummary(Request $request)
{

    $start = Carbon::parse($request->start_date ?? now()->startOfMonth())->startOfDay();
    $end = Carbon::parse($request->end_date ?? now())->endOfDay();

    $invoices = \App\Models\Invoice::whereBetween('issue_date', [
        $start->format('Y-m-d'),
        $end->format('Y-m-d')
    ])->get();

    $expenses = \App\Models\Expense::whereBetween('expense_date', [
        $start->format('Y-m-d'),
        $end->format('Y-m-d')
    ])->get();

    $totalRevenue = $invoices->sum('total_amount');
    $totalExpenses = $expenses->sum('amount');
    $netIncome = $totalRevenue - $totalExpenses;

    $gstTotal = $invoices->sum('tax_amount');
    $tdsTotal = $invoices->sum('tds_amount');

    // Simple estimated tax logic (new regime style)
    $estimatedTax = 0;

    if ($netIncome > 300000) {
        if ($netIncome <= 700000) {
            $estimatedTax = ($netIncome - 300000) * 0.05;
        } elseif ($netIncome <= 1000000) {
            $estimatedTax = 20000 + ($netIncome - 700000) * 0.10;
        } elseif ($netIncome <= 1200000) {
            $estimatedTax = 50000 + ($netIncome - 1000000) * 0.15;
        } elseif ($netIncome <= 1500000) {
            $estimatedTax = 80000 + ($netIncome - 1200000) * 0.20;
        } else {
            $estimatedTax = 140000 + ($netIncome - 1500000) * 0.30;
        }
    }

    $netTaxLiability = $estimatedTax - $tdsTotal;

    return view('tenant.tax-summary', compact(
        'start',
        'end',
        'totalRevenue',
        'totalExpenses',
        'netIncome',
        'gstTotal',
        'tdsTotal',
        'estimatedTax',
        'netTaxLiability'
    ));
}
   
}