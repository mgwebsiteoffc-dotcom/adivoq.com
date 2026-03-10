<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\InvoiceSetting;
use App\Models\TaxSetting;
use App\Models\NotificationSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with('owner')->withCount(['users', 'brands', 'invoices']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('business_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        if ($request->filled('plan_status')) {
            $query->where('plan_status', $request->plan_status);
        }

        $tenants = $query->latest()->paginate(20)->appends($request->query());

        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'trial' => Tenant::where('plan_status', 'trial')->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
        ];

        return view('admin.tenants.index', compact('tenants', 'stats'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'business_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'plan' => 'required|in:free,starter,professional,enterprise',
            'plan_status' => 'required|in:trial,active,suspended,cancelled',
            'status' => 'required|in:active,suspended',
            // Owner user fields
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create Tenant
            $tenant = Tenant::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . Str::random(5),
                'email' => $request->email,
                'business_name' => $request->business_name,
                'phone' => $request->phone,
                'plan' => $request->plan,
                'plan_status' => $request->plan_status,
                'trial_ends_at' => $request->plan_status === 'trial' ? now()->addDays(14) : null,
                'status' => $request->status,
            ]);

            // 2. Create Owner User
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'password' => Hash::make($request->owner_password),
                'role' => 'owner',
                'status' => 'active',
            ]);

            // 3. Create default Invoice Settings
            InvoiceSetting::create([
                'tenant_id' => $tenant->id,
                'invoice_prefix' => 'INV',
                'next_invoice_number' => 1,
                'default_payment_terms' => 'net_30',
                'default_payment_terms_days' => 30,
            ]);

            // 4. Create default Tax Settings
            TaxSetting::create([
                'tenant_id' => $tenant->id,
                'default_cgst_rate' => 9,
                'default_sgst_rate' => 9,
                'default_igst_rate' => 18,
                'default_tds_rate' => 10,
            ]);

            // 5. Create default Notification Settings
            NotificationSetting::create([
                'tenant_id' => $tenant->id,
            ]);

            DB::commit();

            ActivityLog::record(auth()->guard('admin')->user(), 'tenant_created', "Created tenant: {$tenant->name} with owner: {$user->email}");

            return redirect()->route('admin.tenants.show', $tenant)
                ->with('success', "Tenant '{$tenant->name}' created successfully with owner account '{$user->email}'.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create tenant: ' . $e->getMessage());
        }
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['owner', 'users', 'brands', 'taxSetting', 'invoiceSetting']);

        $stats = [
            'total_invoices' => Invoice::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count(),
            'paid_invoices' => Invoice::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('status', 'paid')->count(),
            'total_revenue' => Payment::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('status', 'confirmed')->sum('amount'),
            'brands_count' => $tenant->brands()->withoutGlobalScopes()->where('tenant_id', $tenant->id)->count(),
            'campaigns_count' => $tenant->campaigns()->withoutGlobalScopes()->count(),
            'users_count' => $tenant->users()->count(),
        ];

        $recentInvoices = Invoice::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->with('brand')
            ->latest()
            ->take(10)
            ->get();

        $activityLogs = ActivityLog::where('tenant_id', $tenant->id)
            ->latest('created_at')
            ->take(20)
            ->get();

        return view('admin.tenants.show', compact('tenant', 'stats', 'recentInvoices', 'activityLogs'));
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
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'business_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'plan' => 'required|in:free,starter,professional,enterprise',
            'plan_status' => 'required|in:trial,active,suspended,cancelled',
            'status' => 'required|in:active,suspended',
        ]);

        $tenant->update($request->only([
            'name', 'email', 'business_name', 'phone', 'plan',
            'plan_status', 'status',
        ]));

        ActivityLog::record(auth()->guard('admin')->user(), 'tenant_updated', "Updated tenant: {$tenant->name}");

        return redirect()->route('admin.tenants.show', $tenant)->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        ActivityLog::record(auth()->guard('admin')->user(), 'tenant_deleted', "Soft-deleted tenant: {$tenant->name}");
        $tenant->update(['status' => 'deleted']);
        $tenant->delete();

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended', 'plan_status' => 'suspended']);
        // Also suspend all users
        $tenant->users()->update(['status' => 'suspended']);

        ActivityLog::record(auth()->guard('admin')->user(), 'tenant_suspended', "Suspended tenant: {$tenant->name}");

        return back()->with('success', "Tenant '{$tenant->name}' has been suspended.");
    }

    public function reactivate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active', 'plan_status' => 'active']);
        // Reactivate all users
        $tenant->users()->update(['status' => 'active']);

        ActivityLog::record(auth()->guard('admin')->user(), 'tenant_reactivated', "Reactivated tenant: {$tenant->name}");

        return back()->with('success', "Tenant '{$tenant->name}' has been reactivated.");
    }

    public function impersonate(Tenant $tenant)
    {
        $owner = $tenant->owner;
        if (!$owner) {
            return back()->with('error', 'This tenant has no owner account. Cannot impersonate.');
        }

        if ($tenant->status !== 'active') {
            return back()->with('error', 'Cannot impersonate a suspended/deleted tenant.');
        }

        // Store admin ID in session for "back to admin" functionality
        session(['admin_impersonating' => auth()->guard('admin')->id()]);

        ActivityLog::record(auth()->guard('admin')->user(), 'impersonation_start', "Admin started impersonating tenant: {$tenant->name}");

        // Login as the tenant owner using the web guard
        auth()->guard('admin')->logout();
        auth()->login($owner);

        return redirect()->route('dashboard.home')->with('success', "You are now logged in as {$owner->name} ({$tenant->name}). Use logout to end impersonation.");
    }
}