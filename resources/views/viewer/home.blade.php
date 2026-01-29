@extends('layouts.viewer')

@section('page-title', 'Home')

@section('content')

{{-- Banner Slideshow --}}
<div class="relative w-full h-64 md:h-96 overflow-hidden -mt-16 z-0">
    @foreach($banners as $index => $banner)
        <div class="absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide>
            <img src="{{ asset('storage/' . $banner->image) }}" class="w-full h-full object-cover" 
                 style="clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);">
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/20"
                 style="clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);"></div>

            {{-- Banner Text --}}
            <div class="absolute left-6 top-1/3 max-w-lg text-white">
                <h2 class="text-5xl md:text-6xl font-bold mb-2">Welcome to CEMS</h2>
                <h2 class="text-2xl md:text-2xl font-bold mb-2">CAVRAA Event Monitoring System</h2>
                <p class="text-white/90 text-sm md:text-base">Overview of events, games, announcements, and medal standings.</p>
            </div>
        </div>
    @endforeach
</div>


{{-- Quick Stats --}}
<section class="relative -mt-8 py-12" 
         style="clip-path: polygon(0 15%, 100% 0, 100% 100%, 0 100%);">

    {{-- Section Title --}}
<div class="text-center mb-8">
    <h2 class="text-5xl md:text-6xl text-black font-bold" style="font-family: 'Poppins', sans-serif;">
        Quick Actions
    </h2>
</div>


    {{-- Stats Grid --}}
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Events --}}
        <a href="{{ route('viewer.events') }}" class="transform hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="bg-white rounded-xl shadow-md p-6 text-center border border-gray-100">
                <div class="text-3xl font-bold text-gray-800">{{ $eventsCount }}</div>
                <div class="text-sm text-gray-500 mt-1">Events</div>
            </div>
        </a>

        {{-- Games --}}
        <a href="{{ route('viewer.games') }}" class="transform hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="bg-white rounded-xl shadow-md p-6 text-center border border-gray-100">
                <div class="text-3xl font-bold text-gray-800">{{ $gamesCount }}</div>
                <div class="text-sm text-gray-500 mt-1">Games</div>
            </div>
        </a>

        {{-- Announcements --}}
        <a href="{{ route('viewer.announcements') }}" class="transform hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="bg-white rounded-xl shadow-md p-6 text-center border border-gray-100">
                <div class="text-3xl font-bold text-gray-800">{{ $announcementsCount }}</div>
                <div class="text-sm text-gray-500 mt-1">Announcements</div>
            </div>
        </a>

        {{-- Medal Participants --}}
        <a href="{{ route('viewer.medalTally') }}" class="transform hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="bg-white rounded-xl shadow-md p-6 text-center border border-gray-100">
                <div class="text-3xl font-bold text-gray-800">{{ $medalTally->count() }}</div>
                <div class="text-sm text-gray-500 mt-1">Teams with Medals</div>
            </div>
        </a>

    </div>
</section>





{{-- Medal Tally --}}
<section class="relative py-10">
    {{-- Background Image --}}
    <div class="absolute inset-0">
        <img src="{{ asset('storage/medalbg.jpg') }}"
             alt="Medal Background"
             class="w-full h-full object-cover brightness-75 blur-sm">
        <div class="absolute inset-0 bg-black/30"></div>
    </div>

    {{-- Content --}}
    <div class="relative max-w-7xl mx-auto px-6">

        {{-- Header (Desktop Only) --}}
        <div class="hidden md:grid grid-cols-[40px_1fr_420px_96px] items-center mb-6 text-white">
            <div></div>
            <h3 class="text-3xl font-bold">Medal Tally</h3>

            <div class="grid grid-cols-3 text-center font-semibold text-lg">
                <span class="text-yellow-400">Gold</span>
                <span class="text-gray-300">Silver</span>
                <span class="text-orange-400">Bronze</span>
            </div>

            <div class="text-center font-semibold text-4xl text-red-600">
                Total
            </div>
        </div>

        @if($medalTally->isEmpty())
            <p class="text-gray-200 text-sm">No medals awarded yet.</p>
        @else
            <div class="space-y-4 md:space-y-6">
                @foreach($medalTally as $index => $school)
                    @php
                        $schoolModel = \App\Models\School::find($school->school_id);
                        $total = $school->gold + $school->silver + $school->bronze;
                    @endphp

                    {{-- ================= DESKTOP ROW ================= --}}
                    <div class="hidden md:grid
                                grid-cols-[40px_1fr_420px_70px]
                                items-center p-6
                                bg-white/10 backdrop-blur-md
                                rounded-xl shadow-lg hover:shadow-2xl transition">

                        {{-- Rank --}}
                        <div class="text-white font-bold text-lg text-center">
                            {{ $index + 1 }}
                        </div>

                        {{-- School --}}
                        <div class="flex items-center space-x-4">
                            @if($schoolModel && $schoolModel->logo)
                                <img src="{{ asset('storage/' . $schoolModel->logo) }}"
                                     class="w-12 h-12 object-cover rounded-full border border-white/30">
                            @endif
                            <span class="text-white font-semibold text-lg">
                                {{ $schoolModel->name ?? 'Unknown' }}
                            </span>
                        </div>

                        {{-- Medals --}}
                        <div class="grid grid-cols-3 text-center font-extrabold text-4xl">
                            <span class="text-yellow-400">{{ $school->gold }}</span>
                            <span class="text-gray-300">{{ $school->silver }}</span>
                            <span class="text-orange-400">{{ $school->bronze }}</span>
                        </div>

                        {{-- Total (UNCHANGED DESIGN) --}}
                        <div class="relative h-full flex items-center justify-center">
                            <div class="absolute inset-y-0 left-0 w-[500px] -z-10
                                        bg-gradient-to-r
                                        from-yellow-300 via-yellow-400 to-amber-500
                                        opacity-90"></div>

                            <div class="relative text-red-600 font-extrabold text-6xl">
                                {{ $total }}
                            </div>
                        </div>
                    </div>

                    {{-- ================= MOBILE CARD ================= --}}
                    <div class="md:hidden
                                bg-white/10 backdrop-blur-md
                                rounded-xl shadow-lg p-4 space-y-4">

                        {{-- Header --}}
                        <div class="flex items-center gap-3">
                            <span class="text-white font-bold">#{{ $index + 1 }}</span>

                            @if($schoolModel && $schoolModel->logo)
                                <img src="{{ asset('storage/' . $schoolModel->logo) }}"
                                     class="w-10 h-10 rounded-full border border-white/30 object-cover">
                            @endif

                            <span class="text-white font-semibold">
                                {{ $schoolModel->name ?? 'Unknown' }}
                            </span>
                        </div>

                        {{-- Medals --}}
                        <div class="grid grid-cols-3 text-center">
                            <div>
                                <p class="text-xs text-yellow-300">Gold</p>
                                <p class="text-3xl font-extrabold text-yellow-400">{{ $school->gold }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-300">Silver</p>
                                <p class="text-3xl font-extrabold text-gray-300">{{ $school->silver }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-orange-300">Bronze</p>
                                <p class="text-3xl font-extrabold text-orange-400">{{ $school->bronze }}</p>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="relative overflow-hidden rounded-lg">
                            <div class="absolute inset-0
                                        bg-gradient-to-r
                                        from-yellow-300 via-yellow-400 to-amber-500
                                        opacity-90"></div>
                            <div class="relative text-center text-red-700 font-extrabold text-4xl py-2">
                                Total: {{ $total }}
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>
        @endif
    </div>
</section>





{{-- Latest Gallery Highlight --}}
@php
    use App\Models\GalleryPhoto;
    $latestPhoto = GalleryPhoto::latest()->first();
    $remainingPhotos = GalleryPhoto::latest()->skip(1)->take(5)->get(); // remaining photos for carousel
@endphp

@if($latestPhoto)
<section class="bg-gray-50 py-16 border-t">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Section Title on Top --}}
        <div class="text-center mb-10">
            <h2 class="text-4xl md:text-7xl text-gray-800 font-bold" style="font-family: 'Poppins', sans-serif;">
                Highlights
            </h2>
        </div>

        {{-- Spotlight Container --}}
        <div class="flex flex-col md:flex-row items-center gap-10">

            {{-- Left Side: Latest Photo --}}
            <div class="flex-1">
                @php
                    $firstImage = $latestPhoto->photos[0] ?? null;
                @endphp
                @if($firstImage)
                    <img src="{{ asset('storage/' . $firstImage) }}" 
                         alt="{{ $latestPhoto->title ?? 'Gallery Image' }}"
                         class="w-full h-96 object-cover rounded-3xl shadow-2xl hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-96 bg-gray-300 flex items-center justify-center text-gray-600 text-xl font-semibold rounded-3xl">
                        No Image
                    </div>
                @endif
            </div>

            {{-- Right Side: Title + Description --}}
            <div class="flex-1 space-y-6">
                <h2 class="text-5xl text-gray-800" style="font-family: 'Poppins', sans-serif;">
                    {{ $latestPhoto->title ?? 'Gallery Highlight' }}
                </h2>
                <p class="text-gray-600 text-lg leading-relaxed">
                    {{ $latestPhoto->description ?? 'No description available for this gallery item.' }}
                </p>
                <button onclick="openPhotoModal({{ $latestPhoto->id }})"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg transition">
                    View Gallery
                </button>
            </div>

        </div>
    </div>
</section>

@endif

{{-- Remaining Gallery Carousel --}}
@if($remainingPhotos->isNotEmpty())
<section class="bg-gray-50 py-12 border-t relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Carousel Wrapper --}}
        <div class="relative">

            {{-- Left Arrow --}}
            <button 
                class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white/100 text-gray-800 rounded-full p-3 shadow-lg z-20"
                onclick="document.getElementById('gallery-carousel').scrollBy({left: -300, behavior: 'smooth'})"
            >
                &#8592;
            </button>

            {{-- Right Arrow --}}
            <button 
                class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white/100 text-gray-800 rounded-full p-3 shadow-lg z-20"
                onclick="document.getElementById('gallery-carousel').scrollBy({left: 300, behavior: 'smooth'})"
            >
                &#8594;
            </button>

            {{-- Carousel --}}
            <div id="gallery-carousel" class="flex space-x-6 overflow-x-auto scrollbar-hide scroll-smooth py-2">
                @foreach($remainingPhotos as $photo)
                    @php
                        $firstImage = $photo->photos[0] ?? null;
                        $extraCount = count($photo->photos) - 1;
                    @endphp

                    {{-- Photo Card --}}
                    <div 
                        class="relative flex-shrink-0 w-64 md:w-72 rounded-2xl shadow-lg cursor-pointer transform hover:scale-105 transition-all duration-500 group"
                        onclick="openPhotoModal({{ $photo->id }})"
                    >
                        @if($firstImage)
                            <img src="{{ asset('storage/' . $firstImage) }}" 
                                 class="w-full h-64 md:h-72 object-cover rounded-2xl group-hover:scale-110 transition-transform duration-500">

                            {{-- Overlay --}}
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-center items-center rounded-2xl">
                                @if($extraCount > 0)
                                    <div class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-white font-semibold text-sm mb-2">
                                        +{{ $extraCount }} More
                                    </div>
                                @endif
                                <div class="text-white text-lg font-bold text-center">
                                    {{ $photo->title ?? 'Gallery Item' }}
                                </div>
                            </div>
                        @else
                            <div class="h-64 md:h-72 bg-gray-200 flex items-center justify-center text-gray-500 text-lg font-semibold rounded-2xl">
                                No Image
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif




{{-- Upcoming Events & Games --}}
<section class="bg-gray-200 py-12 border-t">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Section Header --}}
        <div class="text-center mb-10">
            <h2 class="text-4xl md:text-7xl text-gray-800 font-bold" style="font-family: 'Poppins', sans-serif;">
                Competitions Ahead
            </h2>
            <p class="text-gray-500 mt-2 text-sm md:text-lg">Check out the upcoming events and games</p>
        </div>

        {{-- Upcoming Events --}}
        <h3 class="text-2xl md:text-4xl font-bold text-gray-800 mb-6" style="font-family: 'Poppins', sans-serif;">Upcoming Events</h3>
        @if($upcomingEvents->isEmpty())
            <p class="text-gray-600 text-sm">No upcoming events.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingEvents as $event)
                    <a href="{{ route('viewer.events') }}#event-{{ $event->id }}" 
                       class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-5 flex flex-col justify-between group cursor-pointer relative overflow-hidden">

                        {{-- Content --}}
                        <div>
                            <h4 class="text-lg md:text-xl text-gray-800 group-hover:text-indigo-600 transition" style="font-family: 'Poppins', sans-serif;">
                                {{ $event->name }}
                            </h4>
                            <p class="text-gray-400 text-xs mt-1">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                            </p>
                        </div>

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-indigo-50/20 opacity-0 group-hover:opacity-100 transition rounded-2xl pointer-events-none"></div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Upcoming Games --}}
        <h3 class="text-2xl md:text-4xl font-bold text-gray-800 mt-12 mb-6" style="font-family: 'Poppins', sans-serif;">Upcoming Games</h3>
        @if($upcomingGames->isEmpty())
            <p class="text-gray-600 text-sm">No upcoming games.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingGames as $game)
                    <a href="{{ route('viewer.games') }}#game-{{ $game->id }}" 
                       class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-5 flex flex-col justify-between group cursor-pointer relative overflow-hidden">

                        {{-- Content --}}
                        <div>
                            <h4 class="text-lg md:text-xl text-gray-800 group-hover:text-teal-600 transition" style="font-family: 'Poppins', sans-serif;">
                                {{ $game->team_a }} vs {{ $game->team_b }}
                            </h4>
                            <p class="text-gray-400 text-xs mt-1">
                                {{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y h:i A') }}
                            </p>
                            <p class="text-gray-400 text-xs mt-1 italic">{{ $game->event->name ?? 'No Event' }}</p>
                        </div>

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-teal-50/20 opacity-0 group-hover:opacity-100 transition rounded-2xl pointer-events-none"></div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>


{{-- Latest Announcements --}}
<section class="bg-gray-50 py-12 border-t">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Section Header --}}
        <div class="text-center mb-10">
            <h2 class="text-4xl md:text-7xl text-gray-800 font-bold" style="font-family: 'Poppins', sans-serif;">
                Latest News & Updates
            </h2>
            <p class="text-gray-500 mt-2 text-sm md:text-lg">Stay informed with the latest announcements and results</p>
        </div>

        {{-- Announcements Grid --}}
        @if($latestAnnouncements->isEmpty())
            <p class="text-center text-gray-600 text-sm">No announcements available.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($latestAnnouncements as $announcement)
                    <a href="{{ route('viewer.announcements') }}#announcement-{{ $announcement->id }}" 
                       class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-5 flex flex-col justify-between cursor-pointer group">

                        {{-- Content --}}
                        <div>
                            <h4 class="text-lg md:text-xl text-gray-800 group-hover:text-blue-600 transition mb-1" style="font-family: 'Poppins', sans-serif;">
                                {{ $announcement->title }}
                            </h4>
                            <p class="text-gray-400 text-xs mb-2">{{ $announcement->published_at->format('M d, Y') }}</p>
                            <p class="text-gray-600 text-sm">{{ \Illuminate\Support\Str::limit($announcement->content, 120, '...') }}</p>
                        </div>

                        {{-- Hover overlay for NBA-style subtle effect --}}
                        <div class="absolute inset-0 bg-blue-50/20 opacity-0 group-hover:opacity-100 transition rounded-2xl pointer-events-none"></div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>



{{-- Banner Slideshow JS --}}
<script>
let slides = document.querySelectorAll('[data-slide]');
let current = 0;
function showSlide(index){
    slides.forEach((slide,i)=>slide.style.opacity = i===index?'1':'0');
}
setInterval(()=>{ current=(current+1)%slides.length; showSlide(current); },5000);
</script>

{{-- Gallery Modal JS --}}
<script>
const photosData = @json($photos ?? []);
let currentPhotoIndex = 0;
let currentPhotoSet = null;

function openPhotoModal(id) {
    const photo = photosData.find(p=>p.id===id);
    if(!photo) return;
    currentPhotoIndex = 0;
    currentPhotoSet = photo;
    const modal = document.createElement('div');
    modal.id = 'photoModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full relative flex flex-col">
            <button onclick="closePhotoModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            <div class="p-6 flex flex-col overflow-y-auto max-h-[90vh]" id="modalContent"></div>
        </div>
    `;
    document.body.appendChild(modal);
    renderImage();
}

function renderImage(){
    const content = document.getElementById('modalContent');
    if(!currentPhotoSet) return;
    let html = `<h2 class="text-xl font-semibold mb-2">${currentPhotoSet.title}</h2>`;
    html += `<p class="text-gray-600 mb-4">${currentPhotoSet.description}</p>`;
    html += `<div class="relative w-full h-96 flex items-center justify-center mb-4">`;
    html += `<img src="/storage/${currentPhotoSet.photos[currentPhotoIndex]}" class="w-full h-full object-cover rounded">`;
    if(currentPhotoSet.photos.length>1){
        html += `<div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">${currentPhotoIndex+1} / ${currentPhotoSet.photos.length}</div>`;
    }
    html += `</div>`;
    content.innerHTML = html;
}

function closePhotoModal(){
    const modal = document.getElementById('photoModal');
    if(modal) modal.remove();
    currentPhotoIndex=0;
    currentPhotoSet=null;
}
</script>

@endsection
