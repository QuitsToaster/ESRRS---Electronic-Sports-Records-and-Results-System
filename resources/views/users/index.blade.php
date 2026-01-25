@extends('layouts.app')

@section('page-title', 'All Users')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of system users, their roles, and sidebar access
        </p>
    </div>
    <a href="{{ route('users.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
       + Add User
    </a>
</div>

{{-- Search Bar --}}
<form method="GET" action="{{ route('users.index') }}" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search users..."
           class="flex-1 border border-gray-300 rounded px-3 py-2">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>
    @if(request('search'))
        <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700 ml-2">Clear</a>
    @endif
</form>

@if($users->isEmpty())
    <div class="bg-white border border-gray-300 rounded-lg p-8 text-center text-gray-500 shadow-sm">
        No users found.
    </div>
@else
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 border-b border-gray-300">
                <tr>
                    <th class="px-4 py-2 text-left text-gray-700">Name</th>
                    <th class="px-4 py-2 text-left text-gray-700">Email</th>
                    <th class="px-4 py-2 text-left text-gray-700">Role</th>
                    <th class="px-4 py-2 text-left text-gray-700">Access</th>
                    <th class="px-4 py-2 text-left text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition">
                    <td class="px-4 py-2 font-medium text-gray-800">{{ $user->name }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ $user->email }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ $user->role->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ $user->access ? implode(', ', $user->access) : '-' }}
                    </td>
                    <td class="px-4 py-2 flex items-center gap-2">
                        <a href="{{ route('users.edit', $user) }}"
                           class="text-yellow-600 hover:text-yellow-800 font-medium">
                            Edit
                        </a>

                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
