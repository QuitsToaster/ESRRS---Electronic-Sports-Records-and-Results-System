@extends('layouts.viewer')

@section('page-title', 'Home')

@section('content')

{{-- Banner Slideshow --}}
<div class="relative w-full h-64 md:h-96 overflow-hidden">
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
        <div class="absolute inset-0 bg-black/30"></div> {{-- Dark overlay for contrast --}}
    </div>

    {{-- Content --}}
    <div class="relative max-w-7xl mx-auto px-6">
        <h3 class="text-3xl font-bold text-white mb-6 flex items-center space-x-2">
            <span>Medal Tally</span>
        </h3>

        @if($medalTally->isEmpty())
            <p class="text-gray-200 text-sm">No medals awarded yet.</p>
        @else
            <div class="grid gap-4 md:gap-6">
                @foreach($medalTally as $index => $school)
                    @php
                        $schoolModel = \App\Models\School::find($school->school_id);
                        $total = $school->gold + $school->silver + $school->bronze;
                    @endphp

                    {{-- Card Row --}}
                    <div class="flex items-center justify-between p-4 md:p-6 bg-white/10 backdrop-blur-md rounded-xl shadow-lg hover:shadow-2xl transition">
                        {{-- Rank --}}
                        <div class="text-white text-lg font-bold w-8 text-center">{{ $index + 1 }}</div>

                        {{-- School Info --}}
                        <div class="flex items-center space-x-4 flex-1">
                            @if($schoolModel && $schoolModel->logo)
                                <img src="{{ asset('storage/' . $schoolModel->logo) }}" alt="Logo"
                                     class="w-12 h-12 object-cover rounded-full border border-white/30">
                            @else
                                <div class="w-12 h-12 bg-white/30 rounded-full flex items-center justify-center text-gray-200 text-xs">N/A</div>
                            @endif
                            <span class="text-white font-semibold text-lg">{{ $schoolModel->name ?? 'Unknown' }}</span>
                        </div>

                        {{-- Medal Counts --}}
                        <div class="flex items-center space-x-6 font-bold">
                            <div class="flex flex-col items-center text-yellow-400">
                                <span class="text-xl">{{ $school->gold }}</span>
                                <span class="text-sm">🥇</span>
                            </div>
                            <div class="flex flex-col items-center text-gray-300">
                                <span class="text-xl">{{ $school->silver }}</span>
                                <span class="text-sm">🥈</span>
                            </div>
                            <div class="flex flex-col items-center text-orange-400">
                                <span class="text-xl">{{ $school->bronze }}</span>
                                <span class="text-sm">🥉</span>
                            </div>
                            <div class="flex flex-col items-center text-white">
                                <span class="text-xl">{{ $total }}</span>
                                <span class="text-sm">Total</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>



{{-- Latest Gallery --}}
@php
    use App\Models\GalleryPhoto;
    $photos = GalleryPhoto::latest()->take(3)->get();
@endphp
@if($photos->isNotEmpty())
<section class="bg-white py-10 border-t">
    <div class="max-w-7xl mx-auto px-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Latest Gallery Uploads</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($photos as $photo)
                @php
                    $firstImage = $photo->photos[0] ?? null;
                    $extraCount = count($photo->photos) - 1;
                @endphp
                <div class="border rounded-lg overflow-hidden shadow-sm cursor-pointer hover:shadow-md transition"
                     onclick="openPhotoModal({{ $photo->id }})">
                    <div class="p-3 font-semibold bg-gray-100">{{ $photo->title }}</div>
                    @if($firstImage)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $firstImage) }}" class="w-full h-48 object-cover">
                            @if($extraCount > 0)
                                <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded">
                                    +{{ $extraCount }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                            No Image
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Upcoming Events & Games --}}
<section class="bg-indigo-50 py-10 border-t">
    <div class="max-w-7xl mx-auto px-6">
        <h3 class="text-xl font-semibold text-indigo-700 mb-4">Upcoming Events</h3>
        @if($upcomingEvents->isEmpty())
            <p class="text-gray-700 text-sm">No upcoming events.</p>
        @else
            <ul class="divide-y">
                @foreach($upcomingEvents as $event)
                    <li class="py-3 flex justify-between items-center text-sm hover:bg-indigo-100 rounded px-2 transition">
                        <span class="font-medium text-indigo-800">{{ $event->name }}</span>
                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        <h3 class="text-xl font-semibold text-teal-700 mt-8 mb-4">Upcoming Games</h3>
        @if($upcomingGames->isEmpty())
            <p class="text-gray-700 text-sm">No upcoming games.</p>
        @else
            <ul class="divide-y">
                @foreach($upcomingGames as $game)
                    <li class="py-3 px-2 hover:bg-teal-100 rounded transition">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-teal-800">{{ $game->team_a }} vs {{ $game->team_b }}</span>
                            <span class="text-gray-500">{{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="text-gray-400 text-xs mt-1">{{ $game->event->name ?? 'No Event' }}</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>

{{-- Announcements --}}
<section class="bg-pink-50 py-10 border-t">
    <div class="max-w-7xl mx-auto px-6">
        <h3 class="text-xl font-semibold text-pink-700 mb-4">Latest Announcements</h3>
        @if($latestAnnouncements->isEmpty())
            <p class="text-gray-700 text-sm">No announcements available.</p>
        @else
            <ul class="divide-y">
                @foreach($latestAnnouncements as $announcement)
                    <li class="py-4 hover:bg-pink-100 rounded px-2 transition">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-pink-800">{{ $announcement->title }}</span>
                            <span class="text-xs text-gray-500">{{ $announcement->published_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($announcement->content, 120) }}</p>
                    </li>
                @endforeach
            </ul>
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
