@extends('layouts.viewer')

@section('page-title', 'About Us')

@section('content')
<div class="bg-black min-h-screen text-white py-12">

    {{-- =======================
         Header / System Overview
    ======================== --}}
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

    <div class="max-w-7xl mx-auto px-4 text-center mb-20">
        <h1 class="text-7xl font-bold text-red-600 mb-4" style="font-family: 'Poppins', sans-serif;">ESRRS – System Overview</h1>
        <p class="text-gray-300 text-lg mx-auto max-w-3xl" style="font-family: 'Poppins', sans-serif;">
            {!! $fsDeveloper && $fsDeveloper->description 
                ? nl2br(e($fsDeveloper->description)) 
                : 'CEMS – CAVRAA Event Monitoring System is designed to streamline and automate the management of events, games, and participants for CAVRAA tournaments. Stay on top of schedules, winners, and all tournament details with ease.' 
            !!}
        </p>
    </div>

    {{-- =======================
     Developer Profiles
======================== --}}
<div class="max-w-7xl mx-auto px-4 mb-16">

    {{-- CENTERED H1 --}}
    <h1 class="text-5xl font-bold text-red-600 mb-12 text-center" style="font-family: 'Poppins', sans-serif;">
        System Developers
    </h1>

    {{-- GRID FOR DEVELOPER CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        {{-- Full Stack Developer (Highlighted Hero) --}}
        @if($fsDeveloper)
        <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white border border-gray-300 rounded-2xl shadow-2xl p-10 flex flex-col items-center text-center text-black">
            <img src="{{ $fsDeveloper->photo ? asset('storage/' . $fsDeveloper->photo) : 'https://via.placeholder.com/200?text=Photo' }}" 
                 alt="{{ $fsDeveloper->name }}" 
                 class="h-52 w-52 object-cover rounded-full border-4 border-red-600 mb-6">
            <h2 class="text-5xl font-bold mb-2">{{ $fsDeveloper->name }}</h2>
            <p class="mb-1 text-lg">{{ $fsDeveloper->email }}</p>
            <p class="mb-2 text-lg">{{ $fsDeveloper->contact }}</p>
            <p class="text-white bg-red-600 px-6 py-2 rounded-full mb-4 inline-block">{{ $fsDeveloper->role }}</p>
        </div>
        @endif

        {{-- Other Developers --}}
        @foreach($otherDevelopers as $dev)
        <div class="bg-white border border-gray-300 rounded-xl shadow-lg p-6 flex flex-col items-center text-center text-black">
            <img src="{{ $dev->photo ? asset('storage/' . $dev->photo) : 'https://via.placeholder.com/150?text=Photo' }}" 
                 alt="{{ $dev->name }}" 
                 class="h-36 w-36 object-cover rounded-full border-2 border-gray-800 mb-4">
            <h3 class="text-2xl font-semibold mb-1">{{ $dev->name }}</h3>
            <p class="mb-1">{{ $dev->email }}</p>
            <p class="mb-2">{{ $dev->contact }}</p>
            @if($dev->role)
                <p class="text-sm text-white bg-blue-600 px-4 py-1 rounded-full">{{ $dev->role }}</p>
            @endif
        </div>
        @endforeach

    </div>
</div>

    {{-- =======================
         Feedback Form (matched size)
    ======================== --}}
    <div class="max-w-7xl mx-auto px-4 mb-16">
        <div class="bg-white border border-gray-300 rounded-xl shadow-lg p-8 text-black max-w-3xl mx-auto">
            <h3 class="text-4xl font-bold text-black mb-4" style="font-family: 'Poppins', sans-serif;">Send Feedback</h3>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('viewer.feedbacks.send') }}" method="POST" class="space-y-4 text-black">
                @csrf

                <div>
                    <label class="block font-medium">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 bg-white text-black focus:ring focus:ring-red-500 focus:border-red-600">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 bg-white text-black focus:ring focus:ring-red-500 focus:border-red-600">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 bg-white text-black focus:ring focus:ring-red-500 focus:border-red-600">
                    @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium">Message</label>
                    <textarea name="message" rows="5"
                              class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 bg-white text-black focus:ring focus:ring-red-500 focus:border-red-600">{{ old('message') }}</textarea>
                    @error('message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit"
                            class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded transition">
                        Send Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection