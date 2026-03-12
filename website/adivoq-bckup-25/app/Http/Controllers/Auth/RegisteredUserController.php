<?php
// app/Http/Controllers/Auth/RegisteredUserController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'business_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

        // Create user first
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner',
            'is_system_admin' => false,
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->business_name,
            'slug' => Str::slug($request->business_name) . '-' . Str::random(5),
            'owner_id' => $user->id,
            'plan' => 'free',
            'status' => 'active',
            'settings' => [
                'currency' => 'INR',
                'timezone' => 'Asia/Kolkata',
            ],
        ]);

        // Update user with tenant_id
        $user->update(['tenant_id' => $tenant->id]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/dashboard');
    }
}