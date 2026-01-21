@extends('layouts.viewer')

@section('page-title', 'Announcements')

@section('content')
<div class="mb-6">  
    <h2 class="text-2xl font-semibold text-gray-800">Announcements</h2>
    <p class="text-gray-500 text-sm">Latest updates, schedule changes, and results</p>
</div>

@if($announcements->isEmpty())
    <div class="text-center py-12 text-gray-500">
        No announcements available.
    </div>
@else
    <div class="space-y-6">
        @foreach($announcements as $announcement)
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                {{-- Title & Type --}}
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-lg font-semibold text-blue-600">{{ $announcement->title }}</h3>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        @switch($announcement->type)
                            @case('General') bg-gray-100 text-gray-800 @break
                            @case('Schedule Update') bg-yellow-100 text-yellow-800 @break
                            @case('Venue Update') bg-orange-100 text-orange-800 @break
                            @case('Results') bg-green-100 text-green-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch">
                        {{ $announcement->type }}
                    </span>
                </div>

                {{-- Linked Event --}}
                @if($announcement->event)
                    <p class="text-sm text-gray-500 mb-2">
                        <span class="font-semibold">Event:</span> {{ $announcement->event->name }}
                    </p>
                @endif

                {{-- Content --}}
                <p class="text-gray-700 mb-3">
                    {{ \Illuminate\Support\Str::limit($announcement->content, 150, '...') }}
                </p>

                {{-- Attachments Preview --}}
                @if($announcement->files && $announcement->files->count() > 0)
                    <div class="mb-3 flex flex-wrap gap-3" x-data="{ open: false, image: '' }">
                        @foreach($announcement->files as $file)
                            @php
                                $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                            @endphp

                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                {{-- Clickable image preview --}}
                                <button @click="open = true; image='{{ asset('storage/'.$file->file_path) }}'"
                                        class="block w-20 h-20 overflow-hidden rounded shadow hover:scale-105 transform transition focus:outline-none">
                                    <img src="{{ asset('storage/'.$file->file_path) }}" alt="Attachment" class="w-full h-full object-cover">
                                </button>
                            @else
                                {{-- Document/PDF icon preview --}}
                                <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank"
                                   class="flex items-center justify-center w-20 h-20 bg-gray-100 rounded shadow text-xs text-gray-700 font-semibold hover:bg-gray-200 transition text-center p-1">
                                    {{ strtoupper($ext) }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Modal / Lightbox --}}
                        <div x-show="open" x-transition.opacity
                             class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
                             style="display: none;">
                            <div class="relative max-w-3xl w-full">
                                <button @click="open = false" 
                                        class="absolute top-2 right-2 text-white text-2xl font-bold hover:text-gray-300">&times;</button>
                                <img :src="image" class="w-full rounded shadow-lg">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Published Date --}}
                <p class="text-gray-400 text-sm">
                    Published on {{ $announcement->published_at->format('M d, Y h:i A') }}
                </p>
            </div>
        @endforeach
    </div>
@endif

{{-- Alpine.js for modal --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
