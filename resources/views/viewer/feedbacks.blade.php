@extends('layouts.viewer')

@section('page-title', 'Feedbacks')

@section('content')
<div class="bg-black min-h-screen text-white py-12">

    {{-- =======================
         Header / System Overview (from Developer Description)
    ======================== --}}
    @php
        $developer = \App\Models\SystemDeveloper::first();
        $systemDescription = $developer && $developer->description
            ? nl2br(e($developer->description))
            : 'CEMS – CAVRAA Event Monitoring System is designed to streamline and automate the management of events, games, and participants for CAVRAA tournaments. Stay on top of schedules, winners, and all tournament details with ease.';
    @endphp
    <div class="max-w-7xl mx-auto px-4 text-center mb-40">
        <h1 class="text-7xl font-bold text-red-600 mb-4" style="font-family: 'Poppins', sans-serif;">CEMS – System Overview</h1>
        <p class="text-gray-300 text-lg mx-auto max-w-3xl" style="font-family: 'Poppins', sans-serif;">{!! $systemDescription !!}</p>
    </div>

    {{-- =======================
     Developer Info & Feedback Side by Side (Maximized Space)
======================== --}}
<div class="max-w-7xl mx-auto px-4 text-center mb-16">
        <h1 class="text-6xl font-bold text-white mb-4" style="font-family: 'Poppins', sans-serif;">Contact Us</h1>
        <p class="mb-6 text-white" style="font-family: 'Poppins', sans-serif;">
            Encountered a problem? Have a suggestion? Let us know by sending feedback below.
        </p>
    </div>
<div class="w-full mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 px-6 lg:px-16">

    {{-- Developer Info --}}
    <div class="flex-1 bg-white border border-gray-300 rounded-xl shadow-lg p-8 flex flex-col items-center text-center text-black">

        <h2 class="text-4xl font-bold text-black mb-6" style="font-family: 'Poppins', sans-serif;">System Developer</h2>

        @if($developer)
            <img src="{{ $developer->photo ? asset('storage/' . $developer->photo) : 'https://via.placeholder.com/150?text=Photo' }}" 
                 alt="{{ $developer->name }}" 
                 class="h-36 w-36 object-cover rounded-full border-4 border-black mb-4">

            <h3 class="text-xl font-semibold">{{ $developer->name }}</h3>
            <p class="mb-1">{{ $developer->email }}</p>
            <p class="mb-2">{{ $developer->contact }}</p>

            @if($developer->role)
                <p class="text-sm text-white bg-blue-600 px-4 py-1 rounded-full">
                    {{ $developer->role }}
                </p>
            @endif

        @else
            <div class="h-36 w-36 rounded-full bg-gray-200 flex items-center justify-center mb-4 text-gray-500">
                N/A
            </div>
            <p class="mb-1">Developer Name</p>
            <p class="mb-2">Email / Contact</p>
            <p class="text-sm text-white bg-red-600 px-4 py-1 rounded-full">
                Role
            </p>
        @endif
    </div>

    {{-- Feedback Form --}}
    <div class="flex-1 bg-white border border-gray-300 rounded-xl shadow-lg p-8 text-black">
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
