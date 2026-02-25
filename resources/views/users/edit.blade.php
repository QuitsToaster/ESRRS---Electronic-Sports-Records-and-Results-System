@extends('layouts.app')

@section('page-title', 'Edit User')

@section('content')

@php
$isSuperAdmin = $user->role && $user->role->name === 'Super Admin';
@endphp

{{-- ================= INSTRUCTIONS / NOTES ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>Update the information for an existing user account.</li>
        <li>You can modify the <strong>Name</strong> and <strong>Email</strong> of the user.</li>
        <li>To change the password, enter a new <strong>Password</strong> and confirm it. Leave blank if you do not want to update it.</li>
        <li>Select a new <strong>Role</strong> from the dropdown, or create a <strong>New Role</strong> if it does not exist.</li>
        <li>Adjust <strong>Access</strong> permissions, including <strong>Level Access</strong>, <strong>Category Access</strong>, <strong>Sports Access</strong>, and <strong>Sport Type Access</strong> as needed.</li>
        <li>Click <strong>Update User</strong> to save changes. All modifications will update the user’s account and permissions accordingly.</li>
        <li>After updating, you can return to the <strong>Users list</strong> to view, reset passwords, or delete accounts.</li>
    </ul>
</div>

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
    <label class="block text-sm font-semibold mb-1">Name</label>
    <input type="text" name="name" required
           value="{{ old('name', $user->name) }}"
           class="w-full border rounded px-3 py-2">
</div>

{{-- Email --}}
<div>
    <label class="block text-sm font-semibold mb-1">Email</label>
    <input type="email" name="email" required
           value="{{ old('email', $user->email) }}"
           class="w-full border rounded px-3 py-2">
</div>

{{-- Password --}}
<div>
    <label class="block text-sm font-semibold mb-1">Password</label>
    <input type="password" name="password"
           class="w-full border rounded px-3 py-2">
</div>

{{-- Role --}}
<div>
    <label class="block text-sm font-semibold mb-1">Role</label>
    <select name="role_id" class="w-full border rounded px-3 py-2">
        @foreach($roles as $role)
            <option value="{{ $role->id }}"
                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- New Role --}}
<div>
    <label class="block text-sm font-semibold mb-1">Or Add New Role</label>
    <input type="text"
           name="new_role"
           value="{{ old('new_role') }}"
           class="w-full border rounded px-3 py-2"
           placeholder="Enter new role name">
</div>

{{-- ACCESS --}}
<div>
    <label class="block text-sm font-semibold mb-2">System Access</label>

    <label class="flex items-center gap-2 mb-2">
        <input type="checkbox" id="selectAllAccess">
        Select All Access
    </label>

    @php
        $modules = [
            'dashboard','attendance','results','divisions','participants',
            'events','sports','athletes','coaches','games',
            'announcements','users','logs','settings'
        ];
        $userAccess = old('access', $user->access ?? []);
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
        @foreach($modules as $m)
            <label class="flex items-center gap-2">
                <input type="checkbox" class="moduleCheckbox"
                       name="access[]" value="{{ $m }}"
                       {{ in_array($m, $userAccess) ? 'checked' : '' }}>
                {{ ucfirst($m) }}
            </label>
        @endforeach
    </div>
</div>

{{-- LEVEL & CATEGORY --}}
<div class="border rounded p-4 bg-gray-50">
    <h3 class="font-semibold text-sm mb-3">Level & Category Access</h3>

    {{-- LEVEL --}}
    <div class="mb-3">
        <label class="flex items-center gap-2 font-semibold mb-1">
            <input type="checkbox" id="selectAllLevels">
            Select All Levels
        </label>

        @foreach(['Elementary','Secondary','Paragames'] as $level)
            <label class="inline-flex items-center mr-4">
                <input type="checkbox" class="levelCheckbox"
                       name="level_access[]"
                       value="{{ $level }}"
                       {{ $isSuperAdmin || in_array($level, old('level_access', $user->level_access ?? [])) ? 'checked' : '' }}>
                <span class="ml-1">{{ $level }}</span>
            </label>
        @endforeach
    </div>

    {{-- CATEGORY --}}
    <div>
        <label class="flex items-center gap-2 font-semibold mb-1">
            <input type="checkbox" id="selectAllCategories">
            Select All Categories
        </label>

        @foreach(['Boys','Girls'] as $cat)
            <label class="inline-flex items-center mr-4">
                <input type="checkbox" class="categoryCheckbox"
                       name="category_access[]"
                       value="{{ $cat }}"
                       {{ $isSuperAdmin || in_array($cat, old('category_access', $user->category_access ?? [])) ? 'checked' : '' }}>
                <span class="ml-1">{{ $cat }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- SPORTS ACCESS --}}
<div>
    <label class="flex items-center gap-2 font-semibold mb-2">
        <input type="checkbox" id="selectAllSports">
        Select All Sports
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
        @foreach(\App\Models\Sport::orderBy('division')->orderBy('category')->orderBy('name')->get() as $sport)
            <label class="flex items-center gap-2">
                <input type="checkbox" class="sportCheckbox"
                       name="sports_access[]"
                       value="{{ $sport->id }}"
                       {{ $isSuperAdmin || in_array($sport->id, old('sports_access', $user->sports_access ?? [])) ? 'checked' : '' }}>
                {{ $sport->name }}
                <span class="text-xs text-gray-500">
                    ({{ $sport->division }} • {{ $sport->category }})
                </span>
            </label>
        @endforeach
    </div>
</div>

{{-- SPORT TYPE ACCESS --}}
<div>
    <label class="flex items-center gap-2 font-semibold mb-2">
        <input type="checkbox" id="selectAllSportTypes">
        Select All Sport Types
    </label>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
        @foreach($sportTypes as $type)
            <label class="flex items-center gap-2">
                <input type="checkbox" class="sportTypeCheckbox"
                       name="sport_type_access[]"
                       value="{{ $type->id }}"
                       {{ $isSuperAdmin || in_array($type->id, old('sport_type_access', $user->sport_type_access ?? [])) ? 'checked' : '' }}>
                {{ $type->type_name }}
            </label>
        @endforeach
    </div>
</div>

{{-- Actions --}}
<div class="flex justify-end gap-3 pt-4 border-t">
    <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded">Cancel</a>
    <button class="px-5 py-2 bg-green-600 text-white rounded">Update User</button>
</div>

</form>
</div>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const bindSelectAll = (master, items) => {
        master.addEventListener('change', () => {
            items.forEach(cb => cb.checked = master.checked);
        });
    };

    bindSelectAll(document.getElementById('selectAllAccess'), document.querySelectorAll('.moduleCheckbox'));
    bindSelectAll(document.getElementById('selectAllLevels'), document.querySelectorAll('.levelCheckbox'));
    bindSelectAll(document.getElementById('selectAllCategories'), document.querySelectorAll('.categoryCheckbox'));
    bindSelectAll(document.getElementById('selectAllSports'), document.querySelectorAll('.sportCheckbox'));
    bindSelectAll(document.getElementById('selectAllSportTypes'), document.querySelectorAll('.sportTypeCheckbox'));
});
</script>

@endsection