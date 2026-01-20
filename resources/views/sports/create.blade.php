@extends('layouts.app')

@section('page-title', 'Add Sport')

@section('content')
<div class="bg-white shadow rounded-lg p-6 w-full">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Add New Sport</h1>
    <p class="text-sm text-gray-600 mb-6">Fill in the sport details below.</p>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sports.store') }}" method="POST" class="space-y-4 w-full">
        @csrf

        <div>
            <label for="name" class="block text-gray-700 font-semibold mb-2">Sport Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
        </div>

        <div class="flex justify-end space-x-2 mt-4">
            <a href="{{ route('sports.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded shadow text-gray-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">Save</button>
        </div>
    </form>
</div>
@endsection
