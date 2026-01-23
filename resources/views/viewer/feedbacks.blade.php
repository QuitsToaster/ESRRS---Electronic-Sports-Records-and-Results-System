@extends('layouts.viewer')

@section('page-title', 'Feedbacks')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">

    <h2 class="text-3xl font-bold text-gray-800 mb-6">Feedback & System Information</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

      {{-- =======================
     Developer Info / System Overview
======================== --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 flex flex-col items-center text-center">
    @php
        // Fetch the first developer (or multiple if needed)
        $developer = \App\Models\SystemDeveloper::first();
    @endphp

    {{-- Big title --}}
    <h2 class="text-3xl font-bold text-gray-800 mb-6">System Developer</h2>

    @if($developer)
        <img src="{{ $developer->photo ? asset('storage/' . $developer->photo) : 'https://via.placeholder.com/150?text=Photo' }}" 
             alt="{{ $developer->name }}" 
             class="h-32 w-32 object-cover rounded-full border mb-4">

        <h3 class="text-xl font-semibold text-gray-800">{{ $developer->name }}</h3>
        <p class="text-gray-600 mb-1">{{ $developer->email }}</p>
        <p class="text-gray-600 mb-2">{{ $developer->contact }}</p>

        {{-- Role --}}
        @if($developer->role)
            <p class="text-sm text-white bg-blue-600 px-4 py-1 rounded">
                {{ $developer->role }}
            </p>
        @endif

    @else
        <div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center mb-4 text-gray-500">
            N/A
        </div>
        <p class="text-gray-600 mb-1">Developer Name</p>
        <p class="text-gray-600 mb-2">Email / Contact</p>
        <p class="text-sm text-white bg-blue-600 px-4 py-1 rounded">
            Role
        </p>
    @endif

    <hr class="my-4 border-gray-300 w-full">

    {{-- System Overview --}}
    <h4 class="text-lg font-semibold text-gray-800 mb-2">System Overview</h4>
    <p class="text-gray-600 text-sm text-justify">
        @if($developer && $developer->description)
            {!! nl2br(e($developer->description)) !!}
        @else
            CEMS – CAVRAA Event Monitoring System is designed to streamline and automate the management of events, games, and participants for CAVRAA tournaments.
        @endif
    </p>
</div>




        {{-- =======================
             Feedback Form
        ======================== --}}
        <div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Send Feedback</h3>
            <p class="text-gray-500 mb-6">Encountered a problem? Want to share a suggestion? Send us your feedback below.</p>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('viewer.feedbacks.send') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                    @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Message</label>
                    <textarea name="message" rows="5"
                              class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">{{ old('message') }}</textarea>
                    @error('message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
                        Send Feedback
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
