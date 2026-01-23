@extends('layouts.app')

@section('page-title', 'Athletes')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of participating athletes registered in the system
        </p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('athletes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Add Athlete
        </a>

        <button onclick="printAthletes()" 
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow inline-flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
         class="bi bi-printer-fill" viewBox="0 0 16 16">
        <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1"/>
        <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
    </svg>
    <span>Print All Athletes</span>
</button>

    </div>
</div>

{{-- Search Bar --}}
<form method="GET" action="{{ route('athletes.index') }}" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search athletes..."
           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>
    @if(request('search'))
        <a href="{{ route('athletes.index') }}"
           class="text-gray-500 hover:text-gray-700 ml-2">Clear</a>
    @endif
</form>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
                <th class="px-4 py-2 text-left">Profile</th>
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">Team</th>
                <th class="px-4 py-2 text-left">Sport</th>
                <th class="px-4 py-2 text-left">Division</th>
                <th class="px-4 py-2 text-left">Category</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-left">QR Code</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @forelse($athletes as $athlete)
                @php
                    $statusBg = '';
                    if ($athlete->status === 'Injured') $statusBg = 'bg-yellow-50';
                    elseif ($athlete->status === 'Disqualified') $statusBg = 'bg-red-50';
                @endphp

                <tr class="hover:bg-gray-50 transition {{ $statusBg }}">
                    
                    {{-- PROFILE PHOTO --}}
                    <td class="px-4 py-2">
                        @if($athlete->profile_photo)
                            <img
                                src="{{ asset('storage/' . $athlete->profile_photo) }}"
                                alt="{{ $athlete->name }}"
                                class="w-10 h-10 rounded-full object-cover border"
                            >
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-sm font-semibold text-gray-700">
                                {{ strtoupper(substr($athlete->name, 0, 1)) }}
                            </div>
                        @endif
                    </td>

                    <td class="px-4 py-2 font-medium">{{ $athlete->name }}</td>
                    <td class="px-4 py-2">{{ $athlete->school->name }}</td>
                    <td class="px-4 py-2">{{ $athlete->sport->name }}</td>
                    <td class="px-4 py-2">{{ $athlete->division }}</td>
                    <td class="px-4 py-2">{{ $athlete->category }}</td>
                    <td class="px-4 py-2 font-semibold">{{ $athlete->status }}</td>

                    {{-- QR CODE --}}
                    <td class="px-4 py-2">
                        <img
                            src="{{ route('athletes.qr', $athlete->id) }}"
                            alt="QR Code"
                            class="w-14 h-14 border rounded cursor-pointer hover:scale-105 transition"
                            onclick="openQrModal(
                                '{{ route('athletes.qr', $athlete->id) }}',
                                '{{ $athlete->name }}',
                                '{{ $athlete->school->name }}',
                                '{{ $athlete->qr_code }}'
                            )"
                        >
                    </td>

                    {{-- ACTIONS --}}
                    <td class="px-4 py-2 text-right whitespace-nowrap">
                        <a href="{{ route('athletes.edit', $athlete->id) }}"
                           class="text-blue-600 hover:text-blue-800 mr-2">
                            Edit
                        </a>

                        <form action="{{ route('athletes.destroy', $athlete->id) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Delete this athlete?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9"
                        class="px-4 py-4 text-center text-gray-500">
                        No athletes found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- QR MODAL --}}
<div id="qrModal"
     class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg shadow-lg p-6 relative w-80">
        <button onclick="closeQrModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl">
            &times;
        </button>

        <h3 class="text-lg font-semibold text-center mb-1" id="qrName"></h3>
        <p class="text-sm text-gray-600 text-center mb-3" id="qrSchool"></p>

        <img id="qrImage"
             src=""
             class="w-64 h-64 mx-auto border rounded">

        <p class="text-xs text-gray-500 text-center mt-2" id="qrValue"></p>

        <p class="text-xs text-gray-500 text-center mt-3">
            Scan this QR code for attendance
        </p>
    </div>
</div>

{{-- PRINT SECTION (HIDDEN) --}}
<div id="printSection" class="hidden">
    @foreach($athletes as $athlete)
        <div style="width: 250px; height: 150px; border: 1px solid #000; margin: 8px; padding: 8px; display: inline-block; text-align: center; font-family: sans-serif;">
            {{-- Profile --}}
            @if($athlete->profile_photo)
                <img src="{{ asset('storage/' . $athlete->profile_photo) }}"
                     style="width:60px; height:60px; border-radius:50%; object-fit:cover;">
            @else
                <div style="width:60px; height:60px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                    {{ strtoupper(substr($athlete->name,0,1)) }}
                </div>
            @endif

            <p style="margin:4px 0 0 0; font-weight:bold;">{{ $athlete->name }}</p>
            <p style="margin:0; font-size:12px;">{{ $athlete->school->name }}</p>
            <p style="margin:0; font-size:12px;">{{ $athlete->sport->name }}</p>

            {{-- QR Code --}}
            <img src="{{ route('athletes.qr', $athlete->id) }}"
                 style="width:70px; height:70px; margin-top:4px;">
        </div>
    @endforeach
</div>

{{-- SCRIPT --}}
<script>
function openQrModal(src, name, school, value = '') {
    document.getElementById('qrImage').src = src;
    document.getElementById('qrName').innerText = name;
    document.getElementById('qrSchool').innerText = school;
    document.getElementById('qrValue').innerText = value ? `QR Value: ${value}` : '';
    document.getElementById('qrModal').classList.remove('hidden');
    document.getElementById('qrModal').classList.add('flex');
}

function closeQrModal() {
    document.getElementById('qrModal').classList.add('hidden');
    document.getElementById('qrModal').classList.remove('flex');
}

function printAthletes() {
    const printContents = document.getElementById('printSection').innerHTML;
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Athlete IDs</title></head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
</script>
@endsection
