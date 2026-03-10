<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TdsCertificate;
use App\Models\Brand;
use App\Models\Invoice;
use Illuminate\Http\Request;

class TdsCertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = TdsCertificate::with(['brand','invoice'])->latest();

        if ($request->filled('financial_year')) $query->where('financial_year', $request->financial_year);
        if ($request->filled('quarter')) $query->where('quarter', $request->quarter);
        if ($request->filled('status')) $query->where('status', $request->status);

        $certs = $query->paginate(20)->appends($request->query());

        $stats = [
            'total' => TdsCertificate::count(),
            'pending' => TdsCertificate::where('status','pending')->count(),
            'verified' => TdsCertificate::where('status','verified')->count(),
            'tds_total' => TdsCertificate::sum('tds_amount'),
        ];

        return view('tenant.tax.tds-certificates.index', compact('certs','stats'));
    }

    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $invoices = Invoice::latest()->take(200)->get();
        return view('tenant.tax.tds-certificates.form', ['cert' => null, 'brands'=>$brands, 'invoices'=>$invoices]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'nullable|exists:brands,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'certificate_number' => 'nullable|string|max:100',
            'financial_year' => 'nullable|string|max:9',
            'quarter' => 'nullable|in:Q1,Q2,Q3,Q4',
            'tds_amount' => 'required|numeric|min:0',
            'deducted_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
            'status' => 'required|in:pending,verified',
            'file' => 'nullable|file|max:4096',
        ]);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('tds-certificates', 'public');
        }

        TdsCertificate::create($data);

        return redirect()->route('dashboard.tds-certificates.index')->with('success', 'TDS certificate saved.');
    }

    public function edit(TdsCertificate $tdsCertificate)
    {
        $brands = Brand::active()->orderBy('name')->get();
        $invoices = Invoice::latest()->take(200)->get();
        return view('tenant.tax.tds-certificates.form', ['cert' => $tdsCertificate, 'brands'=>$brands, 'invoices'=>$invoices]);
    }

    public function update(Request $request, TdsCertificate $tdsCertificate)
    {
        $data = $request->validate([
            'brand_id' => 'nullable|exists:brands,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'certificate_number' => 'nullable|string|max:100',
            'financial_year' => 'nullable|string|max:9',
            'quarter' => 'nullable|in:Q1,Q2,Q3,Q4',
            'tds_amount' => 'required|numeric|min:0',
            'deducted_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
            'status' => 'required|in:pending,verified',
            'file' => 'nullable|file|max:4096',
        ]);

        if ($request->hasFile('file')) {
            if ($tdsCertificate->file_path) \Storage::disk('public')->delete($tdsCertificate->file_path);
            $data['file_path'] = $request->file('file')->store('tds-certificates', 'public');
        }

        $tdsCertificate->update($data);

        return redirect()->route('dashboard.tds-certificates.index')->with('success', 'TDS certificate updated.');
    }

    public function destroy(TdsCertificate $tdsCertificate)
    {
        if ($tdsCertificate->file_path) \Storage::disk('public')->delete($tdsCertificate->file_path);
        $tdsCertificate->delete();
        return back()->with('success', 'Deleted.');
    }
}