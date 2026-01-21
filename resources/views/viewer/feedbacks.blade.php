@extends('layouts.viewer')

@section('page-title', 'Feedbacks')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Send Feedback</h2>
    <p class="text-gray-500 mb-6">Encountered a problem? Want to share a suggestion? Send us your feedback below.</p>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('viewer.feedbacks.send') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
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
@endsection
