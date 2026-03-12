<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\TaxCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function taxCalculator()
    {
        return view('public.tools.tax-calculator');
    }

    public function calculateTax(Request $request, TaxCalculatorService $calculator)
    {
        $request->validate([
            'income' => 'required|numeric|min:0',
            'gst_amount' => 'nullable|numeric|min:0',
            'gst_rate' => 'nullable|numeric|min:0|max:28',
            'tds_amount' => 'nullable|numeric|min:0',
            'tds_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $results = [];

        // Income Tax
        if ($request->income > 0) {
            $results['old_regime'] = $calculator->calculateIncomeTax($request->income, 'old');
            $results['new_regime'] = $calculator->calculateIncomeTax($request->income, 'new');
        }

        // GST
        if ($request->filled('gst_amount') && $request->filled('gst_rate')) {
            $sellerState = $request->input('seller_state', '07');
            $buyerState = $request->input('buyer_state', '27');
            $results['gst'] = $calculator->calculateGST($request->gst_amount, $request->gst_rate, $sellerState, $buyerState);
        }

        // TDS
        if ($request->filled('tds_amount') && $request->filled('tds_rate')) {
            $results['tds'] = $calculator->calculateTDS($request->tds_amount, $request->tds_rate);
        }

        return view('public.tools.tax-calculator', compact('results'))->withInput();
    }

    public function invoiceGenerator()
    {
        $states = config('invoicehero.indian_states');
        return view('public.tools.invoice-generator', compact('states'));
    }

    public function generateFreePdf(Request $request)
    {
        $request->validate([
            'from_name' => 'required|string|max:255',
            'to_name' => 'required|string|max:255',
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $items = collect($request->items)->map(function ($item) {
            $item['amount'] = $item['quantity'] * $item['rate'];
            return $item;
        });

        $subtotal = $items->sum('amount');
        $taxRate = $request->input('tax_rate', 0);
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;

        $data = [
            'from' => $request->only(['from_name', 'from_email', 'from_address', 'from_phone', 'from_gstin']),
            'to' => $request->only(['to_name', 'to_email', 'to_address', 'to_phone', 'to_gstin']),
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'notes' => $request->notes,
            'currency_symbol' => '₹',
        ];

        $pdf = Pdf::loadView('pdf.free-invoice', $data)
            ->setPaper('a4')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'enable_unicode' => true,
            ]);

        return $pdf->download("Invoice-{$request->invoice_number}.pdf");
    }

    public function templates()
    {
        $templates = [
            ['name' => 'Classic Professional', 'slug' => 'classic', 'color' => '#1e40af', 'image' => 'classic.png', 'description' => 'Clean, timeless design perfect for professional creators.'],
            ['name' => 'Modern Minimal', 'slug' => 'minimal', 'color' => '#111827', 'image' => 'minimal.png', 'description' => 'Sleek minimalist look with focus on clarity.'],
            ['name' => 'Bold & Creative', 'slug' => 'bold', 'color' => '#dc2626', 'image' => 'bold.png', 'description' => 'Stand out with vibrant colors and bold typography.'],
            ['name' => 'Elegant Dark', 'slug' => 'dark', 'color' => '#1f2937', 'image' => 'dark.png', 'description' => 'Sophisticated dark theme for premium creators.'],
            ['name' => 'Startup Fresh', 'slug' => 'startup', 'color' => '#059669', 'image' => 'startup.png', 'description' => 'Fresh, energetic design for modern creators.'],
            ['name' => 'Corporate Blue', 'slug' => 'corporate', 'color' => '#2563eb', 'image' => 'corporate.png', 'description' => 'Professional corporate style with blue accents.'],
        ];

        return view('public.tools.templates', compact('templates'));
    }
}