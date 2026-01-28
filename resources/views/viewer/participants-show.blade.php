@extends('layouts.viewer')

@section('page-title', $school->name)

@section('content')

{{-- School Header Section --}}
<section class="relative w-full -mt-16">
    {{-- Background Image + Overlay (absolute to this section only) --}}
    <div class="absolute inset-0 -z-20">
        <img src="{{ asset('storage/arena.jpg') }}" 
             alt="{{ $school->name }} Background"
             class="w-full h-full object-cover brightness-50">
        <div class="absolute inset-0 bg-black/50"></div> {{-- Darker overlay --}}
    </div>

    {{-- Repeated School Name in Background --}}
    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 -z-10">
        <span class="text-6xl sm:text-8xl md:text-9xl font-bold text-white opacity-20 select-none whitespace-nowrap">
            {{ $school->name }}
        </span>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 pt-48 pb-20 flex flex-col sm:flex-row items-center sm:items-center justify-center sm:justify-start gap-8">
        {{-- Back Button --}}
        <a href="{{ route('viewer.participants') }}"
           class="absolute top-20 left-4 bg-white/30 backdrop-blur-md text-blue-100 hover:text-white px-3 py-1 rounded shadow transition z-10">
            &larr; Back
        </a>

        {{-- School Logo --}}
@if($school->logo)
    <div class="w-40 h-40 sm:w-48 sm:h-48 rounded-full overflow-hidden border-2 border-white/30">
        <img src="{{ asset('storage/' . $school->logo) }}"
             alt="{{ $school->name }} Logo"
             class="w-full h-full object-cover">
    </div>
@endif


        {{-- School Name --}}
        <h1 class="text-7xl sm:text-7xl font-bold text-white text-center sm:text-left z-10">
            {{ $school->name }}
        </h1>
    </div>
</section>

{{-- Medals Section --}}
<section class="relative mx-auto px-4 py-10 bg-gray-300">
    {{-- Section Title --}}
    <div class="text-center mb-8">
        <h2 class="text-5xl md:text-8xl text-black italic font-bold" style="font-family: 'Poppins', sans-serif;">
            Medals
        </h2>
    </div>

    <div x-data="{ openModal: false, selectedMedal: null }" class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        @php
            $gold   = $school->results()->where('medal', 'Gold')->count();
            $silver = $school->results()->where('medal', 'Silver')->count();
            $bronze = $school->results()->where('medal', 'Bronze')->count();
        @endphp

        @foreach(['Gold' => $gold, 'Silver' => $silver, 'Bronze' => $bronze] as $medal => $count)
            @php
                $bgGradient = $medal === 'Gold' ? 'bg-gradient-to-tr from-yellow-400 to-yellow-200' :
                              ($medal === 'Silver' ? 'bg-gradient-to-tr from-gray-300 to-gray-100' :
                              'bg-gradient-to-tr from-orange-400 to-yellow-300');
                $textColor = $medal === 'Gold' ? 'text-yellow-700' :
                             ($medal === 'Silver' ? 'text-gray-700' : 'text-orange-600');
            @endphp

            <button
                @click="openModal = true; selectedMedal = '{{ $medal }}'"
                class="{{ $bgGradient }} rounded-lg p-6 flex flex-col items-center transition transform hover:scale-105 hover:brightness-110 shadow-lg">
                
                {{-- Medal Label --}}
                <span class="text-lg font-semibold {{ $textColor }}">{{ $medal }}</span>
                
                {{-- Medal Count --}}
                <span class="text-3xl font-bold {{ $textColor }} mt-2 drop-shadow-lg">{{ $count }}</span>
            </button>
        @endforeach

        {{-- Medal Modal --}}
        <div x-show="openModal" x-transition
             class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
            <div @click.away="openModal = false"
                 class="bg-white/80 backdrop-blur-md rounded-2xl shadow-2xl max-w-3xl w-full overflow-auto max-h-[80vh] border border-white/30 transition transform scale-95 x-transition:enter="scale-100" x-transition:leave="scale-95">

                {{-- Modal Header --}}
                <div :class="selectedMedal === 'Gold' ? 'bg-yellow-200 text-yellow-800' : selectedMedal === 'Silver' ? 'bg-gray-200 text-gray-800' : 'bg-orange-200 text-orange-700'"
                     class="rounded-t-2xl px-6 py-4 flex justify-between items-center font-bold text-xl shadow-inner">
                    <span x-text="selectedMedal + ' Medals'"></span>
                    <button @click="openModal = false" class="text-gray-600 hover:text-gray-900 text-3xl font-light">&times;</button>
                </div>

                {{-- Modal Table --}}
                <div class="p-6">
                    <table class="min-w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Event</th>
                                <th class="px-3 py-2 text-left">Sport</th>
                                <th class="px-3 py-2 text-left">Division</th>
                                <th class="px-3 py-2 text-left">Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['Gold', 'Silver', 'Bronze'] as $medalType)
                                @foreach($school->results()->where('medal', $medalType)->get() as $result)
                                    <tr x-show="selectedMedal === '{{ $medalType }}'"
                                        class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-3 py-2">{{ $result->event->name ?? 'N/A' }}</td>
                                        <td class="px-3 py-2">{{ $result->sport->name ?? 'N/A' }}</td>
                                        <td class="px-3 py-2">{{ $result->division ?? 'N/A' }}</td>
                                        <td class="px-3 py-2">{{ $result->category ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</section>




{{-- Coaches Section --}}
<section class="relative max-w-7xl mx-auto px-4 py-10">
    {{-- Section Title --}}
    <div class="mb-10">
        <h2 class="text-5xl md:text-6xl text-black" style="font-family: 'Poppins', sans-serif;">
            Coaches
        </h2>
    </div>

    @if($school->coaches->isEmpty())
        <p class="text-gray-400 text-lg">No coaches listed</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            @foreach($school->coaches as $coach)
                <div class="flex items-center gap-4 p-4 rounded-lg hover:shadow-lg transition duration-300 bg-white/50 backdrop-blur-sm">
                    {{-- Coach Photo --}}
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden border-2 border-gray-300 shadow-sm flex-shrink-0">
                        <img src="{{ $coach->profile_photo
                                     ? asset('storage/' . $coach->profile_photo)
                                     : asset('images/avatar-placeholder.png') }}"
                             alt="{{ $coach->name }} Photo"
                             class="w-full h-full object-cover">
                    </div>

                    {{-- Coach Info --}}
                    <div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-800">{{ $coach->name }}</h3>
                        <p class="text-gray-600 sm:text-lg">{{ $coach->sport->name ?? 'N/A' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>


{{-- Athletes Section --}}
<section class="relative max-w-7xl mx-auto px-4 py-10">
    {{-- Section Title --}}
    <div class="text-left mb-8">
        <h2 class="text-5xl md:text-6xl text-black" style="font-family: 'Poppins', sans-serif;">
            Athletes
        </h2>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-wrap gap-2 items-center">
        <form method="GET" action="{{ route('viewer.participants.show', $school->id) }}"
              class="flex flex-wrap gap-2 items-center w-full">
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
</section>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection
