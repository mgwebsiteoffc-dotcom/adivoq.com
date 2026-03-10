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

        $summary = [
            'count' => $invoices->count(),
            'taxable' => (float) $invoices->sum('taxable_amount'),
            'cgst' => (float) $invoices->sum('cgst_amount'),
            'sgst' => (float) $invoices->sum('sgst_amount'),
            'igst' => (float) $invoices->sum('igst_amount'),
            'gst_total' => (float) $invoices->sum('total_tax'),
            'invoice_total' => (float) $invoices->sum('total_amount'),
        ];

        $b2b = $invoices->filter(fn($i) => !empty($i->brand?->gstin))->values();
        $b2c = $invoices->filter(fn($i) => empty($i->brand?->gstin))->values();

        $b2clThreshold = (float) config('gst.b2cl_threshold', 250000);

        $b2cl = $b2c->filter(function ($inv) use ($b2clThreshold) {
            // B2CL: Inter-state + invoice value >= threshold
            $isInter = (float)$inv->igst_amount > 0;
            return $isInter && (float)$inv->total_amount >= $b2clThreshold;
        })->values();

        $b2cs = $b2c->reject(function ($inv) use ($b2clThreshold) {
            $isInter = (float)$inv->igst_amount > 0;
            return $isInter && (float)$inv->total_amount >= $b2clThreshold;
        })->values();

        $hsn = $this->buildHsnSummary($invoices);
        $nil = $this->buildNilExemptSummary($invoices);

        // New rate-wise B2B lines (invoice-wise lines grouped by rate)
        $b2bDetailed = $this->buildRateWiseLines($b2b);

        // B2CS summary grouped by Place of Supply + Rate
        $b2csSummary = $this->buildB2csSummary($b2cs);

        return view('tenant.tax.returns', compact(
            'from', 'to', 'invoices', 'summary',
            'b2b', 'b2c', 'b2cl', 'b2cs',
            'hsn', 'nil', 'b2bDetailed', 'b2csSummary', 'b2clThreshold'
        ));
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

        // Keep existing exports working
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

        if ($type === 'hsn_summary' || $type === 'gstr1_hsn') {
            $hsn = $this->buildHsnSummary($invoices);

            $rows = $hsn->map(fn($r) => [
                'HSN/SAC' => $r['hsn'],
                'Description' => $r['desc'],
                'Taxable Value' => $r['taxable'],
                'GST Rate (%)' => $r['rate'],
                'IGST Amount' => $r['igst'],
                'CGST Amount' => $r['cgst'],
                'SGST Amount' => $r['sgst'],
                'Total GST' => $r['gst_total'],
            ]);

            return $this->csv('gstr1-hsn-summary', $rows);
        }

        // ✅ NEW: B2B Detailed (rate-wise lines)
        if ($type === 'gstr1_b2b_detailed') {
            $b2b = $invoices->filter(fn($i) => !empty($i->brand?->gstin))->values();
            $lines = $this->buildRateWiseLines($b2b);

            $rows = $lines->map(fn($l) => [
                'Receiver GSTIN' => $l['gstin'],
                'Receiver Name' => $l['receiver_name'],
                'Invoice No' => $l['invoice_number'],
                'Invoice Date' => $l['invoice_date'],
                'Place of Supply (State Code)' => $l['pos_state_code'],
                'Invoice Value' => $l['invoice_value'],
                'GST Rate (%)' => $l['rate'],
                'Taxable Value' => $l['taxable'],
                'IGST Amount' => $l['igst'],
                'CGST Amount' => $l['cgst'],
                'SGST Amount' => $l['sgst'],
                'Total GST' => $l['gst_total'],
            ]);

            return $this->csv('gstr1-b2b-detailed', $rows);
        }

        // ✅ NEW: B2CL (B2C Large)
        if ($type === 'gstr1_b2cl') {
            $threshold = (float) config('gst.b2cl_threshold', 250000);

            $b2cl = $invoices->filter(fn($i) => empty($i->brand?->gstin))
                ->filter(fn($i) => (float)$i->igst_amount > 0 && (float)$i->total_amount >= $threshold)
                ->values();

            $rows = $b2cl->map(function ($i) {
                return [
                    'Invoice No' => $i->invoice_number,
                    'Invoice Date' => $i->issue_date->toDateString(),
                    'Place of Supply (State Code)' => $i->brand->state_code ?? '',
                    'Invoice Value' => $i->total_amount,
                    'Taxable Value' => $i->taxable_amount,
                    'IGST Amount' => $i->igst_amount,
                    'GST Rate (%)' => $this->invoiceGstRate($i),
                ];
            });

            return $this->csv('gstr1-b2cl', $rows);
        }

        // ✅ NEW: B2CS Summary (rate-wise + POS)
        if ($type === 'gstr1_b2cs') {
            $threshold = (float) config('gst.b2cl_threshold', 250000);

            $b2cs = $invoices->filter(fn($i) => empty($i->brand?->gstin))
                ->reject(fn($i) => (float)$i->igst_amount > 0 && (float)$i->total_amount >= $threshold)
                ->values();

            $sum = $this->buildB2csSummary($b2cs);

            $rows = $sum->map(fn($r) => [
                'Place of Supply (State Code)' => $r['pos_state_code'],
                'GST Rate (%)' => $r['rate'],
                'Taxable Value' => $r['taxable'],
                'IGST Amount' => $r['igst'],
                'CGST Amount' => $r['cgst'],
                'SGST Amount' => $r['sgst'],
                'Total GST' => $r['gst_total'],
            ]);

            return $this->csv('gstr1-b2cs-summary', $rows);
        }

        // ✅ NEW: Nil/Exempt/Non-GST (simple)
        if ($type === 'gstr1_nil_exempt') {
            $nil = $this->buildNilExemptSummary($invoices);

            $rows = $nil->map(fn($r) => [
                'Bucket' => $r['bucket'],              // Nil-rated / Exempt / Non-GST
                'Taxable Value' => $r['taxable'],
                'Invoices Count' => $r['count'],
            ]);

            return $this->csv('gstr1-nil-exempt', $rows);
        }

        // ✅ NEW: CDNR template (headers only - no credit note module yet)
        if ($type === 'gstr1_cdnr_template') {
            $rows = collect([[
                'Receiver GSTIN' => '',
                'Receiver Name' => '',
                'Note Type (C/D)' => '',
                'Note No' => '',
                'Note Date' => '',
                'Original Invoice No' => '',
                'Original Invoice Date' => '',
                'Note Value' => '',
                'Place of Supply (State Code)' => '',
                'GST Rate (%)' => '',
                'Taxable Value' => '',
                'IGST Amount' => '',
                'CGST Amount' => '',
                'SGST Amount' => '',
                'Reason' => '',
            ]]);

            return $this->csv('gstr1-cdnr-template', $rows);
        }

        return back()->with('error', 'Invalid export type.');
    }

    private function invoiceGstRate($invoice): float
    {
        if ((float)$invoice->igst_rate > 0) return (float) $invoice->igst_rate;
        return (float) ($invoice->cgst_rate + $invoice->sgst_rate);
    }

    /**
     * Build invoice-wise lines grouped by GST rate using item tax_rate.
     * Allocates invoice-level discount proportionally.
     */
    private function buildRateWiseLines($invoices)
    {
        $lines = collect();

        foreach ($invoices as $inv) {
            $subtotal = max((float)$inv->subtotal, 1);
            $taxableInv = (float)$inv->taxable_amount;

            $tenantIsIntra = ((float)$inv->cgst_rate + (float)$inv->sgst_rate) > 0;

            // group item taxable by rate
            $bucket = [];

            foreach ($inv->items as $item) {
                $rate = (float)($item->tax_rate ?? $this->invoiceGstRate($inv));
                $share = ((float)$item->amount) / $subtotal;
                $itemTaxable = round($taxableInv * $share, 2);

                $bucket[$rate] = ($bucket[$rate] ?? 0) + $itemTaxable;
            }

            foreach ($bucket as $rate => $taxable) {
                $igst = $tenantIsIntra ? 0 : round($taxable * ($rate/100), 2);
                $cgst = $tenantIsIntra ? round($taxable * (($rate/2)/100), 2) : 0;
                $sgst = $tenantIsIntra ? round($taxable * (($rate/2)/100), 2) : 0;

                $lines->push([
                    'gstin' => $inv->brand->gstin,
                    'receiver_name' => $inv->brand->name,
                    'invoice_number' => $inv->invoice_number,
                    'invoice_date' => $inv->issue_date->toDateString(),
                    'pos_state_code' => $inv->brand->state_code ?? '',
                    'invoice_value' => (float)$inv->total_amount,
                    'rate' => (float)$rate,
                    'taxable' => round($taxable, 2),
                    'igst' => $igst,
                    'cgst' => $cgst,
                    'sgst' => $sgst,
                    'gst_total' => round($igst+$cgst+$sgst, 2),
                ]);
            }
        }

        return $lines;
    }

    /**
     * B2CS summary grouped by POS state + rate
     */
    private function buildB2csSummary($invoices)
    {
        $rows = [];

        foreach ($invoices as $inv) {
            $pos = $inv->brand->state_code ?? '';
            $subtotal = max((float)$inv->subtotal, 1);
            $taxableInv = (float)$inv->taxable_amount;
            $intra = ((float)$inv->cgst_rate + (float)$inv->sgst_rate) > 0;

            // rate buckets
            $bucket = [];
            foreach ($inv->items as $item) {
                $rate = (float)($item->tax_rate ?? $this->invoiceGstRate($inv));
                $share = ((float)$item->amount) / $subtotal;
                $bucket[$rate] = ($bucket[$rate] ?? 0) + round($taxableInv * $share, 2);
            }

            foreach ($bucket as $rate => $taxable) {
                $key = $pos . '|' . $rate;

                $igst = $intra ? 0 : round($taxable * ($rate/100), 2);
                $cgst = $intra ? round($taxable * (($rate/2)/100), 2) : 0;
                $sgst = $intra ? round($taxable * (($rate/2)/100), 2) : 0;

                if (!isset($rows[$key])) {
                    $rows[$key] = [
                        'pos_state_code' => $pos,
                        'rate' => (float)$rate,
                        'taxable' => 0,
                        'igst' => 0,
                        'cgst' => 0,
                        'sgst' => 0,
                        'gst_total' => 0,
                    ];
                }

                $rows[$key]['taxable'] += round($taxable, 2);
                $rows[$key]['igst'] += $igst;
                $rows[$key]['cgst'] += $cgst;
                $rows[$key]['sgst'] += $sgst;
                $rows[$key]['gst_total'] += round($igst+$cgst+$sgst, 2);
            }
        }

        return collect($rows)->sortByDesc('taxable')->values();
    }

    /**
     * Existing HSN summary (kept)
     */
    private function buildHsnSummary($invoices)
    {
        $rows = [];

        foreach ($invoices as $inv) {
            $subtotal = max((float)$inv->subtotal, 1);
            $taxable = (float)$inv->taxable_amount;
            $isIntra = ((float)$inv->cgst_rate + (float)$inv->sgst_rate) > 0;

            foreach ($inv->items as $item) {
                $hsn = $item->hsn_sac_code ?: 'NA';
                $rate = (float)($item->tax_rate ?? $this->invoiceGstRate($inv));

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

    /**
     * Nil/Exempt/Non-GST simplified buckets:
     * - Nil-rated: GST rate == 0 but taxable present
     * - Non-GST: total_tax == 0 and gst_registered false? (we don't know) -> treat as Non-GST when tax=0 and no gstin on tenant? (not safe)
     * We'll keep it conservative: bucket as Nil/Exempt when rate==0; else ignore.
     */
    private function buildNilExemptSummary($invoices)
    {
        $nilCount = 0; $nilTaxable = 0;

        foreach ($invoices as $inv) {
            $rate = $this->invoiceGstRate($inv);
            if ($rate <= 0 && (float)$inv->taxable_amount > 0) {
                $nilCount++;
                $nilTaxable += (float)$inv->taxable_amount;
            }
        }

        return collect([
            ['bucket' => 'Nil/Exempt (GST rate 0)', 'taxable' => round($nilTaxable, 2), 'count' => $nilCount],
        ]);
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