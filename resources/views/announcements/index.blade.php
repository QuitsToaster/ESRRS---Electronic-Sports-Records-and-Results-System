@extends('layouts.app')

@section('page-title', 'Announcements & Bulletin Board')

@section('content')
<div class="space-y-6">
    {{-- ================= HEADER WITH GRADIENT ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Announcements & Bulletin Board</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Official notices, schedule updates, and uploaded event documents</p>
                </div>
            </div>
        </div>
        
        <a href="{{ route('announcements.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            New Announcement
        </a>
    </div>

    {{-- ================= INSTRUCTIONS CARD ================= --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50/50 border border-blue-100 rounded-2xl p-5 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-blue-900 mb-2">Quick Guide</h3>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1 text-sm text-blue-800/80">
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        View all official notices and schedule updates
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Use search to quickly find announcements
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Upload and preview attachments (images, PDFs)
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Edit or delete announcements as needed
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ================= FILTERS & STATS ================= --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <form method="GET" action="{{ route('announcements.index') }}" class="flex flex-wrap items-center gap-3">
                {{-- Search --}}
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search announcements..."
                           class="w-full sm:w-80 pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                    Search
                </button>

                @if(request('search'))
                    <a href="{{ route('announcements.index') }}"
                       class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                        Clear
                    </a>
                @endif
            </form>

            {{-- Count Badge --}}
            <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <span class="text-sm text-gray-600">
                    <span class="font-semibold">Total Announcements:</span>
                    <span class="ml-1 font-bold text-gray-900">{{ method_exists($announcements, 'total') ? $announcements->total() : $announcements->count() }}</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Empty State --}}
    @if($announcements->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No announcements posted yet</h3>
            <p class="text-gray-500 mb-4">Get started by creating your first announcement</p>
            <a href="{{ route('announcements.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Announcement
            </a>
        </div>
    @else
        {{-- Announcements List --}}
        <div class="space-y-4">
            @foreach($announcements as $announcement)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-all duration-200">
                    {{-- Header with type badge --}}
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-transparent flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $announcement->title }}</h2>
                                @if($announcement->event)
                                    <p class="text-sm text-gray-500 mt-1">
                                        Related Event: <span class="font-medium text-blue-600">{{ $announcement->event->name }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Type Badge --}}
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($announcement->type === 'Schedule Update') bg-amber-100 text-amber-700 border border-amber-200
                            @elseif($announcement->type === 'Venue Update') bg-purple-100 text-purple-700 border border-purple-200
                            @elseif($announcement->type === 'Results') bg-emerald-100 text-emerald-700 border border-emerald-200
                            @else bg-blue-100 text-blue-700 border border-blue-200
                            @endif">
                            {{ $announcement->type }}
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="px-6 py-4">
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $announcement->content }}
                        </div>
                    </div>

                    {{-- Attachments Preview --}}
                    @if($announcement->files->count())
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.414 6.586a6 6 0 108.484 8.484l6.586-6.586" />
                                </svg>
                                Attachments
                            </h4>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($announcement->files as $file)
                                    @php
                                        $fileUrl = asset('storage/' . $file->file_path);
                                        $extension = strtolower($file->file_type);
                                    @endphp

                                    {{-- IMAGE PREVIEW --}}
                                    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                        <a href="{{ $fileUrl }}" target="_blank"
                                           class="group relative block border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-200">
                                            <img src="{{ $fileUrl }}"
                                                 alt="Attachment Image"
                                                 class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </div>
                                        </a>

                                    {{-- PDF PREVIEW --}}
                                    @elseif($extension === 'pdf')
                                        <a href="{{ $fileUrl }}" target="_blank"
                                           class="group relative block border border-gray-200 rounded-xl overflow-hidden bg-gradient-to-br from-red-50 to-red-100 hover:shadow-lg transition-all duration-200">
                                            <div class="h-32 flex flex-col items-center justify-center">
                                                <svg class="w-10 h-10 text-red-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-xs font-medium text-red-700">PDF Document</span>
                                            </div>
                                        </a>

                                    {{-- OTHER FILE TYPES --}}
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank"
                                           class="flex items-center justify-center h-32 border border-gray-200 rounded-xl bg-gray-50 hover:bg-gray-100 text-sm font-medium text-blue-600 hover:text-blue-700 transition-all duration-200">
                                            <span class="text-center">
                                                <svg class="w-8 h-8 mx-auto mb-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                <span class="text-xs">{{ strtoupper($extension) }}</span>
                                            </span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm">
                        <div class="flex items-center gap-2 text-gray-500">
                            <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($announcement->user->name, 0, 1) }}
                            </div>
                            <span>
                                Posted by <span class="font-medium text-gray-700">{{ $announcement->user->name }}</span>
                            </span>
                            <span class="text-gray-300">•</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $announcement->published_at->format('M d, Y h:i A') }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ route('announcements.edit', $announcement) }}"
                               class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                               title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>

                            <form action="{{ route('announcements.destroy', $announcement) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this announcement?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(method_exists($announcements, 'hasPages') && $announcements->hasPages())
        <div class="mt-6">
            {{ $announcements->withQueryString()->links() }}
        </div>
        @endif
    @endif
</div>
@endsection