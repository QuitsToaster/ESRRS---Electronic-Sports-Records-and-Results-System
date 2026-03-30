@extends('layouts.app')

@section('page-title', 'Add User')

@section('content')

<div class="bg-white shadow rounded-lg p-6 w-full">

<form action="{{ route('users.store') }}" method="POST" class="space-y-6">

@if ($errors->any())
    <div class="mb-4 p-4 rounded-lg border border-red-400 bg-red-100 text-red-700">
        <strong class="block mb-2">Whoops! Something went wrong:</strong>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@csrf

{{-- BASIC INFO --}}
<div>
    <label class="block text-sm font-semibold mb-1">Full Name *</label>
    <input type="text" name="name" required class="w-full border rounded px-3 py-2">
</div>

<div>
    <label class="block text-sm font-semibold mb-1">Email *</label>
    <input type="email" name="email" required class="w-full border rounded px-3 py-2">
</div>


<div>
    <label class="block text-sm font-semibold mb-1">Password *</label>
    <input type="password" name="password" required class="w-full border rounded px-3 py-2">
</div>

<div>
    <label class="block text-sm font-semibold mb-1">Confirm Password *</label>
    <input type="password" name="password_confirmation" required class="w-full border rounded px-3 py-2">
</div>

<div>
    <label class="block text-sm font-semibold mb-1">Contact Number</label>
    <div class="flex">
        <span class="inline-flex items-center px-3 rounded-l border bg-gray-100 text-gray-700">+63</span>
        <input type="text"
               name="contact_number"
               maxlength="10"
               pattern="\d{10}"
               placeholder="9123456789"
               class="w-full border rounded-r px-3 py-2">
    </div>
    <p class="text-xs text-gray-500 mt-1">Enter 10 digits only. Example: 9123456789</p>
</div>

{{-- ROLE --}}
<div>
    <label class="block text-sm font-semibold mb-1">Role *</label>
    <select name="role_id" class="w-full border rounded px-3 py-2">
        <option value="">-- Select Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
    </select>
</div>

{{-- SYSTEM ACCESS --}}
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
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
        @foreach($modules as $m)
            <label class="flex items-center gap-2">
                <input type="checkbox"
                       class="moduleCheckbox"
                       name="access[]"
                       value="{{ $m }}">
                {{ ucfirst($m) }}
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

    @foreach(['Elementary','Secondary','Paragames'] as $level)
        <label class="inline-flex items-center mr-4">
            <input type="checkbox" class="levelCheckbox" name="level_access[]" value="{{ $level }}">
            <span class="ml-1">{{ $level }}</span>
        </label>
    @endforeach
</div>

{{-- CATEGORY ACCESS --}}
<div id="categorySection">
    <label class="flex items-center gap-2 font-semibold mb-2">
        <input type="checkbox" id="selectAllCategories">
        Select All Categories
    </label>

    @foreach(['Boys','Girls'] as $cat)
        <label class="inline-flex items-center mr-4">
            <input type="checkbox" class="categoryCheckbox" name="category_access[]" value="{{ $cat }}">
            <span class="ml-1">{{ $cat }}</span>
        </label>
    @endforeach
</div>

{{-- SPORTS --}}
<div>
    <label class="flex items-center gap-2 font-semibold mb-2">
        <input type="checkbox" id="selectAllSports">
        Select All Sports
    </label>

    <div id="sportsContainer" class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">

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
                           name="sports_access[]" value="{{ $sport->id }}">

                    {{ $sport->name }}
                </label>
            @endforeach
        @endforeach
    </div>
</div>

{{-- SPORT TYPES --}}
<div>
    <label class="flex items-center gap-2 font-semibold mb-2">
        <input type="checkbox" id="selectAllSportTypes">
        Select All Sport Types
    </label>

    <div id="sportTypeContainer" class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">

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
                           name="sport_type_access[]" value="{{ $type->id }}">

                    {{ $type->type_name }}
                </label>
            @endforeach
        @endforeach
    </div>
</div>

{{-- ACTIONS --}}
<div class="flex justify-end gap-3 pt-4 border-t">
    <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded">Cancel</a>
    <button class="px-5 py-2 bg-green-600 text-white rounded">Create User</button>
</div>

</form>
</div>


{{-- ================= JS LOGIC ================= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    const levelCBs    = document.querySelectorAll('.levelCheckbox');
    const catCBs      = document.querySelectorAll('.categoryCheckbox');
    const sportItems  = document.querySelectorAll('.sportItem');
    const sportCBs    = document.querySelectorAll('.sportCheckbox');
    const sportTypes  = document.querySelectorAll('.sportTypeItem');

    const allLevels   = document.getElementById('selectAllLevels');
    const allCats     = document.getElementById('selectAllCategories');

    function getChecked(values) {
        return Array.from(values).filter(v => v.checked).map(v => v.value);
    }

    function filterSports() {
        const levels = getChecked(levelCBs);
        const cats   = getChecked(catCBs);

        const allLevelSelected = levels.length === 3; // Elementary, Secondary, Paragames
        const allCatSelected   = cats.length === 2;   // Boys, Girls

        sportItems.forEach(item => {
            const lvl = item.dataset.level;
            const cat = item.dataset.category;

            let show = false;

            // ✅ ALL LEVELS + ALL CATEGORIES → SHOW EVERYTHING
            if (allLevelSelected && allCatSelected) {
                show = true;
            }
            // ✅ Paragames now has categories
            else if (levels.includes(lvl) && cats.includes(cat)) {
                show = true;
            }

            item.style.display = show ? 'flex' : 'none';
            if (!show) item.querySelector('input').checked = false;
        });

        // ✅ HIDE EMPTY GROUP LABELS
        const sportContainer = document.getElementById('sportsContainer');
        const groupLabels = sportContainer.querySelectorAll('div.col-span-full');
        groupLabels.forEach(label => {
            const nextSiblings = [];
            let sibling = label.nextElementSibling;
            while (sibling && !sibling.classList.contains('col-span-full')) {
                nextSiblings.push(sibling);
                sibling = sibling.nextElementSibling;
            }
            const anyVisible = nextSiblings.some(s => s.style.display !== 'none');
            label.style.display = anyVisible ? 'block' : 'none';
        });

        filterSportTypes();
    }

    function filterSportTypes() {
        const selectedSports = getChecked(sportCBs);

        sportTypes.forEach(type => {
            const sid = type.dataset.sportId;
            const show = selectedSports.includes(sid);
            type.style.display = show ? 'flex' : 'none';
            if (!show) type.querySelector('input').checked = false;
        });

        // ✅ HIDE EMPTY SPORT TYPE GROUP LABELS
        const sportTypeContainer = document.getElementById('sportTypeContainer');
        const typeGroupLabels = sportTypeContainer.querySelectorAll('div.col-span-full');
        typeGroupLabels.forEach(label => {
            const nextSiblings = [];
            let sibling = label.nextElementSibling;
            while (sibling && !sibling.classList.contains('col-span-full')) {
                nextSiblings.push(sibling);
                sibling = sibling.nextElementSibling;
            }
            const anyVisible = nextSiblings.some(s => s.style.display !== 'none');
            label.style.display = anyVisible ? 'block' : 'none';
        });
    }

    levelCBs.forEach(cb => cb.addEventListener('change', filterSports));
    catCBs.forEach(cb => cb.addEventListener('change', filterSports));
    sportCBs.forEach(cb => cb.addEventListener('change', filterSportTypes));

    function bind(master, items) {
        master.addEventListener('change', () => {
            items.forEach(cb => {
                if (cb.closest('label').style.display !== 'none') {
                    cb.checked = master.checked;
                }
            });
        });
    }

    bind(document.getElementById('selectAllAccess'), document.querySelectorAll('.moduleCheckbox'));
    bind(document.getElementById('selectAllLevels'), levelCBs);
    bind(document.getElementById('selectAllCategories'), catCBs);
    bind(document.getElementById('selectAllSports'), sportCBs);
    bind(document.getElementById('selectAllSportTypes'), document.querySelectorAll('.sportTypeCheckbox'));

    // INITIAL LOAD
    filterSports();
});
</script>

@endsection