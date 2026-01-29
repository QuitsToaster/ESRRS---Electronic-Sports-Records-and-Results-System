@extends('layouts.app')

@section('page-title', 'Add User')

@section('content')

<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Create a user and assign role and system access.
    </p>
</div>

<div class="bg-white shadow rounded-lg p-6 w-full">

<form action="{{ route('users.store') }}" method="POST" class="space-y-6" x-data="{ allAccess: false }">
@csrf

{{-- Name --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name *</label>
    <input type="text" name="name" value="{{ old('name') }}" required
        class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- Email --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
    <input type="email" name="email" value="{{ old('email') }}" required
        class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- Password --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Password *</label>
    <input type="password" name="password" required
        class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- Confirm Password --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password *</label>
    <input type="password" name="password_confirmation" required
        class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- Role --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Role *</label>
    <select name="role_id" required class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="">-- Select Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
    </select>
</div>

{{-- ACCESS CONTROL --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Access
    </label>

    {{-- Select All --}}
    <label class="flex items-center gap-2 mb-2">
        <input type="checkbox" x-model="allAccess" @click="$refs.modules.querySelectorAll('[data-module=true]').forEach(cb => cb.checked = allAccess)">
        Select All Access
    </label>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm" x-ref="modules">
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
        @endphp

        @foreach($modules as $key => $label)
            <label class="flex items-center gap-2">
                <input type="checkbox" name="access[]" value="{{ $key }}" data-module="true"
                       {{ in_array($key, old('access', [])) ? 'checked' : '' }}>
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
    <a href="{{ route('users.index') }}"
       class="px-4 py-2 rounded border border-gray-400 text-gray-700">
        Cancel
    </a>
    <button type="submit"
            class="px-5 py-2 rounded bg-green-600 text-white font-semibold">
        Create User
    </button>
</div>

</form>
</div>

@endsection
