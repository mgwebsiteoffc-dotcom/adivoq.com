<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $brands = $query->latest()->paginate(20)->withQueryString();

        return view('tenant.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('tenant.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'gstin' => 'nullable|string|max:15',
            'pan' => 'nullable|string|max:10',
            'payment_terms' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        Brand::create($request->all());

        return redirect()->route('tenant.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function show(Brand $brand)
    {
        $brand->load(['campaigns', 'invoices', 'payments']);

        $stats = [
            'revenue' => $brand->payments()->sum('amount'),
            'invoices' => $brand->invoices()->count(),
            'campaigns' => $brand->campaigns()->count(),
            'pending' => $brand->invoices()->whereNotIn('status', ['paid', 'cancelled'])->sum('balance_due'),
        ];

        return view('tenant.brands.show', compact('brand', 'stats'));
    }

    public function edit(Brand $brand)
    {
        return view('tenant.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $brand->update($request->all());

        return redirect()->route('tenant.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('tenant.brands.index')
            ->with('success', 'Brand deleted.');
    }
}