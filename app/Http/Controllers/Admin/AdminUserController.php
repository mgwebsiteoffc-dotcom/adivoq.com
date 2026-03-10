<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = AdminUser::latest()->paginate(20);
        return view('admin.admin-users.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admin-users.form', ['admin' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
        ]);

        AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        ActivityLog::record(auth()->guard('admin')->user(), 'admin_user_created', "Created admin: {$request->email}");

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user created.');
    }

    public function edit(AdminUser $adminUser)
    {
        return view('admin.admin-users.form', ['admin' => $adminUser]);
    }

    public function update(Request $request, AdminUser $adminUser)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email,' . $adminUser->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
        ]);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $adminUser->update($data);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user updated.');
    }

    public function destroy(AdminUser $adminUser)
    {
        if ($adminUser->id === auth()->guard('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $adminUser->delete();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user deleted.');
    }
}