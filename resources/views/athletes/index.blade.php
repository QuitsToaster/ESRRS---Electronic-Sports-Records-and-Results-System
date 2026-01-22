@extends('layouts.app')

@section('page-title', 'Athletes')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of participating athletes registered in the system
        </p>
    </div>

    <a href="{{ route('athletes.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Athlete
    </a>
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
                    if ($athlete->status === 'Injured') $statusBg = 'bg-yellow-200';
                    elseif ($athlete->status === 'Disqualified') $statusBg = 'bg-red-200';
                @endphp
                <tr class="hover:bg-gray-50 transition {{ $statusBg }}">
                    <td class="px-4 py-2">{{ $athlete->name }}</td>
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
                            class="w-16 h-16 border rounded cursor-pointer hover:scale-105 transition"
                            onclick="openQrModal(
                                '{{ route('athletes.qr', $athlete->id) }}',
                                '{{ $athlete->name }}',
                                '{{ $athlete->school->name }}',
                                '{{ $athlete->qr_code }}'
                            )"
                        >
                    </td>

                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('athletes.edit', $athlete->id) }}"
                           class="text-blue-600 hover:text-blue-800 mr-2">Edit</a>

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
                    <td colspan="8"
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
        <p class="text-sm text-gray-600 text-center mb-4" id="qrSchool"></p>
        <p class="text-xs text-gray-500 text-center mt-2" id="qrValue"></p>

        <img id="qrImage"
             src=""
             class="w-64 h-64 mx-auto border rounded">

        <p class="text-xs text-gray-500 text-center mt-3">
            Scan this QR code for attendance
        </p>
    </div>
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
</script>
@endsection
