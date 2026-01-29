@extends('layouts.app')

@section('page-title', 'Edit User')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
    <p class="text-sm text-gray-600 mt-1">
        Update user details, roles, and system access
    </p>
</div>

<div class="bg-white shadow rounded-lg p-6 w-full">

<form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
@csrf
@method('PUT')

{{-- Name --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
    <input type="text" name="name" required
           value="{{ old('name', $user->name) }}"
           class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- Email --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
    <input type="email" name="email" required
           value="{{ old('email', $user->email) }}"
           class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- Password --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Password (leave blank to keep current)</label>
    <input type="password" name="password"
           class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- Confirm Password --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
    <input type="password" name="password_confirmation"
           class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- Role --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
    <select name="role_id" class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="">-- Select Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- New Role --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Or Add New Role</label>
    <input type="text" name="new_role" value="{{ old('new_role') }}"
           class="w-full border border-gray-300 rounded px-3 py-2"
           placeholder="Enter a new role name">
</div>

{{-- ACCESS CONTROL --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Access
    </label>

    {{-- Select All --}}
    <label class="flex items-center gap-2 mb-2">
        <input type="checkbox" id="selectAllAccess">
        Select All Access
    </label>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm" id="accessModules">
        @php
            $modules = [
                'dashboard' => 'Dashboard',
                'attendance' => 'Attendance',
                'results' => 'Results',
                'participants' => 'Teams',
                'events' => 'Events',
                'sports' => 'Sports',
                'athletes' => 'Athletes',
                'coaches' => 'Coaches',
                'games' => 'Games',
                'gallery' => 'Gallery',
                'announcements' => 'Announcements',
                'users' => 'User Management',
                'logs' => 'Audit Logs',
                'settings' => 'Settings',
            ];
            $userAccess = old('access', $user->access ?? []);
        @endphp

        @foreach($modules as $key => $label)
            <label class="flex items-center gap-2">
                <input type="checkbox" name="access[]" value="{{ $key }}"
                       class="moduleCheckbox"
                       {{ in_array($key, $userAccess) ? 'checked' : '' }}>
                {{ $label }}
            </label>
        @endforeach
    </div>

    <p class="text-xs text-gray-500 mt-2">
        Select which modules this user can access in the sidebar.
    </p>
</div>

{{-- Actions --}}
<div class="flex justify-end gap-3 pt-4 border-t">
    <a href="{{ route('users.index') }}" class="px-4 py-2 rounded border border-gray-400 text-gray-700">
        Cancel
    </a>
    <button type="submit" class="px-5 py-2 rounded bg-green-600 text-white font-semibold">
        Update User
    </button>
</div>

</form>
</div>

{{-- Simple JS for Select All --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllAccess');
    const checkboxes = document.querySelectorAll('.moduleCheckbox');

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });
});
</script>

@endsection
