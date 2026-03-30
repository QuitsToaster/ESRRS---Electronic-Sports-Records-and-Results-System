@extends('layouts.app')

@section('page-title', 'Edit User')

@section('content')

@php
$isSuperAdmin = $user->role && $user->role->name === 'Super Admin';
@endphp

{{-- ================= INSTRUCTIONS ================= --}}
<div class="p-5 mb-6 bg-blue-50 rounded-xl border border-blue-100 text-blue-800">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="font-semibold text-blue-800">How to Use This Page:</h3>
    </div>
    <ul class="list-disc list-inside text-sm space-y-1 ml-6">
        <li>Edit user details and permissions.</li>
        <li>Leave password blank to keep current password.</li>
        <li>Sports and Sport Types depend on Level & Category.</li>
        <li><strong>Paragames</strong> does not require Boys/Girls.</li>
    </ul>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 w-full">

<form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
@csrf
@method('PUT')

{{-- NAME & CONTACT --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name"
               value="{{ old('name', $user->name) }}"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Number</label>
        <div class="flex">
            <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 text-gray-600 text-sm">+63</span>
            <input type="text"
                   name="contact_number"
                   maxlength="10"
                   pattern="\d{10}"
                   value="{{ old('contact_number', optional($user)->contact_number ? substr($user->contact_number, 3) : '') }}"
                   class="w-full border border-gray-200 rounded-r-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>
        <p class="text-xs text-gray-500 mt-1.5">Enter 10 digits only. Example: 9123456789</p>
    </div>
</div>

{{-- EMAIL --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
    <input type="email" name="email"
           value="{{ old('email', $user->email) }}"
           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
</div>

{{-- PASSWORD --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password <span class="text-gray-400 text-xs font-normal">(optional)</span></label>
    <input type="password" name="password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    <p class="text-xs text-gray-500 mt-1.5">Leave blank to keep current password</p>
</div>

{{-- ROLE & NEW ROLE --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
        <select name="role_id" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
            @foreach($roles as $role)
                <option value="{{ $role->id }}"
                    {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Or Add New Role</label>
        <input type="text" name="new_role"
               value="{{ old('new_role') }}"
               placeholder="Enter custom role name"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    </div>
</div>

{{-- DIVISIONS (BILLETING QUARTERS) --}}
<div id="divisionSection" class="mt-5" style="display:none">
    <div class="bg-gray-50 rounded-xl p-5">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <label class="text-sm font-semibold text-gray-800">Division(s) <span class="text-red-500">*</span></label>
        </div>
        
        <p class="text-xs text-gray-500 mb-3">Select divisions that this user will manage for billeting quarters.</p>
        
        <div id="divisionContainer" class="space-y-3">
            @php
                $selectedDivisions = old('divisions', $user->divisions ?? []);
            @endphp
            
            @if(count($selectedDivisions) > 0)
                @foreach($selectedDivisions as $divisionId)
                    <div class="division-item flex items-center gap-2">
                        <select name="divisions[]" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                            <option value="">-- Select Division --</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}"
                                    {{ $divisionId == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="removeDivisionBtn text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
            @else
                <div class="division-item flex items-center gap-2">
                    <select name="divisions[]" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="">-- Select Division --</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="removeDivisionBtn text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            @endif
        </div>
        
        <button type="button" id="addDivisionBtn" 
                class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Another Division
        </button>
    </div>
</div>

{{-- SYSTEM ACCESS --}}
<div class="bg-gray-50 rounded-xl p-5">
    <label class="text-sm font-semibold text-gray-800 mb-3 block">System Access</label>

    <div class="mb-3">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAllAccess" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm font-medium text-gray-700">Select All Access</span>
        </label>
    </div>

    @php
        $modules = [
            'dashboard','attendance','results','divisions','participants',
            'events','sports','athletes','coaches','games',
            'announcements','users','logs','settings'
        ];
        $userAccess = old('access', $user->access ?? []);
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-sm">
        @foreach($modules as $m)
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" class="moduleCheckbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                       name="access[]" value="{{ $m }}"
                       {{ in_array($m, $userAccess) ? 'checked' : '' }}>
                <span class="text-gray-700 capitalize">{{ $m }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- LEVEL ACCESS --}}
<div class="bg-gray-50 rounded-xl p-5">
    <div class="mb-3">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAllLevels" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm font-medium text-gray-700">Select All Levels</span>
        </label>
    </div>

    <div class="flex flex-wrap gap-4">
        @foreach(['Elementary','Secondary','Paragames'] as $level)
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" class="levelCheckbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                       name="level_access[]" value="{{ $level }}"
                       {{ in_array($level, old('level_access', $user->level_access ?? [])) ? 'checked' : '' }}>
                <span class="text-gray-700">{{ $level }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- CATEGORY ACCESS --}}
<div class="bg-gray-50 rounded-xl p-5">
    <div class="mb-3">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAllCategories" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm font-medium text-gray-700">Select All Categories</span>
        </label>
    </div>

    <div class="flex flex-wrap gap-4">
        @foreach(['Boys','Girls'] as $cat)
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" class="categoryCheckbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                       name="category_access[]" value="{{ $cat }}"
                       {{ in_array($cat, old('category_access', $user->category_access ?? [])) ? 'checked' : '' }}>
                <span class="text-gray-700">{{ $cat }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- SPORTS --}}
<div class="bg-gray-50 rounded-xl p-5">
    <div class="mb-3">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAllSports" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm font-medium text-gray-700">Select All Sports</span>
        </label>
    </div>

    <div id="sportsContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-sm">

        @php
            $groupedSports = $sports->groupBy(function($item) {
                return $item->division . ' ' . ($item->category ?? '');
            });
        @endphp

        @foreach($groupedSports as $groupName => $group)
            <div class="col-span-full text-xs font-semibold text-gray-500 uppercase tracking-wider mt-3 first:mt-0 mb-2">{{ $groupName }}</div>

            @foreach($group as $sport)
                <label class="inline-flex items-center gap-2 sportItem cursor-pointer"
                       data-level="{{ $sport->division }}"
                       data-category="{{ $sport->category ?? '' }}"
                       style="display:none">

                    <input type="checkbox" class="sportCheckbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           name="sports_access[]" value="{{ $sport->id }}"
                           {{ in_array($sport->id, old('sports_access', $user->sports_access ?? [])) ? 'checked' : '' }}>

                    <span class="text-gray-700">{{ $sport->name }}</span>
                </label>
            @endforeach
        @endforeach
    </div>
</div>

{{-- SPORT TYPES --}}
<div class="bg-gray-50 rounded-xl p-5">
    <div class="mb-3">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAllSportTypes" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm font-medium text-gray-700">Select All Sport Types</span>
        </label>
    </div>

    <div id="sportTypeContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-sm">

        @php
            $groupedTypes = $sportTypes->groupBy(function($item) use ($sports) {
                $sport = $sports->firstWhere('id', $item->sport_id);
                return $sport ? $sport->division . ' ' . ($sport->category ?? '') : 'Unknown';
            });
        @endphp

        @foreach($groupedTypes as $groupName => $types)
            <div class="col-span-full text-xs font-semibold text-gray-500 uppercase tracking-wider mt-3 first:mt-0 mb-2">{{ $groupName }}</div>

            @foreach($types as $type)
                <label class="inline-flex items-center gap-2 sportTypeItem cursor-pointer"
                       data-sport-id="{{ $type->sport_id }}"
                       style="display:none">

                    <input type="checkbox" class="sportTypeCheckbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           name="sport_type_access[]" value="{{ $type->id }}"
                           {{ in_array($type->id, old('sport_type_access', $user->sport_type_access ?? [])) ? 'checked' : '' }}>

                    <span class="text-gray-700">{{ $type->type_name }}</span>
                </label>
            @endforeach
        @endforeach
    </div>
</div>

{{-- ACTIONS --}}
<div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
    <a href="{{ route('users.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">Cancel</a>
    <button class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl transition shadow-sm font-medium">
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

            item.style.display = show ? 'inline-flex' : 'none';
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
            item.style.display = show ? 'inline-flex' : 'none';
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
<script>
    // ===================== DIVISIONS (BILLETING QUARTERS) =====================
document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.querySelector('select[name="role_id"]');
    const divisionSection = document.getElementById('divisionSection');
    const divisionContainer = document.getElementById('divisionContainer');
    const addDivisionBtn = document.getElementById('addDivisionBtn');

    function toggleDivisionSection() {
        const selectedRoleText = roleSelect.options[roleSelect.selectedIndex].text;
        divisionSection.style.display = (selectedRoleText === 'Billeting Quarters') ? 'block' : 'none';
    }

    roleSelect.addEventListener('change', toggleDivisionSection);
    toggleDivisionSection(); // initial check on page load

    addDivisionBtn.addEventListener('click', () => {
        const newSelect = divisionContainer.children[0].cloneNode(true);
        newSelect.value = ''; // reset selection
        divisionContainer.appendChild(newSelect);
    });
});
</script>

@endsection