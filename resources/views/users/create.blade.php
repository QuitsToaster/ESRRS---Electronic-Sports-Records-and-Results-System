@extends('layouts.app')

@section('page-title', 'Add User')

@section('content')

<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Create a user and assign role and system access.
    </p>
</div>

<div class="bg-white shadow rounded-lg p-6 w-full">

<form
    action="{{ route('users.store') }}"
    method="POST"
    class="space-y-6"
    x-data="{
        allAccess: false,
        toggleAll() {
            this.$refs.modules
                .querySelectorAll('input[type=checkbox]')
                .forEach(cb => cb.checked = this.allAccess);
        }
    }"
>
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
    <select name="role_id" class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="">-- Select Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- New Role --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">
        Or Add New Role
    </label>
    <input type="text"
           name="new_role"
           value="{{ old('new_role') }}"
           class="w-full border border-gray-300 rounded px-3 py-2"
           placeholder="Enter new role name">
</div>

{{-- ACCESS CONTROL --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Access
    </label>

    {{-- Select All --}}
    <label class="flex items-center gap-2 mb-3">
        <input type="checkbox"
               x-model="allAccess"
               @change="toggleAll">
        Select All Access
    </label>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm" x-ref="modules">
        @php
            $modules = [
                'dashboard' => 'Dashboard',
                'attendance' => 'Attendance',
                'results' => 'Results',
                'divisions' => 'Divisions',
                'participants' => 'Teams',
                'events' => 'Events',
                'sports' => 'Sports',
                'athletes' => 'Athletes',
                'coaches' => 'Coaches',
                'games' => 'Games',
                'announcements' => 'Announcements',
                'users' => 'User Management',
                'logs' => 'Audit Logs',
                'settings' => 'Settings',
            ];
        @endphp

        @foreach($modules as $key => $label)
            <label class="flex items-center gap-2">
                <input type="checkbox"
                       name="access[]"
                       value="{{ $key }}"
                       {{ in_array($key, old('access', [])) ? 'checked' : '' }}>
                {{ $label }}
            </label>
        @endforeach
    </div>

    <p class="text-xs text-gray-500 mt-2">
        Select which modules this user can access in the sidebar.
    </p>
</div>

{{-- SPORTS ACCESS --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Sports Access
    </label>

    <p class="text-xs text-gray-500 mb-3">
        Select which sport(s) this user can view and manage.
    </p>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
        @foreach($sports as $sport)
            <label class="flex items-center gap-2">
                <input type="checkbox"
                       name="sports_access[]"
                       value="{{ $sport->id }}"
                       {{ in_array($sport->id, old('sports_access', [])) ? 'checked' : '' }}>
                {{ $sport->name }}
            </label>
        @endforeach
    </div>
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
