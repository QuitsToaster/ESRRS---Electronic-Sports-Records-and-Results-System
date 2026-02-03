@extends('layouts.app')

@section('page-title', 'Athletes')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of participating athletes registered in the system
        </p>
    </div>

    <div class="flex gap-2 items-center">

        {{-- ADD ATHLETE --}}
        <a href="{{ route('athletes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Add Athlete
        </a>

        {{-- BULK CSV UPLOAD --}}
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

        {{-- EXPORT ID PDF --}}
<a href="{{ route('athletes.exportIdPdf') }}"
   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow inline-flex items-center gap-2">
    <span>Export ID PDF</span>
</a>



    </div>
</div>


{{-- SEARCH --}}
<form method="GET" action="{{ route('athletes.index') }}" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search name, school, sport, region, status..."
           class="flex-1 border rounded px-3 py-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
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
    <th class="px-4 py-2 text-left">Team</th>
    <th class="px-4 py-2 text-left">Sport</th>
    <th class="px-4 py-2 text-left">Region</th>
    <th class="px-4 py-2 text-left">School Type</th>
    <th class="px-4 py-2 text-left">Division</th>
    <th class="px-4 py-2 text-left">Category</th>
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
        {{ $athlete->last_name }},
        {{ $athlete->first_name }}
        @if($athlete->middle_initial)
            {{ $athlete->middle_initial }}
        @endif
    </td>

    <td class="px-4 py-2">{{ $athlete->school->name ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->sport->name ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->region ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->school_type ?? '—' }}</td>
    <td class="px-4 py-2">{{ $athlete->division }}</td>
    <td class="px-4 py-2">{{ $athlete->category }}</td>
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
    <td colspan="12" class="text-center py-6 text-gray-500">
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
