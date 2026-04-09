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
       class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center gap-2 font-medium text-sm sm:text-base">
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Athlete
    </a>
@endif

{{-- BULK CSV UPLOAD (hidden for Division Official) --}}
@if(!$isDivisionOfficial)
    <form action="{{ route('athletes.bulkUpload') }}"
          method="POST"
          enctype="multipart/form-data"
          class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
        @csrf

        <div class="relative flex-1">
            <input type="file"
                   name="file"
                   accept=".csv,.xlsx"
                   required
                   id="csvFileInput"
                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <div class="w-full border-2 border-dashed border-gray-300 hover:border-purple-400 rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-sm text-gray-500 bg-gray-50 hover:bg-purple-50 transition-all duration-200 cursor-pointer flex items-center justify-between gap-2">
                <span id="fileNameDisplay" class="truncate text-gray-500">Choose CSV or Excel file</span>
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
            </div>
        </div>

        <button type="submit"
                class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center justify-center gap-2 font-medium text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Upload CSV
        </button>
    </form>

    <style>
        /* File name display update on selection */
        #csvFileInput:focus + div {
            border-color: #9b4dff;
            box-shadow: 0 0 0 3px rgba(155, 77, 255, 0.1);
        }
    </style>

    <script>
        // Update file name display when a file is selected
        const fileInput = document.getElementById('csvFileInput');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const fileNameDisplay = document.getElementById('fileNameDisplay');
                if (this.files && this.files.length > 0) {
                    const fileName = this.files[0].name;
                    const fileSize = (this.files[0].size / 1024).toFixed(1);
                    fileNameDisplay.innerHTML = `<span class="text-purple-600 font-medium">${fileName}</span> <span class="text-gray-400 text-xs">(${fileSize} KB)</span>`;
                    fileNameDisplay.classList.add('text-purple-600');
                } else {
                    fileNameDisplay.innerHTML = 'Choose CSV or Excel file';
                    fileNameDisplay.classList.remove('text-purple-600');
                }
            });
        }
    </script>
@endif

   {{-- EXPORT ID PDF --}}
<button onclick="openExportModal()"
        class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center gap-2 font-medium text-sm sm:text-base">
    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    Export ID PDF
</button>

{{-- EXPORT MODAL --}}
<div id="exportModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 transition-all duration-300 p-2 sm:p-4">

    <div class="bg-white w-full max-w-7xl rounded-2xl shadow-2xl p-4 sm:p-6 md:p-8 relative flex flex-col lg:flex-row gap-6 lg:gap-8 animate-fade-in-up max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">

        {{-- CLOSE BUTTON - STICKY ON SCROLL --}}
        <button onclick="closeExportModal()"
                class="sticky top-0 ml-auto w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition-colors duration-200 z-20 float-right mb-2 lg:absolute lg:top-5 lg:right-5 lg:float-none lg:mb-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="clear-both lg:clear-none"></div>

        {{-- ================= LEFT SIDE ================= --}}
        <div class="w-full lg:w-1/2">

            <div class="mb-4 sm:mb-6">
                <h3 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Export Athlete ID Cards</h3>
                <p class="text-xs sm:text-sm text-amber-600 mt-2 flex items-center gap-1">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Exporting many athletes may slow down the system
                </p>
            </div>

            {{-- ================= SEARCH ATHLETE ================= --}}
            <div class="mb-4 sm:mb-6">
                <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1">Search Athlete</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text"
                           id="athleteSearch"
                           placeholder="Type first or last name..."
                           class="w-full border border-gray-200 rounded-xl px-8 sm:px-10 py-2 sm:py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                </div>

                <div id="searchResults"
                     class="border border-gray-100 rounded-xl mt-2 max-h-40 sm:max-h-48 overflow-y-auto hidden bg-white shadow-lg z-10">
                </div>
            </div>

            {{-- EXPORT SELECTED ATHLETE --}}
            <form method="GET" action="{{ route('athletes.exportIdPdf') }}">
                <input type="hidden" name="athlete_id" id="selectedAthleteId">

                <button type="submit"
                        class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-4 py-2 sm:py-2.5 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg mb-3 sm:mb-4 text-sm sm:text-base">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Export Selected Athlete
                    </span>
                </button>
            </form>

            <div class="relative my-4 sm:my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs sm:text-sm">
                    <span class="px-3 bg-white text-gray-500">or</span>
                </div>
            </div>

            {{-- EXPORT ALL --}}
            <a href="{{ route('athletes.exportIdPdf') }}"
               class="block w-full text-center bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 sm:py-2.5 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg mb-3 sm:mb-4 text-sm sm:text-base">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export ALL Athletes
                </span>
            </a>

            <div class="relative my-4 sm:my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs sm:text-sm">
                    <span class="px-3 bg-white text-gray-500">range</span>
                </div>
            </div>

            {{-- RANGE --}}
            <form method="GET" action="{{ route('athletes.exportIdPdf') }}" class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">

                <label class="text-xs sm:text-sm font-semibold text-gray-700 block">
                    Export by Athlete Number Range
                </label>

                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                    <div>
                        <label class="text-[10px] sm:text-xs text-gray-500 block mb-1">Start</label>
                        <input type="number" name="start" min="1" value="1"
                               class="w-full border border-gray-200 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="text-[10px] sm:text-xs text-gray-500 block mb-1">End</label>
                        <input type="number" name="end" min="1" value="10"
                               class="w-full border border-gray-200 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white px-4 py-2 sm:py-2.5 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg text-sm sm:text-base">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Export Athlete Range
                    </span>
                </button>

            </form>

          {{-- SUPER ADMIN --}}
@if($isSuperAdmin)

<div class="relative my-4 sm:my-6">
    <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-gray-200"></div>
    </div>
    <div class="relative flex justify-center text-xs sm:text-sm">
        <span class="px-3 bg-white text-gray-500">advanced filters</span>
    </div>
</div>

{{-- TOTAL COUNT DISPLAY --}}
<div id="totalCountContainer" 
     class="mb-4 p-3 sm:p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100 hidden">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="text-sm sm:text-base font-semibold text-gray-700">Matching Athletes:</span>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-blue-600" id="athleteTotalCount">0</div>
    </div>
    <p class="text-xs text-gray-500 mt-1 ml-7 sm:ml-8">Based on your selected filters</p>
</div>

<form method="GET" action="{{ route('athletes.exportIdPdf') }}" class="space-y-3 sm:space-y-4 pb-4" id="advancedFilterForm">

    {{-- ================= DIVISION ================= --}}
    <div>
        <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1">Division</label>
        <select name="school_division"
                id="divisionSelect"
                class="filter-select w-full border border-gray-200 rounded-xl px-3 py-2 sm:py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
            <option value="">Select Division</option>
            @foreach($divisions as $division)
                <option value="{{ $division }}">
                    SDO - {{ $division }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- ================= LEVEL (ALWAYS VISIBLE) ================= --}}
    <div>
        <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1">Level</label>
        <select name="level"
                id="levelSelect"
                class="filter-select w-full border border-gray-200 rounded-xl px-3 py-2 sm:py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
            <option value="">Select Level</option>
            <option value="Elementary">Elementary</option>
            <option value="Secondary">Secondary</option>
            <option value="Paragames">Paragames</option>
        </select>
    </div>

    {{-- ================= EVENT (DYNAMIC) ================= --}}
    <div>
        <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1">Event</label>
        <select name="event"
                id="eventSelect"
                class="filter-select w-full border border-gray-200 rounded-xl px-3 py-2 sm:py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white hidden">
            <option value="">Select Event</option>
        </select>
    </div>

    {{-- ================= CATEGORY ================= --}}
    <div>
        <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1">Category</label>
        <select name="category"
                id="categorySelect"
                class="filter-select w-full border border-gray-200 rounded-xl px-3 py-2 sm:py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white hidden">
            <option value="">Select Category</option>
            <option value="Boys">Boys</option>
            <option value="Girls">Girls</option>
        </select>
    </div>

    {{-- ================= RANGE ================= --}}
    <div id="rangeContainer" class="hidden">
        <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1">Athlete Number Range</label>
        <div class="grid grid-cols-2 gap-2 sm:gap-3">
            <input type="number" name="start" min="1" id="rangeStart"
                   placeholder="Start"
                   class="range-input w-full border border-gray-200 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <input type="number" name="end" min="1" id="rangeEnd"
                   placeholder="End"
                   class="range-input w-full border border-gray-200 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
        </div>
    </div>

    <button type="submit"
            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 sm:py-2.5 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg text-sm sm:text-base">
        <span class="flex items-center justify-center gap-2">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Export Filtered IDs
        </span>
    </button>

</form>

@endif

        </div>

        {{-- ================= RIGHT SIDE (REAL ID PREVIEW - FULL SIZE) ================= --}}
<div class="w-full lg:w-1/2 flex flex-col bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 sticky top-4 self-start">

    {{-- HEADER --}}
    <div class="mb-4">
        <h4 class="font-bold text-gray-700 flex items-center gap-2 text-base">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Live ID Preview
        </h4>
    </div>

    {{-- FULL SIZE PREVIEW CONTAINER --}}
    <div id="previewCard"
         class="w-full h-full min-h-[420px] bg-white rounded-2xl shadow-xl flex items-center justify-center p-6 transition-all duration-300 border border-gray-100">

        {{-- EMPTY STATE --}}
        <div class="text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-sm">Select an athlete to preview ID card</p>
        </div>

    </div>

</div>

    </div>
</div>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-up {
    animation: fadeInUp 0.3s ease-out;
}

/* Custom scrollbar for better mobile experience */
#searchResults::-webkit-scrollbar {
    width: 4px;
}

#searchResults::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#searchResults::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

#searchResults::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Ensure modal content is fully scrollable on mobile */
@media (max-width: 1023px) {
    .animate-fade-in-up {
        max-height: 95vh;
        overflow-y: auto;
    }
}

/* Better touch scrolling on mobile */
@media (max-width: 640px) {
    .animate-fade-in-up {
        -webkit-overflow-scrolling: touch;
    }
}
</style>

{{-- ================= SCRIPT ================= --}}
<script>
function openExportModal() {
    const modal = document.getElementById('exportModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeExportModal() {
    const modal = document.getElementById('exportModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

/* Close modal on escape key */
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('exportModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeExportModal();
        }
    }
});

/* Close modal when clicking outside */
document.getElementById('exportModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeExportModal();
    }
});

/* ================= AJAX SEARCH (FULL DATABASE) ================= */
let typingTimer;
const searchInput = document.getElementById('athleteSearch');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        let query = this.value.trim();
        let resultsBox = document.getElementById('searchResults');

        clearTimeout(typingTimer);

        if (query.length < 2) {
            resultsBox.classList.add('hidden');
            resultsBox.innerHTML = '';
            return;
        }

        typingTimer = setTimeout(() => {
            fetch(`/athletes/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    resultsBox.innerHTML = '';

                    if (data.length === 0) {
                        resultsBox.innerHTML = `
                            <div class="px-3 sm:px-4 py-2 sm:py-3 text-gray-500 text-xs sm:text-sm text-center">
                                No results found
                            </div>
                        `;
                        resultsBox.classList.remove('hidden');
                        return;
                    }

                    data.forEach(a => {
                        let item = document.createElement('div');
                        item.className = 'px-3 sm:px-4 py-2 sm:py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 transition-colors duration-150';

                        item.innerHTML = `
                            <div class="font-medium text-gray-800 text-sm sm:text-base">
                                ${a.last_name}, ${a.first_name} ${a.middle_initial ?? ''}
                            </div>
                            <div class="text-[10px] sm:text-xs text-gray-500 mt-0.5">
                                ${a.school?.name ?? 'No School'}
                            </div>
                        `;

                        item.onclick = function () {
                            selectAthlete(a);
                            resultsBox.classList.add('hidden');
                            document.getElementById('athleteSearch').value = `${a.last_name}, ${a.first_name}`;
                        };

                        resultsBox.appendChild(item);
                    });

                    resultsBox.classList.remove('hidden');
                });
        }, 300);
    });
}

/* ================= SELECT ATHLETE + REAL PREVIEW (FIXED) ================= */
function selectAthlete(a) {
    document.getElementById('selectedAthleteId').value = a.id;

    console.log('Selected Athlete:', a); // DEBUG

    // ================= SAFE VALUES =================
    let fullName = `${a.last_name ?? ''}, ${a.first_name ?? ''} ${a.middle_initial ?? ''}`;
    let school = a.school?.name ?? 'No School';
    let division = a.school_division ?? '—';
    let event = a.event ?? '—'; // ✅ FIXED (string column)

    let photo = a.profile_photo
        ? `/storage/${a.profile_photo}`
        : null;

    let qrUrl = `/athletes/${a.id}/qr`;

    const previewCard = document.getElementById('previewCard');

    previewCard.innerHTML = `
        <div class="flex gap-4 justify-center w-full">

            <!-- FRONT -->
            <div class="w-[150px] h-[210px] relative rounded-xl overflow-hidden shadow-lg"
                style="background-image:url('/storage/id_templates/id-template-front.png'); background-size:cover;">

                ${photo ? `
                <img src="${photo}"
                    class="absolute top-10 left-1/2 transform -translate-x-1/2 w-12 h-16 object-cover rounded-md border-2 border-white">
                ` : ''}

                <div class="absolute bottom-14 w-full text-center">
                    <p class="font-bold text-xs text-blue-900 px-1 truncate">
                        ${fullName}
                    </p>
                </div>

                <div class="absolute bottom-9 w-full text-center">
                    <p class="text-[10px] text-blue-800 px-1 truncate">
                        ${school}
                    </p>
                </div>

                <div class="absolute bottom-5 w-full text-center">
                    <p class="text-[9px] text-blue-800 px-1 truncate">
                        SDO - ${division}
                    </p>
                </div>

                <div class="absolute bottom-0 w-full text-center bg-blue-900/80 py-1">
                    <p class="text-[10px] font-bold text-white uppercase truncate">
                        ${event}
                    </p>
                </div>

            </div>

            <!-- BACK -->
            <div class="w-[150px] h-[210px] relative rounded-xl overflow-hidden shadow-lg"
                style="background-image:url('/storage/id_templates/id-template-back.png'); background-size:cover;">

                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <img src="${qrUrl}"
                        class="w-16 h-16 rounded-lg shadow-md">
                    <div class="mt-2 text-[8px] font-mono text-blue-800 bg-white/80 px-2 py-1 rounded-full">
                        ${a.qr_code ?? ''}
                    </div>
                </div>

            </div>

        </div>
    `;
}


// Handle window resize for preview responsiveness
window.addEventListener('resize', function() {
    const selectedId = document.getElementById('selectedAthleteId')?.value;
    if (selectedId) {
        const searchValue = document.getElementById('athleteSearch')?.value;
        if (searchValue) {
            setTimeout(() => {
                const event = new Event('input');
                document.getElementById('athleteSearch')?.dispatchEvent(event);
            }, 100);
        }
    }
});
</script>
<script>
/* ================= DIVISION → EVENTS ================= */
document.getElementById('divisionSelect')?.addEventListener('change', function () {

    let division = this.value;
    let eventSelect = document.getElementById('eventSelect');

    if (!division) {
        eventSelect.classList.add('hidden');
        return;
    }

    eventSelect.innerHTML = '<option value="">Loading events...</option>';
    eventSelect.classList.remove('hidden');

    fetch(`/athletes/events-by-division?division=${encodeURIComponent(division)}`)
        .then(res => res.json())
        .then(data => {

            eventSelect.innerHTML = '<option value="">Select Event</option>';

            if (data.length === 0) {
                eventSelect.innerHTML = '<option value="">No events found</option>';
                return;
            }

            data.forEach(event => {
                let opt = document.createElement('option');
                opt.value = event;
                opt.textContent = event;
                eventSelect.appendChild(opt);
            });

        });

});

/* ================= EVENT → SHOW CATEGORY ================= */
document.getElementById('eventSelect')?.addEventListener('change', function () {

    let categorySelect = document.getElementById('categorySelect');

    if (this.value) {
        categorySelect.classList.remove('hidden');
    } else {
        categorySelect.classList.add('hidden');
    }

});


/* ================= CATEGORY → SHOW RANGE ================= */
document.getElementById('categorySelect')?.addEventListener('change', function () {

    let rangeContainer = document.getElementById('rangeContainer');

    if (this.value) {
        rangeContainer.classList.remove('hidden');
    } else {
        rangeContainer.classList.add('hidden');
    }

});
</script>
<script>
    /* ================= FETCH TOTAL ATHLETE COUNT BASED ON FILTERS ================= */
function fetchTotalCount() {
    // Get all filter values
    const division = document.getElementById('divisionSelect')?.value || '';
    const level = document.getElementById('levelSelect')?.value || '';
    const event = document.getElementById('eventSelect')?.value || '';
    const category = document.getElementById('categorySelect')?.value || '';
    const start = document.getElementById('rangeStart')?.value || '';
    const end = document.getElementById('rangeEnd')?.value || '';
    
    // Check if any filter is selected (at least division or level)
    const hasFilters = division || level || event || category || (start && end);
    
    if (!hasFilters) {
        document.getElementById('totalCountContainer')?.classList.add('hidden');
        return;
    }
    
    // Build query parameters
    const params = new URLSearchParams();
    if (division) params.append('school_division', division);
    if (level) params.append('level', level);
    if (event) params.append('event', event);
    if (category) params.append('category', category);
    if (start && end) {
        params.append('start', start);
        params.append('end', end);
    }
    
    // Show loading state
    const totalCountElement = document.getElementById('athleteTotalCount');
    if (totalCountElement) {
        totalCountElement.innerHTML = '<div class="animate-pulse">...</div>';
    }
    document.getElementById('totalCountContainer')?.classList.remove('hidden');
    
    // Fetch the count
    fetch(`/athletes/filter-count?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            const count = data.count || 0;
            const totalCountElement = document.getElementById('athleteTotalCount');
            if (totalCountElement) {
                totalCountElement.innerHTML = count;
                totalCountElement.className = 'text-2xl sm:text-3xl font-bold ' + 
                    (count > 0 ? 'text-blue-600' : 'text-gray-400');
            }
            
            // Optional: Show warning if count is too high
            const container = document.getElementById('totalCountContainer');
            if (count > 500) {
                container?.classList.add('border-amber-200', 'bg-amber-50');
                container?.classList.remove('border-blue-100', 'bg-blue-50');
            } else {
                container?.classList.add('border-blue-100', 'bg-blue-50');
                container?.classList.remove('border-amber-200', 'bg-amber-50');
            }
        })
        .catch(err => {
            console.error('Error fetching count:', err);
            const totalCountElement = document.getElementById('athleteTotalCount');
            if (totalCountElement) {
                totalCountElement.innerHTML = 'Error';
                totalCountElement.className = 'text-2xl sm:text-3xl font-bold text-red-500';
            }
        });
}

// Debounce function to limit API calls
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Attach event listeners to all filter selects and inputs
const filterSelects = document.querySelectorAll('.filter-select');
const rangeInputs = document.querySelectorAll('.range-input');

const debouncedFetchCount = debounce(fetchTotalCount, 300);

filterSelects.forEach(select => {
    if (select) {
        select.addEventListener('change', debouncedFetchCount);
    }
});

rangeInputs.forEach(input => {
    if (input) {
        input.addEventListener('input', debouncedFetchCount);
    }
});

// Also trigger count when events are loaded dynamically
const originalEventSelectHandler = document.getElementById('eventSelect')?.onchange;
if (originalEventSelectHandler) {
    const eventSelect = document.getElementById('eventSelect');
    eventSelect.addEventListener('change', debouncedFetchCount);
}

// Initial fetch if any filters have default values
setTimeout(() => {
    fetchTotalCount();
}, 500);
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

{{-- ================= MISSING ATHLETES ================= --}}
@if(session('missing_athletes') && count(session('missing_athletes')) > 0)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
        <strong>⚠️ The following athletes were NOT registered in the system:</strong>

        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach(session('missing_athletes') as $name)
                <li>{{ $name }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- IMPORT ERRORS --}}
@if(session('import_errors'))
    <div class="bg-red-100 border border-red-400 text-red-700 p-4 mb-4 rounded">
        <strong>Import Issues:</strong>
        <ul class="list-disc pl-5">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- PREVIEW --}}
@if(session('preview_data'))
    <div class="bg-gray-100 p-4 rounded mb-4">
        <strong>Preview (First 10 Rows)</strong>

        @php
            $preview = collect(session('preview_data'))->map(function ($row) {
                return is_array($row) ? $row : $row->toArray();
            });
        @endphp

        <table class="w-full mt-2 border">
            <thead>
                <tr class="bg-gray-200">
                    @foreach(array_keys($preview->first()) as $header)
                        <th class="border px-2 py-1">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($preview as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td class="border px-2 py-1">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
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
    <th class="px-4 py-2 text-left">Legislative District</th>
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
    <td class="px-4 py-2">{{ $athlete->legislative_district ?? '—' }}</td>
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

        <button
    onclick="openDeleteModal({{ $athlete->id }}, '{{ $athlete->last_name }}, {{ $athlete->first_name }} {{ $athlete->middle_initial }}')"
    class="text-red-600 hover:underline">
    Delete
</button>
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

{{-- DELETE MODAL --}}
<div id="deleteModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 relative animate-fade-in-up">

        {{-- CLOSE --}}
        <button onclick="closeDeleteModal()"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-700 text-xl">
            &times;
        </button>

        {{-- CONTENT --}}
        <h2 class="text-lg font-bold text-gray-800 mb-2">
            Confirm Delete
        </h2>

        <p class="text-sm text-gray-600 mb-4">
            Are you sure you want to delete:
        </p>

        <p id="deleteAthleteName"
           class="font-semibold text-red-600 mb-6">
        </p>

        {{-- FORM --}}
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function openDeleteModal(id, name) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const nameField = document.getElementById('deleteAthleteName');

    // Set dynamic values
    nameField.innerText = name;
    form.action = `/athletes/${id}`;

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    document.body.style.overflow = '';
}

/* Close on outside click */
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

/* Close on ESC */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>

@endsection
