@extends('layouts.app')

@section('page-title', 'Add User')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 w-full">

<form action="{{ route('users.store') }}" method="POST" class="space-y-6">

@if ($errors->any())
    <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50 text-red-700">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <strong class="font-semibold">Whoops! Something went wrong:</strong>
        </div>
        <ul class="list-disc list-inside text-sm space-y-1 ml-6">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@csrf

{{-- BASIC INFO --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    </div>
</div>

{{-- PASSWORD SECTION --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    {{-- PASSWORD --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Password <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-2">
            <div class="relative flex-1">
                <input type="password" id="password" name="password" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-10">
                <button type="button" id="togglePassword" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <button type="button" id="defaultPassword" 
                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition shadow-sm">
                Default
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-1.5">Default password: ESRRS@2026</p>
    </div>

    {{-- CONFIRM PASSWORD --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Confirm Password <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-10">
            <button type="button" id="togglePasswordConfirm" 
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- CONTACT NUMBER --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Number</label>
    <div class="flex">
        <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 text-gray-600 text-sm">+63</span>
        <input type="text"
               name="contact_number"
               maxlength="10"
               pattern="\d{10}"
               placeholder="9123456789"
               class="w-full border border-gray-200 rounded-r-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    </div>
    <p class="text-xs text-gray-500 mt-1.5">Enter 10 digits only. Example: 9123456789</p>
</div>

{{-- ROLE --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
    <select name="role_id" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
        <option value="">-- Select Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-2">Or add a new role below:</p>
    <input type="text"
           name="new_role"
           placeholder="Enter new role"
           class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
</div>

{{-- DIVISIONS FOR BILLETING QUARTERS --}}
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
            <div class="division-item">
                <select name="divisions[]" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                    <option value="">-- Select Division --</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
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
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-sm">
        @foreach($modules as $m)
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox"
                       class="moduleCheckbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                       name="access[]"
                       value="{{ $m }}">
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
                <input type="checkbox" class="levelCheckbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" name="level_access[]" value="{{ $level }}">
                <span class="text-gray-700">{{ $level }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- CATEGORY ACCESS --}}
<div id="categorySection" class="bg-gray-50 rounded-xl p-5">
    <div class="mb-3">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAllCategories" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm font-medium text-gray-700">Select All Categories</span>
        </label>
    </div>

    <div class="flex flex-wrap gap-4">
        @foreach(['Boys','Girls','Mixed'] as $cat)
    <label class="inline-flex items-center gap-2 cursor-pointer">
        <input type="checkbox"
               class="categoryCheckbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
               name="category_access[]"
               value="{{ $cat }}">
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
                           name="sports_access[]" value="{{ $sport->id }}">

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
                           name="sport_type_access[]" value="{{ $type->id }}">

                    <span class="text-gray-700">{{ $type->type_name }}</span>
                </label>
            @endforeach
        @endforeach
    </div>
</div>

{{-- ACTIONS --}}
<div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
    <a href="{{ route('users.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">Cancel</a>
    <button class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl transition shadow-sm">Create User</button>
</div>

</form>
</div>


{{-- ================= JS LOGIC ================= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Password toggle functionality
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const defaultPasswordBtn = document.getElementById('defaultPassword');

    if (togglePassword) {
        togglePassword.addEventListener('click', () => {
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
        });
    }

    if (togglePasswordConfirm) {
        togglePasswordConfirm.addEventListener('click', () => {
            const type = passwordConfirm.type === 'password' ? 'text' : 'password';
            passwordConfirm.type = type;
        });
    }

    if (defaultPasswordBtn) {
        defaultPasswordBtn.addEventListener('click', () => {
            const defaultPwd = 'ESRRS@2026';
            password.value = defaultPwd;
            passwordConfirm.value = defaultPwd;
        });
    }

    // Level and category filtering
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

        const allLevelSelected = levels.length === 3;
        const allCatSelected   = cats.length === 3;

        sportItems.forEach(item => {
            const lvl = item.dataset.level;
            const cat = item.dataset.category;

            let show = false;

            if (allLevelSelected && allCatSelected) {
                show = true;
            }
            else if (levels.includes(lvl) && cats.includes(cat)) {
                show = true;
            }

            item.style.display = show ? 'inline-flex' : 'none';
            if (!show) item.querySelector('input').checked = false;
        });

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
            type.style.display = show ? 'inline-flex' : 'none';
            if (!show) type.querySelector('input').checked = false;
        });

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

    // ================= DYNAMIC DIVISIONS FOR BILLETING QUARTERS =================
const roleSelect = document.querySelector('select[name="role_id"]');
const divisionSection = document.getElementById('divisionSection');
const divisionContainer = document.getElementById('divisionContainer');
const addDivisionBtn = document.getElementById('addDivisionBtn');

function showDivisionSection() {
    if (!roleSelect) return;
    roleSelect.addEventListener('change', () => {
        const selectedText = roleSelect.options[roleSelect.selectedIndex].text;
        if (selectedText === 'Billeting Quarters') {
            divisionSection.style.display = 'block';
        } else {
            divisionSection.style.display = 'none';
            // Optional: remove extra selects when role changes
            const selects = divisionContainer.querySelectorAll('select[name="divisions[]"]');
            selects.forEach((s, i) => {
                if (i > 0) s.closest('div').remove();
            });
            // Reset first select
            selects[0].value = '';
        }
    });
}

// Add new division dropdown
if (addDivisionBtn) {
    addDivisionBtn.addEventListener('click', () => {
        const firstSelect = divisionContainer.querySelector('select[name="divisions[]"]');
        if (!firstSelect) return;

        const newDiv = document.createElement('div');
        newDiv.classList.add('flex', 'gap-2', 'items-center');

        const clonedSelect = firstSelect.cloneNode(true);
        clonedSelect.value = ''; // reset value

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Remove';
        removeBtn.className = 'px-3 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition text-sm';
        removeBtn.addEventListener('click', () => newDiv.remove());

        newDiv.appendChild(clonedSelect);
        newDiv.appendChild(removeBtn);
        divisionContainer.appendChild(newDiv);
    });
}

showDivisionSection();
    

    bind(document.getElementById('selectAllAccess'), document.querySelectorAll('.moduleCheckbox'));
    bind(document.getElementById('selectAllLevels'), levelCBs);
    bind(document.getElementById('selectAllCategories'), catCBs);
    bind(document.getElementById('selectAllSports'), sportCBs);
    bind(document.getElementById('selectAllSportTypes'), document.querySelectorAll('.sportTypeCheckbox'));

    filterSports();
    
});
</script>
<script>
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
    toggleDivisionSection(); // initial check

    addDivisionBtn.addEventListener('click', () => {
        const newSelect = divisionContainer.children[0].cloneNode(true);
        newSelect.value = ''; // reset selection
        divisionContainer.appendChild(newSelect);
    });
});
</script>

@endsection