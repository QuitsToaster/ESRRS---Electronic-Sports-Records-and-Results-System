@extends('layouts.viewer')

@section('page-title', $school->name)

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Back Button --}}
    <a href="{{ route('viewer.participants') }}" class="text-blue-600 hover:underline mb-4 inline-block">
        &larr; Back
    </a>

    {{-- School Header --}}
    <div class="flex flex-col items-center mb-6">
        @if($school->logo)
            <img src="{{ asset('storage/' . $school->logo) }}"
                 alt="{{ $school->name }} Logo"
                 class="w-32 h-32 object-contain rounded-full border border-gray-300 mb-3">
        @endif
        <h1 class="text-3xl font-bold text-gray-800">{{ $school->name }}</h1>
    </div>

    {{-- Medals Section --}}
    <h2 class="text-xl font-semibold text-gray-700 mb-2">Medals</h2>

    <div x-data="{ openModal: false, selectedMedal: null }" class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">

        @php
            $gold   = $school->results()->where('medal', 'Gold')->count();
            $silver = $school->results()->where('medal', 'Silver')->count();
            $bronze = $school->results()->where('medal', 'Bronze')->count();
        @endphp

        @foreach(['Gold' => $gold, 'Silver' => $silver, 'Bronze' => $bronze] as $medal => $count)
            @php
                $colorBg   = $medal === 'Gold' ? 'bg-yellow-100' : ($medal === 'Silver' ? 'bg-gray-200' : 'bg-orange-100');
                $colorText = $medal === 'Gold' ? 'text-yellow-600' : ($medal === 'Silver' ? 'text-gray-500' : 'text-orange-500');
            @endphp

            <button
                @click="openModal = true; selectedMedal = '{{ $medal }}'"
                class="{{ $colorBg }} rounded-lg p-4 flex flex-col items-center shadow hover:shadow-lg transition">
                <span class="text-lg font-semibold text-gray-700">{{ $medal }}</span>
                <span class="text-3xl font-bold {{ $colorText }} mt-2">{{ $count }}</span>
            </button>
        @endforeach

        {{-- Medal Modal --}}
        <div x-show="openModal" x-transition
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.away="openModal = false"
                 class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-6 overflow-auto max-h-[80vh]">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold" x-text="selectedMedal + ' Medals'"></h3>
                    <button @click="openModal = false"
                            class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                <table class="min-w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-1 text-left">Event</th>
                            <th class="px-2 py-1 text-left">Sport</th>
                            <th class="px-2 py-1 text-left">Division</th>
                            <th class="px-2 py-1 text-left">Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['Gold', 'Silver', 'Bronze'] as $medalType)
                            @foreach($school->results()->where('medal', $medalType)->get() as $result)
                                <tr x-show="selectedMedal === '{{ $medalType }}'"
                                    class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-2 py-1">{{ $result->event->name ?? 'N/A' }}</td>
                                    <td class="px-2 py-1">{{ $result->sport->name ?? 'N/A' }}</td>
                                    <td class="px-2 py-1">{{ $result->division ?? 'N/A' }}</td>
                                    <td class="px-2 py-1">{{ $result->category ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Coaches --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Coaches</h2>

        @if($school->coaches->isEmpty())
            <p class="text-gray-400">No coaches listed</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($school->coaches as $coach)
                    <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full overflow-hidden border mb-3">
                            <img src="{{ $coach->profile_photo
                                         ? asset('storage/' . $coach->profile_photo)
                                         : asset('images/avatar-placeholder.png') }}"
                                 alt="{{ $coach->name }} Photo"
                                 class="w-full h-full object-cover">
                        </div>
                        <h3 class="font-semibold text-gray-800">{{ $coach->name }}</h3>
                        <p class="text-gray-500 text-sm mt-1">{{ $coach->sport->name ?? 'N/A' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Athletes --}}
    <h2 class="text-xl font-semibold text-gray-700 mb-2">Athletes</h2>

    {{-- Filters --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('viewer.participants.show', $school->id) }}"
              class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search athletes..."
                   class="border rounded px-3 py-2 flex-1">

            <select name="sport" class="border rounded px-3 py-2">
                <option value="">Sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" @selected($sportId == $sport->id)>
                        {{ $sport->name }}
                    </option>
                @endforeach
            </select>

            <select name="division" class="border rounded px-3 py-2">
                <option value="">Divisions</option>
                @foreach($divisions as $div)
                    <option value="{{ $div }}" @selected($division == $div)>
                        {{ $div }}
                    </option>
                @endforeach
            </select>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
        </form>
    </div>

    {{-- Athletes Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 text-left">Photo</th>
                    <th class="px-2 py-1 text-left">Name</th>
                    <th class="px-2 py-1 text-left">Sport</th>
                    <th class="px-2 py-1 text-left">Division</th>
                    <th class="px-2 py-1 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($school->athletes as $athlete)
                    <tr class="border-b hover:bg-gray-50
                        @if(strtolower($athlete->status)=='injured') bg-yellow-100
                        @elseif(strtolower($athlete->status)=='disqualified') bg-red-100 text-red-700 @endif">

                        {{-- Athlete Photo --}}
                        <td class="px-2 py-1">
                            <img
                                src="{{ $athlete->profile_photo
                                        ? asset('storage/'.$athlete->profile_photo)
                                        : asset('images/avatar-placeholder.png') }}"
                                class="w-10 h-10 rounded-full object-cover border"
                                alt="{{ $athlete->name }} Photo">
                        </td>

                        <td class="px-2 py-1 font-medium">{{ $athlete->name }}</td>
                        <td class="px-2 py-1">{{ $athlete->sport->name ?? 'N/A' }}</td>
                        <td class="px-2 py-1">{{ $athlete->division }}</td>
                        <td class="px-2 py-1">{{ $athlete->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-gray-400 text-center py-3">
                            No athletes listed
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection
