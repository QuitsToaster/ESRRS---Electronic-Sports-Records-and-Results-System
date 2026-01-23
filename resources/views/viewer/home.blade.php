@extends('layouts.viewer')

@section('page-title', 'Home')

@section('content')

{{-- Banner Slideshow --}}
<div class="relative w-full h-64 md:h-96 rounded-lg overflow-hidden shadow-lg">
    @foreach($banners as $index => $banner)
        <div class="absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide>
            <img src="{{ asset('storage/' . $banner->image) }}" class="w-full h-full object-cover">
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

    {{-- Latest Gallery --}}
    @php
        use App\Models\GalleryPhoto;
        $photos = GalleryPhoto::latest()->take(3)->get(); // latest 3 uploads
    @endphp

    @if($photos->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Latest Gallery Uploads</h3>

        <div class="flex gap-4 overflow-x-auto pb-2">
            @foreach($photos as $photo)
                @php
                    $firstImage = $photo->photos[0] ?? null;
                    $extraCount = count($photo->photos) - 1;
                @endphp
                <div class="min-w-[300px] border rounded-lg overflow-hidden shadow-sm cursor-pointer relative flex-shrink-0" onclick="openPhotoModal({{ $photo->id }})">
                    {{-- Title --}}
                    <div class="p-2 font-semibold bg-gray-100">{{ $photo->title }}</div>

                    {{-- First Image --}}
                    @if($firstImage)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $firstImage) }}" alt="Photo" class="w-full h-48 object-cover rounded">
                            @if($extraCount > 0)
                                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                    +{{ $extraCount }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                            No Image
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Upcoming Events & Games --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
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

</div>

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
