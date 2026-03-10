@extends('layouts.admin')
@section('title', 'Admin Users')
@section('page_title', 'Admin User Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $admins->total() }} admin users</p>
    <a href="{{ route('admin.admin-users.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i>Add Admin
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Email</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Role</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Last Login</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($admins as $admin)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $admin->name }}
                        @if($admin->id === auth()->guard('admin')->id())
                            <span class="text-xs text-indigo-600 font-medium ml-1">(You)</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $admin->email }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $admin->role === 'super_admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst(str_replace('_',' ',$admin->role)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $admin->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.admin-users.edit', $admin) }}" class="text-blue-600 hover:text-blue-800 text-xs mr-2">Edit</a>
                        @if($admin->id !== auth()->guard('admin')->id())
                            <form method="POST" action="{{ route('admin.admin-users.destroy', $admin) }}" class="inline" onsubmit="return confirm('Delete admin?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $admins->links() }}</div>
</div>
@endsection