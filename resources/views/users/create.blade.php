@extends('layouts.app')

@section('page-title', 'Add User')

@section('content')

{{-- ================= INSTRUCTIONS / NOTES ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>Fill out the form to create a new user account in the system.</li>
        <li>Enter <strong>Name</strong> and <strong>Email</strong> for the user.</li>
        <li>Set a <strong>Password</strong> and confirm it. This will be the user’s initial login password.</li>
        <li>Select a <strong>Role</strong> from the dropdown. If the role does not exist, you can create a new one in the <strong>New Role</strong> field.</li>
        <li>Choose the appropriate <strong>Access</strong> options (e.g., results, admin panels) for the user.</li>
        <li>Select <strong>Level Access</strong> (Elementary, Secondary, Paragames) and <strong>Category Access</strong> (Boys/Girls) if applicable.</li>
        <li>Choose <strong>Sports Access</strong> and/or <strong>Divisions</strong> that this user should manage.</li>
        <li>Click <strong>Create User</strong> to save. The new user will receive permissions and access according to the selections.</li>
        <li>After creation, you can edit the user’s information or reset their password from the Users list.</li>
    </ul>
</div>

<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Create a user and assign role and system access.
    </p>
</div>

<div class="bg-white shadow rounded-lg p-6 w-full">

<form action="{{ route('users.store') }}" method="POST" class="space-y-6">
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

    <label class="flex items-center gap-2 mb-3">
        <input type="checkbox" id="selectAllAccess">
        Select All Access
    </label>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
        @php
            $modules = [
                'dashboard','attendance','results','divisions','participants',
                'events','sports','athletes','coaches','games',
                'announcements','users','logs','settings'
            ];
        @endphp

        @foreach($modules as $key)
            <label class="flex items-center gap-2">
                <input type="checkbox" class="moduleCheckbox"
                       name="access[]" value="{{ $key }}"
                       {{ in_array($key, old('access', [])) ? 'checked' : '' }}>
                {{ ucfirst($key) }}
            </label>
        @endforeach
    </div>
</div>

{{-- LEVEL ACCESS --}}
<div>
    <label class="flex items-center gap-2 font-semibold mb-2">
        <input type="checkbox" id="selectAllLevels">
        Select All Levels
    </label>

    <div class="flex gap-4 text-sm">
        @foreach(['Elementary','Secondary','Paragames'] as $level)
            <label class="flex items-center gap-2">
                <input type="checkbox" class="levelCheckbox"
                       name="level_access[]" value="{{ $level }}"
                       {{ in_array($level, old('level_access', [])) ? 'checked' : '' }}>
                {{ $level }}
            </label>
        @endforeach
    </div>
</div>

{{-- CATEGORY ACCESS --}}
<div>
    <label class="flex items-center gap-2 font-semibold mb-2">
        <input type="checkbox" id="selectAllCategories">
        Select All Categories
    </label>

    <div class="flex gap-4 text-sm">
        @foreach(['Boys','Girls'] as $cat)
            <label class="flex items-center gap-2">
                <input type="checkbox" class="categoryCheckbox"
                       name="category_access[]" value="{{ $cat }}"
                       {{ in_array($cat, old('category_access', [])) ? 'checked' : '' }}>
                {{ $cat }}
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

    <p class="text-xs text-gray-500 mb-3">
        Sports are grouped by <strong>Level</strong> and <strong>Category</strong>.
    </p>

    @foreach($sports->groupBy(['division','category']) as $division => $categories)
        <div class="mb-4">
            <h4 class="font-semibold text-sm mb-2">{{ $division }}</h4>

            @foreach($categories as $category => $items)
                <p class="text-xs text-gray-600 mb-1">{{ $category }}</p>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm mb-3">
                    @foreach($items as $sport)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" class="sportCheckbox"
                                   name="sports_access[]" value="{{ $sport->id }}"
                                   {{ in_array($sport->id, old('sports_access', [])) ? 'checked' : '' }}>
                            {{ $sport->name }}
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach
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

{{-- SELECT ALL JS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const bind = (master, items) => {
        master.addEventListener('change', () => {
            items.forEach(cb => cb.checked = master.checked);
        });
    };

    bind(
        document.getElementById('selectAllAccess'),
        document.querySelectorAll('.moduleCheckbox')
    );

    bind(
        document.getElementById('selectAllLevels'),
        document.querySelectorAll('.levelCheckbox')
    );

    bind(
        document.getElementById('selectAllCategories'),
        document.querySelectorAll('.categoryCheckbox')
    );

    bind(
        document.getElementById('selectAllSports'),
        document.querySelectorAll('.sportCheckbox')
    );
});
</script>

@endsection
