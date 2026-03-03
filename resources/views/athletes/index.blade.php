@extends('layouts.app')

@section('page-title', 'Athletes')

@section('content')
{{-- ================= INSTRUCTIONS / NOTES ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>View all registered athletes in the system, including their school, region, and assigned sport.</li>
        <li>Use the search box to find athletes by name, school, or sport.</li>
        <li>Apply filters for <strong>Division</strong>, <strong>Sex</strong>, <strong>Sport</strong>, or <strong>Level</strong> to narrow down the list.</li>
        <li>Click <strong>+ Add Athlete</strong> to register a new athlete manually, or use <strong>Upload CSV</strong> for bulk registration.</li>
        <li>Click <strong>Export ID PDF</strong> to generate ID cards for all listed athletes.</li>
        <li>Click on the QR image to view the athlete’s QR code and details in a modal.</li>
    </ul>
</div>

<div class="flex justify-between items-center mb-4">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of participating athletes registered in the system
        </p>

        {{-- ATHLETE COUNT --}}
        <p class="text-sm text-gray-700 mt-1 font-semibold">
            Total Athletes: {{ $athleteCount }}
        </p>
    </div>

    @php
    use Illuminate\Support\Str;
    $user = auth()->user();
    $isDivisionOfficial = Str::contains(strtolower(auth()->user()->role), 'division official');
    $isSuperAdmin = $user->role && $user->role->name === 'Super Admin';
@endphp

<div class="flex gap-2 items-center">

    {{-- ADD ATHLETE (hidden for Division Official) --}}
    @if(!$isDivisionOfficial)
        <a href="{{ route('athletes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Add Athlete
        </a>
    @endif

    {{-- BULK CSV UPLOAD (hidden for Division Official) --}}
    @if(!$isDivisionOfficial)
        <form action="{{ route('athletes.bulkUpload') }}"
              method="POST"
              enctype="multipart/form-data"
              class="flex items-center gap-2">
            @csrf

            <input type="file"
                   name="file"
                   accept=".csv,.xlsx"
                   required
                   class="border rounded px-2 py-1 text-sm">

            <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded shadow">
                Upload CSV
            </button>
        </form>
    @endif

    {{-- EXPORT ID PDF --}}
<button onclick="openExportModal()"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow inline-flex items-center gap-2">
    Export ID PDF
</button>

{{-- EXPORT MODAL --}}
<div id="exportModal"
     class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">

        <button onclick="closeExportModal()"
                class="absolute top-3 right-3 text-xl text-gray-500 hover:text-black">
            &times;
        </button>

        <h3 class="text-lg font-bold mb-2">Export Athlete ID Cards</h3>

        <p class="text-sm text-gray-600 mb-4">
            ⚠️ Exporting many athletes may slow down the system.
        </p>

        {{-- ================= EXPORT ALL ================= --}}
        <a href="{{ route('athletes.exportIdPdf') }}"
           class="block w-full text-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded mb-3">
            Export ALL Athletes
        </a>

        <hr class="my-4">

        {{-- ================= EXPORT WITH LIMIT ================= --}}
        <form method="GET" action="{{ route('athletes.exportIdPdf') }}" class="space-y-3 mb-4">
            <label class="text-sm font-medium">
                Export first X athletes
            </label>

            <input type="number"
                   name="limit"
                   min="1"
                   max="{{ $athleteCount }}"
                   value="50"
                   class="w-full border rounded px-3 py-2"
                   required>

            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                Export With Limit
            </button>
        </form>

        {{-- ================= SUPER ADMIN ONLY ================= --}}
        @if($isSuperAdmin)

        <hr class="my-4">

        <h4 class="font-semibold text-sm mb-2 text-gray-700">
            Export by Division (Super Admin Only)
        </h4>

        <form method="GET" action="{{ route('athletes.exportIdPdf') }}" class="space-y-3">

            {{-- Select Division --}}
            <select name="school_division"
                    class="w-full border rounded px-3 py-2"
                    required>
                <option value="">Select Division</option>
                @foreach($divisions as $division)
                    <option value="{{ $division }}">
                        SDO - {{ $division }}
                    </option>
                @endforeach
            </select>

            {{-- Optional Limit --}}
            <input type="number"
                   name="limit"
                   min="1"
                   placeholder="Optional Limit (leave blank for all)"
                   class="w-full border rounded px-3 py-2">

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Export Selected Division
            </button>

        </form>

        @endif

    </div>
</div>

{{-- EXPORT MODAL SCRIPT --}}
<script>
function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
    document.getElementById('exportModal').classList.add('flex');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
    document.getElementById('exportModal').classList.remove('flex');
}
</script>

</div>
</div>

{{-- SEARCH + FILTER --}}
<form method="GET" action="{{ route('athletes.index') }}"
      class="mb-4 bg-white p-4 rounded shadow flex flex-wrap gap-3 items-end">

    {{-- SEARCH --}}
    <div class="flex-1 min-w-[200px]">
        <label class="text-xs text-gray-500">Search</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Name, school, sport..."
               class="w-full border rounded px-3 py-2">
    </div>

    {{-- FILTER SCHOOL DIVISION (SDO) --}}
<div class="min-w-[200px]">
    <label class="text-xs text-gray-500">Division</label>
    <select name="school_division" class="w-full border rounded px-3 py-2">
        <option value="">All SDOs</option>
        @foreach($divisions as $division)
            <option value="{{ $division }}"
                {{ request('school_division') == $division ? 'selected' : '' }}>
                SDO - {{ $division }}
            </option>
        @endforeach
    </select>
</div>


    {{-- FILTER SEX --}}
    <div class="min-w-[150px]">
        <label class="text-xs text-gray-500">Sex</label>
        <select name="sex" class="w-full border rounded px-3 py-2">
            <option value="">All</option>
            <option value="Male" {{ request('sex') == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ request('sex') == 'Female' ? 'selected' : '' }}>Female</option>
        </select>
    </div>

    {{-- FILTER SPORT --}}
    <div class="min-w-[200px]">
        <label class="text-xs text-gray-500">Sport</label>
        <select name="sport" class="w-full border rounded px-3 py-2">
            <option value="">All Sports</option>
            @foreach($sports as $sport)
                <option value="{{ $sport->id }}"
                    {{ request('sport') == $sport->id ? 'selected' : '' }}>
                    {{ $sport->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- FILTER LEVEL (DIVISION) --}}
    <div class="min-w-[180px]">
        <label class="text-xs text-gray-500">Level</label>
        <select name="division" class="w-full border rounded px-3 py-2">
            <option value="">All Levels</option>
            <option value="Elementary" {{ request('division') == 'Elementary' ? 'selected' : '' }}>
                Elementary
            </option>
            <option value="Secondary" {{ request('division') == 'Secondary' ? 'selected' : '' }}>
                Secondary
            </option>
            <option value="Paragames" {{ request('division') == 'Paragames' ? 'selected' : '' }}>
                Paragames
            </option>
        </select>
    </div>

    {{-- BUTTONS --}}
    <div class="flex gap-2">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Apply
        </button>

        <a href="{{ route('athletes.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded shadow">
            Reset
        </a>
    </div>
</form>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white border rounded shadow overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-gray-100">
<tr>
    <th class="px-4 py-2 text-left">Profile</th>
    <th class="px-4 py-2 text-left">Name</th>
    <th class="px-4 py-2 text-left">Sex</th>
    <th class="px-4 py-2 text-left">Birthday</th>
    <th class="px-4 py-2 text-left">Level</th>
    <th class="px-4 py-2 text-left">Category</th>
    <th class="px-4 py-2 text-left">Division</th>
    <th class="px-4 py-2 text-left">Event</th>
    <th class="px-4 py-2 text-left">Municipality/Town/City</th>
    <th class="px-4 py-2 text-left">School</th>
    <th class="px-4 py-2 text-left">School Type</th>
    <th class="px-4 py-2 text-left">School Address</th>
    <th class="px-4 py-2 text-left">School Code</th>
    <th class="px-4 py-2 text-left">LRN</th>
    <th class="px-4 py-2 text-left">Status</th>
    <th class="px-4 py-2 text-left">QR</th>
    <th class="px-4 py-2 text-right">Actions</th>
</tr>
</thead>

<tbody class="divide-y">
@forelse($athletes as $athlete)
<tr class="hover:bg-gray-50">
    {{-- PROFILE --}}
    <td class="px-4 py-2">
        @if($athlete->profile_photo)
            <img src="{{ asset('storage/'.$athlete->profile_photo) }}"
                 class="w-10 h-10 rounded-full object-cover border">
        @else
            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center font-bold">
                {{ strtoupper(substr($athlete->last_name,0,1)) }}
            </div>
        @endif
    </td>

    {{-- NAME --}}
    <td class="px-4 py-2 font-medium">
        {{ $athlete->last_name }}, {{ $athlete->first_name }}
        @if($athlete->middle_initial)
            {{ $athlete->middle_initial }}
        @endif
    </td>

    <td class="px-4 py-2">{{ $athlete->sex ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->birthdate ? \Carbon\Carbon::parse($athlete->birthdate)->format('M. d, Y') : '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->division }}</td>
    <td class="px-4 py-2">{{ $athlete->category }}</td>
    <td class="px-4 py-2">SDO - {{ $athlete->school_division ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->event ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->municipality ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->school->name ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->school_type ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->school_address ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->school_code ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->lrn ?? '—' }}</td>
    <td class="px-4 py-2 font-semibold">{{ $athlete->status }}</td>

    {{-- QR --}}
    <td class="px-4 py-2">
        <img src="{{ route('athletes.qr',$athlete->id) }}"
             class="w-14 h-14 border rounded cursor-pointer"
             onclick="openQrModal(
                '{{ route('athletes.qr',$athlete->id) }}',
                '{{ $athlete->last_name }}, {{ $athlete->first_name }}',
                '{{ $athlete->school->name ?? '' }}',
                '{{ $athlete->qr_code }}'
             )">
    </td>

    {{-- ACTIONS --}}
    <td class="px-4 py-2 text-right whitespace-nowrap">
        <a href="{{ route('athletes.edit',$athlete->id) }}"
           class="text-blue-600 hover:underline mr-2">Edit</a>

        <form action="{{ route('athletes.destroy',$athlete->id) }}"
              method="POST" class="inline"
              onsubmit="return confirm('Delete this athlete?')">
            @csrf
            @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="17" class="text-center py-6 text-gray-500">
        No athletes found.
    </td>
</tr>
@endforelse
</tbody>
</table>
</div>

{{-- Pagination links --}}
<div class="mt-4">
    {{ $athletes->links() }}
</div>

{{-- QR MODAL --}}
<div id="qrModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
<div class="bg-white p-6 rounded w-80 relative">
    <button onclick="closeQrModal()" class="absolute top-2 right-2 text-xl">&times;</button>
    <h3 id="qrName" class="font-semibold text-center"></h3>
    <p id="qrSchool" class="text-sm text-center text-gray-500"></p>
    <img id="qrImage" class="w-64 h-64 mx-auto my-3 border">
    <p id="qrValue" class="text-xs text-center text-gray-500"></p>
</div>
</div>

<script>
function openQrModal(src,name,school,value){
    qrModal.classList.remove('hidden');
    qrModal.classList.add('flex');
    qrImage.src = src;
    qrName.innerText = name;
    qrSchool.innerText = school;
    qrValue.innerText = 'QR Value: ' + value;
}
function closeQrModal(){
    qrModal.classList.add('hidden');
    qrModal.classList.remove('flex');
}
</script>

@endsection
