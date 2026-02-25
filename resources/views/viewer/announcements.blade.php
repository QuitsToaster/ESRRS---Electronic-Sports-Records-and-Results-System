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
                Filter Feed
            </div>

            <form method="GET" action="{{ route('viewer.announcements') }}" class="flex flex-wrap gap-2 items-center">

                <input type="text"
                       name="search"
                       value="{{ request('search') ?? '' }}"
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

        {{-- 📰 ANNOUNCEMENTS LIST --}}
        @if($announcements->isEmpty())

            <div class="bg-white text-gray-500 text-center py-16 rounded-lg shadow">
                No announcements available.
            </div>

        @else

            <div class="space-y-4">

                @foreach($announcements as $announcement)

                <div x-data="{ openModal: false }"
                     class="bg-white rounded-xl shadow border-l-4
                     @switch($announcement->type)
                        @case('General') border-gray-400 @break
                        @case('Schedule Update') border-yellow-500 @break
                        @case('Venue Update') border-orange-500 @break
                        @case('Results') border-green-500 @break
                        @default border-gray-400
                     @endswitch
                     p-5">

                    {{-- TITLE ROW --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                        <div>
                            <h3 class="text-xl md:text-2xl font-bold text-gray-800">
                                {{ $announcement->title }}
                            </h3>

                            @if($announcement->event)
                                <p class="text-lg text-gray-500 mt-1">
                                    Event: {{ $announcement->event->name }}
                                </p>
                            @endif

                            <button @click="openModal = true"
                                    class="text-red-600 font-semibold hover:underline text-xs">
                                Read Full Story
                            </button>
                                                
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-xs font-bold uppercase rounded-full
                                @switch($announcement->type)
                                    @case('General') bg-gray-200 text-gray-800 @break
                                    @case('Schedule Update') bg-yellow-200 text-yellow-900 @break
                                    @case('Venue Update') bg-orange-200 text-orange-900 @break
                                    @case('Results') bg-green-200 text-green-900 @break
                                @endswitch">
                                {{ $announcement->type }}
                            </span>
                        </div>

                    </div>

                    {{-- META --}}
                    <div class="mt-3 text-xs text-gray-500 flex justify-between">
                        <span>
                            Published {{ $announcement->published_at->format('M d, Y h:i A') }}
                        </span>
                        <span class="uppercase tracking-wide font-semibold">
                            eSRRS News Desk
                        </span>
                    </div>

                    {{-- ================= MODAL ================= --}}
<div x-show="openModal"
     x-transition.opacity
     @keydown.escape.window="openModal = false"
     class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-6"
     style="display:none">

    <div @click.away="openModal = false"
         class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all duration-300 scale-95 x-show.transition.scale.100">

        {{-- MODAL HEADER --}}
        <div class="flex justify-between items-start p-6 bg-gradient-to-r from-red-600 to-red-400 text-white">
            <div>
                <h3 class="text-2xl font-extrabold tracking-tight">
                    {{ $announcement->title }}
                </h3>
                <p class="text-sm opacity-80 mt-1">
                    {{ $announcement->type }}
                </p>
            </div>

            <button @click="openModal = false"
                    class="text-white hover:text-gray-200 text-3xl font-bold leading-none transition-transform transform hover:scale-110">
                &times;
            </button>
        </div>

        {{-- MODAL BODY --}}
        <div class="p-6 space-y-6 bg-gray-50">

            <p class="text-gray-800 leading-relaxed whitespace-pre-line text-base">
                {{ $announcement->content }}
            </p>

            {{-- ATTACHMENTS --}}
            @if($announcement->files && $announcement->files->count() > 0)
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">
                        Attachments
                    </h4>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($announcement->files as $file)
                            @php
                                $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                            @endphp

                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank"
                                   class="group relative overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1 hover:scale-105">
                                    <img src="{{ asset('storage/'.$file->file_path) }}"
                                         class="w-full h-32 object-cover transition-transform duration-300 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white font-semibold">
                                        View
                                    </div>
                                </a>
                            @else
                                <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank"
                                   class="flex items-center justify-center w-full h-32 bg-gray-100 border border-gray-300 rounded-xl shadow hover:shadow-md transition text-gray-700 font-semibold text-sm uppercase hover:bg-gray-200">
                                    {{ strtoupper($ext) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- MODAL FOOTER --}}
        <div class="px-6 py-4 bg-gray-100 border-t flex justify-end space-x-3">
            <button
                    class="px-6 py-2 bg-red-600 text-white rounded-xl font-semibold shadow transition">
                eSRRS News Desk
            </button>
        </div>

    </div>
</div>

                </div>

                @endforeach

            </div>

        @endif

    </div>

</div>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection