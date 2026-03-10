<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GstReturnController extends Controller
{
    public function index(Request $request)
    {
        // Default: current month
        $from = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfMonth();

        $invoices = Invoice::with(['brand', 'items'])
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('issue_date')
            ->get();

        // Summary totals
        $summary = [
            'count' => $invoices->count(),
            'taxable' => $invoices->sum('taxable_amount'),
            'cgst' => $invoices->sum('cgst_amount'),
            'sgst' => $invoices->sum('sgst_amount'),
            'igst' => $invoices->sum('igst_amount'),
            'gst_total' => $invoices->sum('total_tax'),
            'invoice_total' => $invoices->sum('total_amount'),
        ];

        // B2B = brand has GSTIN
        $b2b = $invoices->filter(fn($i) => !empty($i->brand?->gstin))->values();
        $b2c = $invoices->filter(fn($i) => empty($i->brand?->gstin))->values();

        // HSN summary (approx allocation for discount)
        $hsn = $this->buildHsnSummary($invoices);

        return view('tenant.tax.returns', compact('from', 'to', 'invoices', 'summary', 'b2b', 'b2c', 'hsn'));
    }

    public function export(string $type, Request $request)
    {
        $from = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfMonth();

        $invoices = Invoice::with(['brand', 'items'])
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('issue_date')
            ->get();

        if ($type === 'gstr1_b2b') {
            $rows = $invoices->filter(fn($i) => !empty($i->brand?->gstin))
                ->map(function ($i) {
                    $rate = $this->invoiceGstRate($i);
                    return [
                        'Receiver GSTIN' => $i->brand->gstin,
                        'Receiver Name' => $i->brand->name,
                        'Invoice No' => $i->invoice_number,
                        'Invoice Date' => $i->issue_date->toDateString(),
                        'Invoice Value' => $i->total_amount,
                        'Place of Supply (State Code)' => $i->brand->state_code ?? '',
                        'Taxable Value' => $i->taxable_amount,
                        'GST Rate (%)' => $rate,
                        'IGST Amount' => $i->igst_amount,
                        'CGST Amount' => $i->cgst_amount,
                        'SGST Amount' => $i->sgst_amount,
                        'Total GST' => $i->total_tax,
                    ];
                });

            return $this->csv('gstr1-b2b', $rows);
        }

        if ($type === 'gstr1_b2c') {
            // Simplified B2C summary (invoice-wise)
            $rows = $invoices->filter(fn($i) => empty($i->brand?->gstin))
                ->map(function ($i) {
                    $rate = $this->invoiceGstRate($i);
                    return [
                        'Customer/Brand' => $i->brand->name ?? 'B2C',
                        'Invoice No' => $i->invoice_number,
                        'Invoice Date' => $i->issue_date->toDateString(),
                        'Invoice Value' => $i->total_amount,
                        'Place of Supply (State Code)' => $i->brand->state_code ?? '',
                        'Taxable Value' => $i->taxable_amount,
                        'GST Rate (%)' => $rate,
                        'IGST Amount' => $i->igst_amount,
                        'CGST Amount' => $i->cgst_amount,
                        'SGST Amount' => $i->sgst_amount,
                        'Total GST' => $i->total_tax,
                    ];
                });

            return $this->csv('gstr1-b2c', $rows);
        }

        if ($type === 'hsn_summary') {
            $hsn = $this->buildHsnSummary($invoices);

            $rows = $hsn->map(function ($r) {
                return [
                    'HSN/SAC' => $r['hsn'],
                    'Description' => $r['desc'],
                    'Taxable Value' => $r['taxable'],
                    'GST Rate (%)' => $r['rate'],
                    'IGST Amount' => $r['igst'],
                    'CGST Amount' => $r['cgst'],
                    'SGST Amount' => $r['sgst'],
                    'Total GST' => $r['gst_total'],
                ];
            });

            return $this->csv('hsn-summary', $rows);
        }

        return back()->with('error', 'Invalid export type.');
    }

    private function invoiceGstRate($invoice): float
    {
        if ($invoice->igst_rate > 0) return (float) $invoice->igst_rate;
        return (float) ($invoice->cgst_rate + $invoice->sgst_rate);
    }

    private function buildHsnSummary($invoices)
    {
        $rows = [];

        foreach ($invoices as $inv) {
            $subtotal = max((float)$inv->subtotal, 1);
            $taxable = (float)$inv->taxable_amount;
            $isIntra = ($inv->cgst_rate + $inv->sgst_rate) > 0;

            foreach ($inv->items as $item) {
                $hsn = $item->hsn_sac_code ?: 'NA';
                $rate = (float)($item->tax_rate ?? $this->invoiceGstRate($inv));

                // Allocate invoice-level discount proportionally
                $itemShare = ((float)$item->amount) / $subtotal;
                $itemTaxable = round($taxable * $itemShare, 2);

                $igst = $isIntra ? 0 : round($itemTaxable * ($rate / 100), 2);
                $cgst = $isIntra ? round($itemTaxable * (($rate/2) / 100), 2) : 0;
                $sgst = $isIntra ? round($itemTaxable * (($rate/2) / 100), 2) : 0;

                if (!isset($rows[$hsn])) {
                    $rows[$hsn] = [
                        'hsn' => $hsn,
                        'desc' => 'Services',
                        'rate' => $rate,
                        'taxable' => 0,
                        'igst' => 0,
                        'cgst' => 0,
                        'sgst' => 0,
                        'gst_total' => 0,
                    ];
                }

                $rows[$hsn]['taxable'] += $itemTaxable;
                $rows[$hsn]['igst'] += $igst;
                $rows[$hsn]['cgst'] += $cgst;
                $rows[$hsn]['sgst'] += $sgst;
                $rows[$hsn]['gst_total'] += ($igst + $cgst + $sgst);
            }
        }

        return collect($rows)->sortByDesc('taxable')->values();
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
}