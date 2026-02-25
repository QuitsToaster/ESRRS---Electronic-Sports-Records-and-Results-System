@extends('layouts.viewer')

@section('page-title', 'Announcements')

@section('content')

<div class="bg-gray-100 min-h-screen">

    {{-- 🔴 TOP NEWS HEADER --}}
    <div class="bg-red-700 text-white px-6 py-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-3xl md:text-5xl font-extrabold uppercase tracking-wide" style="font-family: 'Poppins'">
                    The Bulletin.
                </h2>
                <p class="text-sm md:text-base opacity-90" style="font-family: 'Poppins'">
                    Announcements • Schedules • Official Results
                </p>
            </div>

            <div class="bg-yellow-400 text-black px-4 py-2 font-bold uppercase text-sm tracking-widest rounded shadow">
                Public Advisory
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

        {{-- 🔎 SEARCH & FILTER BAR --}}
        <div class="bg-white border border-gray-200 p-4 rounded-lg shadow flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div class="font-bold uppercase tracking-wide text-sm text-gray-700">
                Filter News Feed
            </div>

            <form method="GET" action="{{ route('viewer.announcements') }}" class="flex flex-wrap gap-2 items-center">

                <input type="text" name="search" value="{{ request('search') ?? '' }}"
                       placeholder="Search announcements..."
                       class="w-full sm:w-64 border border-gray-300 rounded-md px-4 py-2
                              focus:outline-none focus:ring-2 focus:ring-red-500 transition">

                <select name="type"
                        class="w-full sm:w-48 border border-gray-300 rounded-md px-4 py-2
                               focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                    <option value="">All Types</option>
                    @php
                        $types = ['General','Schedule Update','Venue Update','Results'];
                    @endphp
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-semibold shadow transition">
                    Search
                </button>

                @if(request('search') || request('type'))
                    <a href="{{ route('viewer.announcements') }}"
                       class="text-gray-500 hover:text-gray-800 px-2 py-2 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- 📰 ANNOUNCEMENTS FEED --}}
        @if($announcements->isEmpty())

            <div class="bg-white text-gray-500 text-center py-16 rounded-lg shadow">
                No announcements available.
            </div>

        @else

            <div class="space-y-6">

                @foreach($announcements as $announcement)

                <div x-data="{ open: false }"
                     class="bg-white rounded-xl shadow-lg border-l-4
                     @switch($announcement->type)
                        @case('General') border-gray-400 @break
                        @case('Schedule Update') border-yellow-500 @break
                        @case('Venue Update') border-orange-500 @break
                        @case('Results') border-green-500 @break
                        @default border-gray-400
                     @endswitch
                     p-6">

                    {{-- LOWER THIRD HEADER --}}
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">

                        <div>
                            <h3 class="text-xl md:text-2xl font-bold uppercase tracking-wide text-gray-800">
                                {{ $announcement->title }}
                            </h3>

                            @if($announcement->event)
                                <p class="text-sm text-gray-500 mt-1">
                                    Event: {{ $announcement->event->name }}
                                </p>
                            @endif
                        </div>

                        <span class="px-3 py-1 text-xs font-bold uppercase tracking-wide rounded-full
                            @switch($announcement->type)
                                @case('General') bg-gray-200 text-gray-800 @break
                                @case('Schedule Update') bg-yellow-200 text-yellow-900 @break
                                @case('Venue Update') bg-orange-200 text-orange-900 @break
                                @case('Results') bg-green-200 text-green-900 @break
                                @default bg-gray-200 text-gray-800
                            @endswitch">
                            {{ $announcement->type }}
                        </span>

                    </div>

                    {{-- CONTENT --}}
                    <div class="text-gray-700 text-sm md:text-base leading-relaxed">
                        <p x-show="!open">
                            {{ \Illuminate\Support\Str::limit($announcement->content, 180, '...') }}
                        </p>

                        <p x-show="open" x-transition>
                            {{ $announcement->content }}
                        </p>

                        <button @click="open = !open"
                                class="text-red-600 font-semibold hover:underline text-sm mt-2">
                            <span x-text="open ? 'Show Less' : 'Read Full Story'"></span>
                        </button>
                    </div>

                    {{-- ATTACHMENTS --}}
                    @if($announcement->files && $announcement->files->count() > 0)
                        <div class="mt-4 flex flex-wrap gap-3" x-data="{ openModal: false, image: '' }">

                            @foreach($announcement->files as $file)
                                @php
                                    $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                                @endphp

                                @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                    <button @click="openModal = true; image='{{ asset('storage/'.$file->file_path) }}'"
                                            class="block w-20 h-20 overflow-hidden rounded-lg shadow hover:scale-105 transition">
                                        <img src="{{ asset('storage/'.$file->file_path) }}" class="w-full h-full object-cover">
                                    </button>
                                @else
                                    <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank"
                                       class="flex items-center justify-center w-20 h-20 bg-gray-100 border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-200 transition">
                                        {{ strtoupper($ext) }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- IMAGE MODAL --}}
                            <div x-show="openModal" x-transition.opacity
                                 class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                                <div class="relative max-w-3xl w-full px-4">
                                    <button @click="openModal = false"
                                            class="absolute top-2 right-4 text-white text-2xl font-bold">&times;</button>
                                    <img :src="image" class="w-full rounded-xl shadow-lg">
                                </div>
                            </div>

                        </div>
                    @endif

                    {{-- FOOTER STRIP --}}
                    <div class="mt-4 pt-3 border-t border-gray-200 text-xs text-gray-500 flex justify-between">
                        <span>
                            Published {{ $announcement->published_at->format('M d, Y h:i A') }}
                        </span>
                        <span class="uppercase tracking-wide font-semibold">
                            eSRRS News Desk
                        </span>
                    </div>

                </div>

                @endforeach

            </div>

        @endif

    </div>

</div>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection