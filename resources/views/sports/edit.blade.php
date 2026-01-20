@extends('layouts.app')

@section('page-title', 'Edit Sport')

@section('content')
<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Edit Sport</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sports.update', $sport->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Sport Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $sport->name) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('sports.index') }}" class="mr-2 px-4 py-2 rounded bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600">Cancel</a>
            <button type="submit" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Update</button>
        </div>
    </form>
</div>
@endsection
