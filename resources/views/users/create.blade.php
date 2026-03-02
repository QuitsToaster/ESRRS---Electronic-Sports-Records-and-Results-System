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
<div id="categorySection">
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
        Sports will appear based on selected Level and Category.
    </p>

    <div id="sportsContainer" class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">

        @foreach($sports as $sport)
            <label class="flex items-center gap-2 sportItem"
                   data-level="{{ $sport->division }}"
                   data-category="{{ $sport->category ?? '' }}"
                   style="display:none">

                <input type="checkbox"
                       class="sportCheckbox"
                       name="sports_access[]"
                       value="{{ $sport->id }}"
                       data-sport-id="{{ $sport->id }}">

                {{ $sport->name }}
            </label>
        @endforeach

    </div>
</div>

{{-- SPORT TYPE ACCESS --}}
<div class="mt-6">
    <label class="flex items-center gap-2 font-semibold mb-2">
        <input type="checkbox" id="selectAllSportTypes">
        Select All Sport Types
    </label>

    <p class="text-xs text-gray-500 mb-3">
        Sport types will appear based on selected sport.
    </p>

    <div id="sportTypeContainer" class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">

        @foreach($sportTypes as $type)
            <label class="flex items-center gap-2 sportTypeItem"
                   data-sport-id="{{ $type->sport_id }}"
                   data-level="{{ $type->division ?? '' }}"
                   data-category="{{ $type->category ?? '' }}"
                   style="display:none">

                <input type="checkbox"
                       class="sportTypeCheckbox"
                       name="sport_type_access[]"
                       value="{{ $type->id }}">

                {{ $type->type_name }}
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

    bind(
        document.getElementById('selectAllSportTypes'),
        document.querySelectorAll('.sportTypeCheckbox')
    );
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const levelCheckboxes     = document.querySelectorAll('.levelCheckbox');
    const categoryCheckboxes  = document.querySelectorAll('.categoryCheckbox');
    const sportCheckboxes     = document.querySelectorAll('.sportCheckbox');
    const sportItems          = document.querySelectorAll('.sportItem');
    const sportTypeItems      = document.querySelectorAll('.sportTypeItem');

    const categorySection     = document.getElementById('categorySection');

    // --------------------------------------------------
    // HANDLE PARAGAMES CATEGORY RULE
    // --------------------------------------------------
    function handleParagamesRule() {

        const paragamesChecked = Array.from(levelCheckboxes)
            .some(cb => cb.checked && cb.value === 'Paragames');

        if (paragamesChecked) {
            // Hide category section
            categorySection.style.display = 'none';

            // Uncheck all categories
            categoryCheckboxes.forEach(cb => cb.checked = false);
        } else {
            categorySection.style.display = 'block';
        }
    }

    // --------------------------------------------------
    // FILTER SPORTS BASED ON RULES
    // --------------------------------------------------
    function filterSports() {

        const selectedLevels = Array.from(levelCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        const selectedCategories = Array.from(categoryCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        sportItems.forEach(item => {

            const sportLevel    = item.dataset.level;
            const sportCategory = item.dataset.category;

            let show = false;

            // 🟢 PARAGAMES → CATEGORY NOT REQUIRED
            if (
                selectedLevels.includes('Paragames') &&
                sportLevel === 'Paragames'
            ) {
                show = true;
            }

            // 🟢 ELEMENTARY / SECONDARY → CATEGORY REQUIRED
            if (
                selectedLevels.includes(sportLevel) &&
                selectedCategories.includes(sportCategory)
            ) {
                show = true;
            }

            if (show) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
                item.querySelector('input').checked = false;
            }
        });

        filterSportTypes();
    }

    // --------------------------------------------------
    // FILTER SPORT TYPES
    // --------------------------------------------------
    function filterSportTypes() {

        const selectedSports = Array.from(sportCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        sportTypeItems.forEach(item => {
            const sportId = item.dataset.sportId;

            if (selectedSports.includes(sportId)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
                item.querySelector('input').checked = false;
            }
        });
    }

    // --------------------------------------------------
    // EVENT LISTENERS
    // --------------------------------------------------
    levelCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            handleParagamesRule();
            filterSports();
        });
    });

    categoryCheckboxes.forEach(cb =>
        cb.addEventListener('change', filterSports)
    );

    sportCheckboxes.forEach(cb =>
        cb.addEventListener('change', filterSportTypes)
    );

    // --------------------------------------------------
    // SELECT ALL (VISIBLE ONLY)
    // --------------------------------------------------
    function bind(masterId, itemsClass) {
        const master = document.getElementById(masterId);
        const items = document.querySelectorAll(itemsClass);

        master.addEventListener('change', () => {
            items.forEach(cb => {
                if (cb.closest('label').style.display !== 'none') {
                    cb.checked = master.checked;
                }
            });
        });
    }

    bind('selectAllSports', '.sportCheckbox');
    bind('selectAllSportTypes', '.sportTypeCheckbox');

});
</script>

@endsection
