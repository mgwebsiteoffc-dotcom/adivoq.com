<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\InvoiceSetting;
use App\Models\TaxSetting;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'business_name' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Create Tenant
            $tenant = Tenant::create([
                'name' => $request->business_name ?: $request->name,
                'slug' => Str::slug($request->name) . '-' . Str::random(5),
                'email' => $request->email,
                'business_name' => $request->business_name,
                'plan' => 'free',
                'plan_status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
                'status' => 'active',
            ]);

            // Create Owner User
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'owner',
                'status' => 'active',
            ]);

            // Create default settings
            InvoiceSetting::create([
                'tenant_id' => $tenant->id,
                'invoice_prefix' => 'INV',
                'next_invoice_number' => 1,
                'default_payment_terms' => 'net_30',
                'default_payment_terms_days' => 30,
            ]);

            TaxSetting::create([
                'tenant_id' => $tenant->id,
                'default_cgst_rate' => 9,
                'default_sgst_rate' => 9,
                'default_igst_rate' => 18,
                'default_tds_rate' => 10,
            ]);

            NotificationSetting::create([
                'tenant_id' => $tenant->id,
            ]);

            DB::commit();

            auth()->login($user);

            return redirect()->route('dashboard.home')
                ->with('success', 'Welcome! Your account has been created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Registration failed. Please try again.');
        }
    }
}