@extends('layouts.admin')
@section('title', $admin ? 'Edit Admin' : 'Add Admin')
@section('page_title', $admin ? 'Edit Admin User' : 'Create Admin User')

@section('content')
<div class="max-w-lg">
    <a href="{{ route('admin.admin-users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i>Back</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ $admin ? route('admin.admin-users.update', $admin) : route('admin.admin-users.store') }}">
            @csrf
            @if($admin) @method('PUT') @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $admin?->name) }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $admin?->email) }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password {{ $admin ? '(leave blank to keep)' : '*' }}</label>
                    <input type="password" name="password" {{ $admin ? '' : 'required' }} class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role *</label>
                    <select name="role" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="admin" {{ old('role', $admin?->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super_admin" {{ old('role', $admin?->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>
                <div class="pt-4 border-t border-gray-100 text-right">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        {{ $admin ? 'Update' : 'Create' }} Admin
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection