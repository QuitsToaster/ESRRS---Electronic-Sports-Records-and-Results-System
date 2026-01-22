@extends('layouts.viewer')

@section('page-title', 'Home')

@section('content')

{{-- Banner Slideshow --}}
<div class="relative w-full h-64 md:h-96 rounded-lg overflow-hidden shadow-lg">
    @foreach($banners as $index => $banner)
        <div class="absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide>
            <img src="{{ asset('storage/' . $banner->image) }}" class="w-full h-full object-cover">
            {{-- Gradient overlay: darkest left, lighter right --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 to-black/20"></div>
            <div class="absolute left-6 top-1/3 max-w-lg text-white">
                <h2 class="text-5xl md:text-6xl font-bold mb-2">Welcome to CEMS</h2>
                <h2 class="text-2xl md:text-2xl font-bold mb-2">CAVRAA Event Monitoring System</h2>
                <p class="text-white/90 text-sm md:text-base">Overview of events, games, announcements, and medal standings.</p>
            </div>
        </div>
    @endforeach
</div>



{{-- Main Container --}}
<div class="space-y-8 max-w-7xl mx-auto px-4 md:px-6 lg:px-8 mt-6">

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-5 text-center hover:shadow-lg transition">
            <div class="text-2xl font-bold text-gray-800">{{ $eventsCount }}</div>
            <div class="text-sm text-gray-500 mt-1">Events</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 text-center hover:shadow-lg transition">
            <div class="text-2xl font-bold text-gray-800">{{ $gamesCount }}</div>
            <div class="text-sm text-gray-500 mt-1">Games</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 text-center hover:shadow-lg transition">
            <div class="text-2xl font-bold text-gray-800">{{ $announcementsCount }}</div>
            <div class="text-sm text-gray-500 mt-1">Announcements</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 text-center hover:shadow-lg transition">
            <div class="text-2xl font-bold text-gray-800">{{ $medalTally->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Participants with Medals</div>
        </div>
    </div>

    {{-- Upcoming Events & Games --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Events --}}
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Events</h3>
            @if($upcomingEvents->isEmpty())
                <p class="text-gray-500 text-sm">No upcoming events.</p>
            @else
                <ul class="divide-y">
                    @foreach($upcomingEvents as $event)
                        <li class="py-3 flex justify-between items-center text-sm hover:bg-gray-50 rounded px-2 transition">
                            <span class="text-gray-700 font-medium">{{ $event->name }}</span>
                            <span class="text-gray-500">{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Games --}}
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Games</h3>
            @if($upcomingGames->isEmpty())
                <p class="text-gray-500 text-sm">No upcoming games.</p>
            @else
                <ul class="divide-y">
                    @foreach($upcomingGames as $game)
                        <li class="py-3 px-2 hover:bg-gray-50 rounded transition">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $game->team_a }} vs {{ $game->team_b }}</span>
                                <span class="text-gray-500">{{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y h:i A') }}</span>
                            </div>
                            <div class="text-gray-400 text-xs mt-1">{{ $game->event->name ?? 'No Event' }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Announcements --}}
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Latest Announcements</h3>
        @if($latestAnnouncements->isEmpty())
            <p class="text-gray-500 text-sm">No announcements available.</p>
        @else
            <ul class="divide-y">
                @foreach($latestAnnouncements as $announcement)
                    <li class="py-4 hover:bg-gray-50 rounded px-2 transition">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-800">{{ $announcement->title }}</span>
                            <span class="text-xs text-gray-500">{{ $announcement->published_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($announcement->content, 120) }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Medal Tally --}}
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Medal Tally</h3>
        @if($medalTally->isEmpty())
            <p class="text-gray-500 text-sm">No medals awarded yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">School</th>
                            <th class="px-4 py-2 text-center">Gold</th>
                            <th class="px-4 py-2 text-center">Silver</th>
                            <th class="px-4 py-2 text-center">Bronze</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($medalTally as $school)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">{{ \App\Models\School::find($school->school_id)->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-2 text-center">{{ $school->gold }}</td>
                                <td class="px-4 py-2 text-center">{{ $school->silver }}</td>
                                <td class="px-4 py-2 text-center">{{ $school->bronze }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

{{-- Slideshow JS --}}
<script>
let slides = document.querySelectorAll('[data-slide]');
let current = 0;

function showSlide(index) {
    slides.forEach((slide, i) => slide.style.opacity = i === index ? '1' : '0');
}

setInterval(() => {
    current = (current + 1) % slides.length;
    showSlide(current);
}, 5000);
</script>

@endsection
