<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::withCount(['campaigns', 'invoices']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('contact_person', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $brands = $query->latest()->paginate(15)->appends($request->query());

        return view('tenant.brands.index', compact('brands'));
    }

    public function create()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canAddBrand()) {
            return back()->with('error', 'You have reached the brand limit for your plan. Please upgrade.');
        }

        $states = config('invoicehero.indian_states');
        return view('tenant.brands.form', ['brand' => null, 'states' => $states]);
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->canAddBrand()) {
            return back()->with('error', 'Brand limit reached for your plan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'state_code' => 'nullable|string|max:5',
            'pincode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|max:1024',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($validated);

        return redirect()->route('dashboard.brands.index')->with('success', 'Brand added successfully!');
    }

    public function show(Brand $brand)
    {
        $brand->loadCount(['campaigns', 'invoices']);

        $totalRevenue = $brand->invoices()->where('status', 'paid')->sum('total_amount');
        $outstanding = $brand->invoices()->whereIn('status', ['sent', 'viewed', 'partially_paid', 'overdue'])->sum('amount_due');

        $campaigns = $brand->campaigns()->latest()->take(10)->get();

        $invoices = $brand->invoices()
            ->with('payments')
            ->latest()
            ->take(10)
            ->get();

        $payments = Payment::whereIn('invoice_id', $brand->invoices()->pluck('id'))
            ->confirmed()
            ->latest('payment_date')
            ->take(10)
            ->get();

        return view('tenant.brands.show', compact('brand', 'totalRevenue', 'outstanding', 'campaigns', 'invoices', 'payments'));
    }

    public function edit(Brand $brand)
    {
        $states = config('invoicehero.indian_states');
        return view('tenant.brands.form', compact('brand', 'states'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'state_code' => 'nullable|string|max:5',
            'pincode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|max:1024',
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo) \Storage::disk('public')->delete($brand->logo);
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($validated);

        return redirect()->route('dashboard.brands.show', $brand)->with('success', 'Brand updated!');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->invoices()->exists()) {
            return back()->with('error', 'Cannot delete a brand that has invoices. Archive it instead.');
        }

        if ($brand->logo) \Storage::disk('public')->delete($brand->logo);
        $brand->forceDelete();

        return redirect()->route('dashboard.brands.index')->with('success', 'Brand deleted.');
    }

    public function archive(Brand $brand)
    {
        $brand->update(['status' => 'archived']);
        return back()->with('success', 'Brand archived.');
    }

    public function restore(Brand $brand)
    {
        $brand->update(['status' => 'active']);
        return back()->with('success', 'Brand restored.');
    }

    // AJAX: search brands
    public function search(Request $request)
    {
        $brands = Brand::where('status', 'active')
            ->where('name', 'like', '%' . $request->q . '%')
            ->take(10)
            ->get(['id', 'name', 'email', 'gstin', 'state_code']);

        return response()->json($brands);
    }

    // AJAX: get brand details
    public function details(Brand $brand)
    {
        return response()->json($brand);
    }
}