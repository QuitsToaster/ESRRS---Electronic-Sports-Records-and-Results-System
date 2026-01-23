@extends('layouts.app')

@section('page-title', 'Coaches')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of coaches registered in the system
        </p>
    </div>
    <a href="{{ route('coaches.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Coach
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Search Bar --}}
<form method="GET" action="{{ route('coaches.index') }}" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search coaches..."
           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>
    @if(request('search'))
        <a href="{{ route('coaches.index') }}" class="text-gray-500 hover:text-gray-700 ml-2">Clear</a>
    @endif
</form>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
                <th class="px-4 py-2 text-left">Coach</th>
                <th class="px-4 py-2 text-left">Team</th>
                <th class="px-4 py-2 text-left">Sport</th>
                <th class="px-4 py-2 text-left">QR Code</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($coaches as $coach)
                <tr class="hover:bg-gray-50 transition">
                    {{-- Coach Info with Photo --}}
                    <td class="px-4 py-2 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-300 flex-shrink-0">
                            <img src="{{ $coach->profile_photo ? asset('storage/' . $coach->profile_photo) : asset('images/avatar-placeholder.png') }}"
                                 alt="{{ $coach->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <span>{{ $coach->name }}</span>
                    </td>

                    {{-- Team --}}
                    <td class="px-4 py-2">{{ $coach->school->name ?? '-' }}</td>

                    {{-- Sport --}}
                    <td class="px-4 py-2">{{ $coach->sport->name ?? '-' }}</td>

                    {{-- QR Code --}}
                    <td class="px-4 py-2">
                        @if($coach->qr_code)
                            <img src="{{ route('coaches.qr', $coach->id) }}"
                                 alt="QR Code"
                                 class="w-16 h-16 border rounded cursor-pointer hover:scale-105 transition"
                                 onclick="openQrModal(
                                     '{{ route('coaches.qr', $coach->id) }}',
                                     '{{ $coach->name }}',
                                     '{{ $coach->school->name ?? '-' }}',
                                     '{{ $coach->qr_code }}'
                                 )"
                            >
                        @else
                            <span class="text-gray-400 text-sm">No QR</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('coaches.edit', $coach->id) }}" class="text-blue-600 hover:text-blue-800 mr-2">Edit</a>
                        <form action="{{ route('coaches.destroy', $coach->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this coach?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">No coaches found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- QR Modal --}}
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

{{-- Script --}}
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
