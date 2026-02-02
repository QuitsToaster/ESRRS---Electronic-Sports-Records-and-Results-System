@extends('layouts.viewer')

@section('page-title', 'Events')

@section('content')
<div class="bg-black min-h-screen text-white">

    {{-- ====== Page Header & Events Section ====== --}}
    <div class="max-w-7xl mx-auto px-4 py-12 space-y-12">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold text-white" style="font-family: 'Poppins', sans-serif;">Don't miss the action.</h1>
                <p class="text-gray-400 mt-1 text-sm md:text-base" style="font-family: 'Poppins', sans-serif;">Events. Competitions. Champions. Explore it all.</p>
            </div>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('viewer.events') }}" class="flex flex-wrap gap-2 items-center">
                <input type="text" name="search" value="{{ request('search') ?? '' }}"
                       placeholder="Search events..."
                       class="w-full sm:w-64 border border-gray-700 rounded-md px-4 py-2 bg-black text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow font-semibold transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('viewer.events') }}" class="text-gray-400 hover:text-white px-2 py-2 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Events Grid --}}
        @if($events->isEmpty())
            <div class="text-gray-400 text-center py-20">No events available at the moment.</div>
        @else
            <div x-data="{ activeEvent: null }" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <div class="bg-gray-900 rounded-2xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-1 p-6 flex flex-col justify-between relative group cursor-pointer border border-gray-800">

                        {{-- Event Info --}}
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-white group-hover:text-red-600 transition text-center mb-4">
                                {{ $event->name }}
                            </h3>
                            <div class="flex flex-wrap gap-2 mt-2 mb-2">
                                <span class="bg-blue-800 text-blue-100 text-xs md:text-sm font-medium px-2 py-1 rounded-full">{{ $event->sport->name ?? '-' }}</span>
                                <span class="bg-green-800 text-green-100 text-xs md:text-sm font-medium px-2 py-1 rounded-full">{{ $event->age_group ?? '-' }}</span>
                                <span class="bg-purple-800 text-purple-100 text-xs md:text-sm font-medium px-2 py-1 rounded-full">{{ $event->category ?? '-' }}</span>
                            </div>
                            <p class="text-gray-400 text-sm"><span class="font-semibold">Date:</span> 
                                {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'TBA' }}
                                @if($event->end_date) - {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }} @endif
                            </p>
                            <p class="text-gray-400 text-sm"><span class="font-semibold">Venue:</span> {{ $event->venue ?? 'TBA' }}</p>
                        </div>

                        {{-- View Details Button --}}
                        <button @click="activeEvent = {{ $event->id }}"
                                class="mt-4 w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition">
                            View Details
                        </button>

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-blue-50/10 opacity-0 group-hover:opacity-30 transition rounded-2xl pointer-events-none"></div>
                    </div>
                @endforeach

                {{-- Event Modal --}}
                <template x-for="event in {{ $eventsJson->toJson() }}" :key="event.id">
                    <div x-show="activeEvent === event.id" x-transition.opacity
                         class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center p-4 overflow-auto"
                         style="display: none;">
                        <div @click.away="activeEvent = null"
                             class="bg-gray-900 w-full md:max-w-4xl rounded-2xl shadow-2xl overflow-hidden relative">

                            {{-- Modal Header --}}
                            <div class="flex justify-between items-center p-5 border-b border-gray-700">
                                <h3 class="text-3xl font-bold text-white" x-text="event.name"></h3>
                                <button @click="activeEvent = null" class="text-gray-400 hover:text-white text-3xl">&times;</button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="p-6 space-y-6 text-gray-200">

                                {{-- Event Info --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div><span class="font-semibold">Sport:</span> <span x-text="event.sport?.name ?? '-'"></span></div>
                                    <div><span class="font-semibold">Division:</span> <span x-text="event.age_group ?? '-'"></span></div>
                                    <div><span class="font-semibold">Category:</span> <span x-text="event.category ?? '-'"></span></div>
                                    <div><span class="font-semibold">Venue:</span> <span x-text="event.venue ?? '-'"></span></div>
                                    <div>
                                        <span class="font-semibold">Date:</span>
                                        <span x-text="event.start_date ? new Date(event.start_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : 'TBA'"></span>
                                        <span x-show="event.end_date"> - <span x-text="new Date(event.end_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' })"></span></span>
                                    </div>
                                </div>

                                {{-- Participating Divisions (SDOs) --}}
<div>
    <h4 class="text-xl font-semibold text-white mb-3">Participating SDOs</h4>
    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 list-disc list-inside">
        <template x-for="division in event.divisions" :key="division.id">
            <li class="text-gray-200 font-medium" x-text="division.name"></li>
        </template>
        <li x-show="!event.divisions.length" class="text-gray-400 italic">No divisions assigned.</li>
    </ul>
</div>


                                {{-- Winners --}}
                                <div>
                                    <h4 class="text-xl font-semibold text-white mb-3">Winners</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="bg-yellow-700 text-yellow-100 p-4 rounded-xl text-center shadow">
                                            <div class="font-bold text-lg">Gold</div>
                                            <div class="mt-1 font-medium" x-text="event.goldWinner ? event.goldWinner.name : '-'"></div>
                                        </div>
                                        <div class="bg-gray-800 text-gray-100 p-4 rounded-xl text-center shadow">
                                            <div class="font-bold text-lg">Silver</div>
                                            <div class="mt-1 font-medium" x-text="event.silverWinner ? event.silverWinner.name : '-'"></div>
                                        </div>
                                        <div class="bg-orange-700 text-orange-100 p-4 rounded-xl text-center shadow">
                                            <div class="font-bold text-lg">Bronze</div>
                                            <div class="mt-1 font-medium" x-text="event.bronzeWinner ? event.bronzeWinner.name : '-'"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Modal Footer --}}
                            <div class="flex justify-end p-5 border-t border-gray-700">
                                <button @click="activeEvent = null"
                                        class="px-5 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg shadow transition">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>

    {{-- ====== Gallery Slider (FULL WIDTH) ====== --}}
    @if(isset($gallery_photos) && $gallery_photos->count() > 0)
        <div class="mt-40 space-y-6 overflow-hidden w-full">

            {{-- Upper row: slide right --}}
            <div class="relative overflow-hidden w-screen -mx-0">
                <div class="flex animate-slide-right space-x-6">
                    @foreach($gallery_photos as $photo)
                        @foreach($photo->photos as $file)
                            <div class="min-w-[200px] flex-shrink-0 rounded-lg overflow-hidden shadow-lg">
                                <img src="{{ asset('storage/' . $file) }}" class="w-full h-40 object-cover" alt="{{ $photo->title }}">
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

            {{-- Lower row: slide left --}}
            <div class="relative overflow-hidden w-screen -mx-0">
                <div class="flex animate-slide-left space-x-6">
                    @foreach($gallery_photos as $photo)
                        @foreach($photo->photos as $file)
                            <div class="min-w-[200px] flex-shrink-0 rounded-lg overflow-hidden shadow-lg">
                                <img src="{{ asset('storage/' . $file) }}" class="w-full h-40 object-cover" alt="{{ $photo->title }}">
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

        </div>
    @endif

</div>

{{-- Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>

{{-- Tailwind Animations --}}
<style>
    @keyframes slide-right {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-100%)); }
    }
    @keyframes slide-left {
        0% { transform: translateX(calc(-100%)); }
        100% { transform: translateX(0); }
    }

    .animate-slide-right {
        display: flex;
        animation: slide-right 40s linear infinite;
    }
    .animate-slide-left {
        display: flex;
        animation: slide-left 40s linear infinite;
    }
</style>
@endsection
