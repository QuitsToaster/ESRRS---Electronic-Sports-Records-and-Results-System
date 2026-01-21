@extends('layouts.app')

@section('page-title', 'Announcements & Bulletin Board')

@section('content')

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            Official notices, schedule updates, and uploaded event documents
        </p>
    </div>

    <a href="{{ route('announcements.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        + New Announcement
    </a>
</div>

{{-- Search Bar --}}
<form method="GET" action="{{ route('announcements.index') }}" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search announcements..."
           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>
    @if(request('search'))
        <a href="{{ route('announcements.index') }}" class="text-gray-500 hover:text-gray-700 ml-2">Clear</a>
    @endif
</form>

{{-- Empty State --}}
@if($announcements->isEmpty())
    <div class="bg-white border border-gray-300 rounded-lg p-8 text-center text-gray-500 shadow-sm">
        No announcements posted yet.
    </div>
@else

{{-- Announcements List --}}
<div class="space-y-6">

    @foreach($announcements as $announcement)
        <div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">

            {{-- Title & Type --}}
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ $announcement->title }}
                    </h2>

                    @if($announcement->event)
                        <p class="text-sm text-gray-500 mt-1">
                            Related Event:
                            <span class="font-medium">{{ $announcement->event->name }}</span>
                        </p>
                    @endif
                </div>

                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($announcement->type === 'Schedule Update') bg-yellow-100 text-yellow-800
                    @elseif($announcement->type === 'Venue Update') bg-purple-100 text-purple-800
                    @elseif($announcement->type === 'Results') bg-green-100 text-green-800
                    @else bg-blue-100 text-blue-800
                    @endif">
                    {{ $announcement->type }}
                </span>
            </div>

            {{-- Content --}}
            <div class="mt-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ $announcement->content }}
            </div>

            {{-- Attachments Preview --}}
            @if($announcement->files->count())
                <div class="mt-5 border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">
                        📎 Attachments
                    </h4>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($announcement->files as $file)
                            @php
                                $fileUrl = asset('storage/' . $file->file_path);
                                $extension = strtolower($file->file_type);
                            @endphp

                            {{-- IMAGE PREVIEW --}}
                            @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                                <a href="{{ $fileUrl }}" target="_blank"
                                   class="block border rounded overflow-hidden hover:shadow-lg transition">
                                    <img src="{{ $fileUrl }}"
                                         alt="Attachment Image"
                                         class="w-full h-32 object-cover">
                                </a>

                            {{-- PDF PREVIEW --}}
                            @elseif($extension === 'pdf')
                                <a href="{{ $fileUrl }}" target="_blank"
                                   class="block border rounded overflow-hidden hover:shadow-lg transition">
                                    <iframe src="{{ $fileUrl }}"
                                            class="w-full h-32 pointer-events-none"></iframe>
                                </a>

                            {{-- OTHER FILE TYPES --}}
                            @else
                                <a href="{{ $fileUrl }}" target="_blank"
                                   class="flex items-center justify-center h-32 border rounded bg-gray-50 hover:bg-gray-100 text-sm font-medium text-blue-600">
                                    Download {{ strtoupper($extension) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Footer --}}
            <div class="mt-6 flex justify-between items-center text-sm text-gray-500">
                <div>
                    Posted by
                    <span class="font-medium text-gray-700">
                        {{ $announcement->user->name }}
                    </span>
                    • {{ $announcement->published_at->format('M d, Y h:i A') }}
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('announcements.edit', $announcement) }}"
                       class="text-yellow-600 hover:text-yellow-800 font-medium">
                        Edit
                    </a>

                    <form action="{{ route('announcements.destroy', $announcement) }}"
                          method="POST"
                          onsubmit="return confirm('Delete this announcement?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 font-medium">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

        </div>
    @endforeach

</div>
@endif

@endsection
