@extends('layouts.viewer') 

@section('page-title', 'Gallery')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Official Gallery</h2>
            <p class="text-gray-600 mt-1 text-sm">Browse official photos uploaded.</p>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('viewer.gallery') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                   placeholder="Search photos..."
                   class="w-full sm:w-64 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow font-semibold transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('viewer.gallery') }}" class="text-gray-500 hover:text-gray-700 px-2 py-2 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>
</div>

@if($photos->isEmpty())
    <p class="text-gray-500">No photos uploaded yet.</p>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    @foreach($photos as $photo)
        @php
            $firstImage = $photo->photos[0] ?? null;
            $extraCount = count($photo->photos) - 1;
        @endphp

        <div class="border rounded-lg overflow-hidden shadow-sm cursor-pointer relative" onclick="openPhotoModal({{ $photo->id }})">
            {{-- Title --}}
            <div class="p-2 font-semibold bg-gray-100">{{ $photo->title }}</div>

            {{-- First Image --}}
            @if($firstImage)
                <div class="relative">
                    <img src="{{ asset('storage/' . $firstImage) }}" alt="Photo" class="w-full h-48 object-cover rounded">

                    {{-- Extra images watermark --}}
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
@endif

{{-- PHOTO MODAL --}}
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 overflow-auto p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full relative flex flex-col">
        <button onclick="closePhotoModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>

        <div class="p-6 flex flex-col overflow-y-auto max-h-[90vh]" id="modalContent">
            {{-- Content injected by JS --}}
        </div>
    </div>
</div>

<script>
const photosData = @json($photos);
let currentPhotoIndex = 0;
let currentPhotoSet = null;

function openPhotoModal(id) {
    const photo = photosData.find(p => p.id === id);
    if(!photo) return;

    currentPhotoIndex = 0;
    currentPhotoSet = photo;
    const modal = document.getElementById('photoModal');
    const content = document.getElementById('modalContent');

    function renderImage() {
        let html = `<h2 class="text-xl font-semibold mb-2">${photo.title}</h2>`;
        html += `<p class="text-gray-600 mb-4">${photo.description}</p>`;

        html += `<div class="relative w-full h-96 flex items-center justify-center mb-4">`;
        html += `<img src="/storage/${photo.photos[currentPhotoIndex]}" class="w-full h-full object-cover rounded">`;

        if(photo.photos.length > 1) {
            html += `
                <button onclick="prevImage()" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded hover:bg-opacity-70">&larr;</button>
                <button onclick="nextImage()" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded hover:bg-opacity-70">&rarr;</button>
                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                    ${currentPhotoIndex + 1} / ${photo.photos.length}
                </div>
            `;
        }

        html += `</div>`;
        content.innerHTML = html;
    }

    window.prevImage = function() {
        if(currentPhotoIndex > 0) {
            currentPhotoIndex--;
            renderImage();
        }
    }

    window.nextImage = function() {
        if(currentPhotoIndex < photo.photos.length - 1) {
            currentPhotoIndex++;
            renderImage();
        }
    }

    renderImage();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentPhotoIndex = 0;
    currentPhotoSet = null;
}
</script>
@endsection
