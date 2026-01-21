@extends('layouts.app')

@section('page-title', 'Officials')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold">Officials</h2>
    <a href="{{ route('officials.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Add Official
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-left table-auto">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Sport</th>
                <th class="px-4 py-2 w-40">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($officials as $official)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $official->name }}</td>
                    <td class="px-4 py-2">{{ $official->sport }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        {{-- Edit --}}
                        <a href="{{ route('officials.edit', $official) }}"
                           class="text-blue-600 hover:text-blue-800 font-semibold mr-2">
                            Edit
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('officials.destroy', $official) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this official?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-800 font-semibold">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3"
                        class="px-4 py-4 text-center text-gray-500">
                        No officials found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
