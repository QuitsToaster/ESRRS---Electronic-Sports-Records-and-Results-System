@extends('layouts.viewer')

@section('page-title', 'About Us')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 py-16">
    @php
        // Get all developers
        $developers = \App\Models\SystemDeveloper::all();

        // Locate the first developer whose role contains "Full Stack Developer"
        $fsDeveloper = $developers->first(function ($dev) {
            return str_contains($dev->role, 'Full Stack Developer');
        });

        // All other developers
        $otherDevelopers = $developers->filter(function ($dev) {
            return !str_contains($dev->role, 'Full Stack Developer');
        });
    @endphp

    {{-- =======================
         Hero Section
    ======================== --}}
    <div class="max-w-4xl mx-auto px-4 text-center mb-24">
        <div class="inline-block mb-4">
            <span class="px-4 py-2 bg-red-600/10 text-red-500 rounded-full text-sm font-semibold tracking-wider">
                SYSTEM OVERVIEW
            </span>
        </div>
        <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 tracking-tight">
            ESRRS
        </h1>
        <div class="w-24 h-1 bg-red-600 mx-auto mb-8"></div>
        <p class="text-gray-300 text-lg md:text-xl leading-relaxed max-w-3xl mx-auto">
            {!! $fsDeveloper && $fsDeveloper->description 
                ? nl2br(e($fsDeveloper->description)) 
                : 'CEMS – CAVRAA Event Monitoring System is designed to streamline and automate the management of events, games, and participants for CAVRAA tournaments. Stay on top of schedules, winners, and all tournament details with ease.' 
            !!}
        </p>
    </div>

    {{-- =======================
         Developers Section
    ======================== --}}
    <div class="max-w-7xl mx-auto px-4 mb-24">
        <div class="text-center mb-16">
            <span class="px-4 py-2 bg-blue-600/10 text-blue-500 rounded-full text-sm font-semibold tracking-wider">
                TEAM
            </span>
            <h2 class="text-4xl md:text-5xl font-bold text-white mt-4 mb-4">System Developers</h2>
            <p class="text-gray-400">The minds behind ESRRS</p>
        </div>

        {{-- Full Stack Developer (Featured) --}}
        @if($fsDeveloper)
        <div class="mb-16">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-red-800 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
                <div class="relative bg-gray-800 rounded-2xl p-8 md:p-12 flex flex-col md:flex-row items-center gap-8 border border-gray-700">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-red-600 to-red-800 rounded-full blur opacity-50"></div>
                            <img src="{{ $fsDeveloper->photo ? asset('storage/' . $fsDeveloper->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($fsDeveloper->name) . '&size=200&background=dc2626&color=fff' }}" 
                                 alt="{{ $fsDeveloper->name }}" 
                                 class="relative h-40 w-40 md:h-48 md:w-48 object-cover rounded-full border-4 border-gray-800">
                        </div>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h3 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ $fsDeveloper->name }}</h3>
                        <p class="text-gray-400 mb-3">{{ $fsDeveloper->email }} • {{ $fsDeveloper->contact }}</p>
                        <div class="inline-block px-6 py-2 bg-red-600 text-white rounded-full text-sm font-semibold mb-4">
                            {{ $fsDeveloper->role }}
                        </div>
                        <div class="flex gap-3 justify-center md:justify-start">
                            <a href="https://github.com/QuitsToaster" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-gray-300 hover:bg-red-600 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.03-2.682-.103-.253-.447-1.27.098-2.646 0 0 .84-.269 2.75 1.025.8-.223 1.65-.334 2.5-.334.85 0 1.7.111 2.5.334 1.91-1.294 2.75-1.025 2.75-1.025.545 1.376.201 2.393.099 2.646.64.698 1.03 1.591 1.03 2.682 0 3.841-2.337 4.687-4.565 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.161 22 16.418 22 12c0-5.523-4.477-10-10-10z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-gray-300 hover:bg-red-600 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Other Developers Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($otherDevelopers as $dev)
            <div class="group relative">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-gray-600 to-gray-700 rounded-xl blur opacity-0 group-hover:opacity-25 transition duration-300"></div>
                <div class="relative bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition-all duration-300">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative mb-4">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full blur opacity-0 group-hover:opacity-50 transition duration-300"></div>
                            <img src="{{ $dev->photo ? asset('storage/' . $dev->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($dev->name) . '&size=120&background=2563eb&color=fff' }}" 
                                 alt="{{ $dev->name }}" 
                                 class="relative h-28 w-28 object-cover rounded-full border-2 border-gray-700 group-hover:border-blue-500 transition-colors">
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">{{ $dev->name }}</h3>
                        <p class="text-sm text-gray-400 mb-2">{{ $dev->email }}</p>
                        <p class="text-sm text-gray-400 mb-3">{{ $dev->contact }}</p>
                        @if($dev->role)
                            <span class="px-4 py-1.5 bg-blue-600/20 text-blue-400 rounded-full text-xs font-semibold border border-blue-600/30">
                                {{ $dev->role }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- =======================
         Feedback Form
    ======================== --}}
    <div class="max-w-3xl mx-auto px-4">
        <div class="relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-red-800 rounded-2xl blur opacity-25"></div>
            <div class="relative bg-gray-800 rounded-2xl p-8 md:p-10 border border-gray-700">
                <div class="text-center mb-8">
                    <span class="px-4 py-2 bg-red-600/10 text-red-500 rounded-full text-sm font-semibold tracking-wider">
                        GET IN TOUCH
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mt-4 mb-2">Send Feedback</h2>
                    <p class="text-gray-400">We'd love to hear from you</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('viewer.feedbacks.send') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror">
                            @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Subject</label>
                        <input type="text" name="subject" value="{{ old('subject') }}"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('subject') border-red-500 @enderror">
                        @error('subject') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Message</label>
                        <textarea name="message" rows="5"
                                  class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Send Feedback
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection