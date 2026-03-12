<?php
// app/Http/Controllers/Admin/TenantController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with('owner')->withCount(['brands', 'invoices', 'users']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        $tenants = $query->latest()->paginate(20)->withQueryString();

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|min:8',
            'plan' => 'required|in:free,pro,business',
            'status' => 'required|in:active,suspended,trial',
        ]);

        // Create owner user
        $user = User::create([
            'name' => $request->owner_name,
            'email' => $request->owner_email,
            'password' => Hash::make($request->owner_password),
            'role' => 'owner',
            'is_system_admin' => false,
            'email_verified_at' => now(),
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'owner_id' => $user->id,
            'plan' => $request->plan,
            'status' => $request->status,
            'trial_ends_at' => $request->status === 'trial' ? now()->addDays(14) : null,
            'settings' => [
                'currency' => 'INR',
                'timezone' => 'Asia/Kolkata',
            ],
        ]);

        // Update user with tenant_id
        $user->update(['tenant_id' => $tenant->id]);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['owner', 'users', 'brands', 'invoices' => function ($q) {
            $q->latest()->take(5);
        }]);

        $stats = [
            'total_revenue' => $tenant->invoices()->sum('amount_paid'),
            'total_invoices' => $tenant->invoices()->count(),
            'total_brands' => $tenant->brands()->count(),
            'total_users' => $tenant->users()->count(),
        ];

        return view('admin.tenants.show', compact('tenant', 'stats'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('owner');
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'plan' => 'required|in:free,pro,business',
            'status' => 'required|in:active,suspended,trial',
        ]);

        $tenant->update([
            'name' => $request->name,
            'plan' => $request->plan,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        // Soft delete tenant
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);

        return back()->with('success', 'Tenant suspended successfully.');
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);

        return back()->with('success', 'Tenant activated successfully.');
    }

    public function impersonate(Tenant $tenant)
    {
        $owner = $tenant->owner;

        if (!$owner) {
            return back()->with('error', 'Tenant has no owner.');
        }

        // Store admin's original ID
        session(['impersonating_from' => auth()->id()]);

        // Login as tenant owner
        auth()->login($owner);
        session(['tenant_id' => $tenant->id]);

        return redirect('/dashboard')->with('info', 'You are now viewing as ' . $owner->name);
    }
}