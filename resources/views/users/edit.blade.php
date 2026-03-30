@extends('layouts.app')

@section('page-title', 'Edit User')

@section('content')

@php
$isSuperAdmin = $user->role && $user->role->name === 'Super Admin';
@endphp

{{-- ================= INSTRUCTIONS ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>Edit user details and permissions.</li>
        <li>Leave password blank to keep current password.</li>
        <li>Sports and Sport Types depend on Level & Category.</li>
        <li><strong>Paragames</strong> does not require Boys/Girls.</li>
    </ul>
</div>

<div class="bg-white shadow rounded-lg p-6 w-full">

<form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
@csrf
@method('PUT')

{{-- NAME --}}
<div>
    <label class="block font-semibold mb-1">Name</label>
    <input type="text" name="name"
           value="{{ old('name', $user->name) }}"
           class="w-full border rounded px-3 py-2" required>
</div>

<div>
    <label class="block text-sm font-semibold mb-1">Contact Number</label>
    <div class="flex">
        <span class="inline-flex items-center px-3 rounded-l border bg-gray-100 text-gray-700">+63</span>
        <input type="text"
               name="contact_number"
               maxlength="10"
               pattern="\d{10}"
               value="{{ old('contact_number', optional($user)->contact_number ? substr($user->contact_number, 3) : '') }}"
               class="w-full border rounded-r px-3 py-2">
    </div>
    <p class="text-xs text-gray-500 mt-1">Enter 10 digits only. Example: 9123456789</p>
</div>

{{-- EMAIL --}}
<div>
    <label class="block font-semibold mb-1">Email</label>
    <input type="email" name="email"
           value="{{ old('email', $user->email) }}"
           class="w-full border rounded px-3 py-2" required>
</div>

{{-- PASSWORD --}}
<div>
    <label class="block font-semibold mb-1">New Password (optional)</label>
    <input type="password" name="password" class="w-full border rounded px-3 py-2">
</div>

{{-- ROLE --}}
<div>
    <label class="block font-semibold mb-1">Role</label>
    <select name="role_id" class="w-full border rounded px-3 py-2">
        @foreach($roles as $role)
            <option value="{{ $role->id }}"
                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- NEW ROLE --}}
<div>
    <label class="block font-semibold mb-1">Or Add New Role</label>
    <input type="text" name="new_role"
           value="{{ old('new_role') }}"
           class="w-full border rounded px-3 py-2">
</div>

{{-- ACCESS --}}
<div>
    <label class="font-semibold mb-2 block">System Access</label>

    <label class="flex items-center gap-2 mb-2">
        <input type="checkbox" id="selectAllAccess"> Select All Access
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

{{-- LEVEL --}}
<div>
    <label class="font-semibold mb-2 block">
        <input type="checkbox" id="selectAllLevels"> Select All Levels
    </label>

    <div class="flex gap-4 text-sm">
        @foreach(['Elementary','Secondary','Paragames'] as $level)
            <label class="flex items-center gap-2">
                <input type="checkbox" class="levelCheckbox"
                       name="level_access[]" value="{{ $level }}"
                       {{ in_array($level, old('level_access', $user->level_access ?? [])) ? 'checked' : '' }}>
                {{ $level }}
            </label>
        @endforeach
    </div>
</div>

{{-- CATEGORY --}}
<div>
    <label class="font-semibold mb-2 block">
        <input type="checkbox" id="selectAllCategories"> Select All Categories
    </label>

    <div class="flex gap-4 text-sm">
        @foreach(['Boys','Girls'] as $cat)
            <label class="flex items-center gap-2">
                <input type="checkbox" class="categoryCheckbox"
                       name="category_access[]" value="{{ $cat }}"
                       {{ in_array($cat, old('category_access', $user->category_access ?? [])) ? 'checked' : '' }}>
                {{ $cat }}
            </label>
        @endforeach
    </div>
</div>

{{-- SPORTS --}}
<div>
    <label class="font-semibold mb-2 block">
        <input type="checkbox" id="selectAllSports"> Select All Sports
    </label>

    <div id="sportsContainer" class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">

        @php
            $groupedSports = $sports->groupBy(function($item) {
                return $item->division . ' ' . ($item->category ?? '');
            });
        @endphp

        @foreach($groupedSports as $groupName => $group)
            <div class="col-span-full font-semibold mt-2">{{ $groupName }}</div>

            @foreach($group as $sport)
                <label class="flex items-center gap-2 sportItem"
                       data-level="{{ $sport->division }}"
                       data-category="{{ $sport->category ?? '' }}"
                       style="display:none">

                    <input type="checkbox" class="sportCheckbox"
                           name="sports_access[]" value="{{ $sport->id }}"
                           {{ in_array($sport->id, old('sports_access', $user->sports_access ?? [])) ? 'checked' : '' }}>

                    {{ $sport->name }}
                </label>
            @endforeach
        @endforeach
    </div>
</div>

{{-- SPORT TYPES --}}
<div>
    <label class="font-semibold mb-2 block">
        <input type="checkbox" id="selectAllSportTypes"> Select All Sport Types
    </label>

    <div id="sportTypeContainer" class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">

        @php
            $groupedTypes = $sportTypes->groupBy(function($item) use ($sports) {
                $sport = $sports->firstWhere('id', $item->sport_id);
                return $sport ? $sport->division . ' ' . ($sport->category ?? '') : 'Unknown';
            });
        @endphp

        @foreach($groupedTypes as $groupName => $types)
            <div class="col-span-full font-semibold mt-2">{{ $groupName }}</div>

            @foreach($types as $type)
                <label class="flex items-center gap-2 sportTypeItem"
                       data-sport-id="{{ $type->sport_id }}"
                       style="display:none">

                    <input type="checkbox" class="sportTypeCheckbox"
                           name="sport_type_access[]" value="{{ $type->id }}"
                           {{ in_array($type->id, old('sport_type_access', $user->sport_type_access ?? [])) ? 'checked' : '' }}>

                    {{ $type->type_name }}
                </label>
            @endforeach
        @endforeach
    </div>
</div>

{{-- ACTIONS --}}
<div class="flex justify-end gap-3 pt-4 border-t">
    <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded">Cancel</a>
    <button class="px-5 py-2 bg-green-600 text-white rounded font-semibold">
        Update User
    </button>
</div>

</form>
</div>


{{-- ================= JS (UPDATED WITH GROUP LABEL HIDING AND PARAGAMES CATEGORIES) ================= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    const levels = document.querySelectorAll('.levelCheckbox');
    const categories = document.querySelectorAll('.categoryCheckbox');
    const sports = document.querySelectorAll('.sportItem');
    const sportCheckboxes = document.querySelectorAll('.sportCheckbox');
    const sportTypes = document.querySelectorAll('.sportTypeItem');

    function getChecked(values) {
        return Array.from(values).filter(v => v.checked).map(v => v.value);
    }

    function filterSports() {
        const selectedLevels = getChecked(levels);
        const selectedCategories = getChecked(categories);

        const allLevelsSelected = selectedLevels.length === 3; // Elementary, Secondary, Paragames
        const allCategoriesSelected = selectedCategories.length === 2; // Boys, Girls

        sports.forEach(item => {
            const level = item.dataset.level;
            const category = item.dataset.category;

            let show = false;

            // ✅ ALL LEVELS + ALL CATEGORIES → SHOW EVERYTHING
            if (allLevelsSelected && allCategoriesSelected) {
                show = true;
            }
            // ✅ Paragames now has categories like Elementary/Secondary
            else if (selectedLevels.includes(level) && selectedCategories.includes(category)) {
                show = true;
            }

            item.style.display = show ? 'flex' : 'none';
            if (!show) item.querySelector('input').checked = false;
        });

        // ✅ HIDE EMPTY GROUP LABELS IN SPORTS
        const sportContainer = document.getElementById('sportsContainer');
        if (sportContainer) {
            const groupLabels = sportContainer.querySelectorAll('div.col-span-full');
            groupLabels.forEach(label => {
                const siblings = [];
                let sibling = label.nextElementSibling;
                while (sibling && !sibling.classList.contains('col-span-full')) {
                    siblings.push(sibling);
                    sibling = sibling.nextElementSibling;
                }
                const anyVisible = siblings.some(s => s.style.display !== 'none');
                label.style.display = anyVisible ? 'block' : 'none';
            });
        }

        filterSportTypes();
    }

    function filterSportTypes() {
        const selectedSports = getChecked(sportCheckboxes);

        sportTypes.forEach(item => {
            const sportId = item.dataset.sportId;
            const show = selectedSports.includes(sportId);
            item.style.display = show ? 'flex' : 'none';
            if (!show) item.querySelector('input').checked = false;
        });

        // ✅ HIDE EMPTY GROUP LABELS IN SPORT TYPES
        const sportTypeContainer = document.getElementById('sportTypeContainer');
        if (sportTypeContainer) {
            const typeLabels = sportTypeContainer.querySelectorAll('div.col-span-full');
            typeLabels.forEach(label => {
                const siblings = [];
                let sibling = label.nextElementSibling;
                while (sibling && !sibling.classList.contains('col-span-full')) {
                    siblings.push(sibling);
                    sibling = sibling.nextElementSibling;
                }
                const anyVisible = siblings.some(s => s.style.display !== 'none');
                label.style.display = anyVisible ? 'block' : 'none';
            });
        }
    }

    levels.forEach(cb => cb.addEventListener('change', filterSports));
    categories.forEach(cb => cb.addEventListener('change', filterSports));
    sportCheckboxes.forEach(cb => cb.addEventListener('change', filterSportTypes));

    function bind(masterId, className) {
        const master = document.getElementById(masterId);
        const items = document.querySelectorAll(className);
        if (!master) return;

        master.addEventListener('change', () => {
            items.forEach(cb => {
                if (cb.closest('label').style.display !== 'none') {
                    cb.checked = master.checked;
                }
            });
        });
    }

    bind('selectAllAccess', '.moduleCheckbox');
    bind('selectAllLevels', '.levelCheckbox');
    bind('selectAllCategories', '.categoryCheckbox');
    bind('selectAllSports', '.sportCheckbox');
    bind('selectAllSportTypes', '.sportTypeCheckbox');

    // INITIAL LOAD
    filterSports();
});
</script>

@endsection