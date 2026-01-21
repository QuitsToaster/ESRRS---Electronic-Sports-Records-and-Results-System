@extends('layouts.app')

@section('page-title', 'Add User')

@section('content')

<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Create a user and assign a role with specific system access.
    </p>
</div>

<div class="bg-white shadow rounded-lg p-6 w-full">

    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Name --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
            <select name="role_id" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                <option value="">-- Select Role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">
                If the role does not exist, add a new role below.
            </p>
        </div>

        {{-- Add New Role --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">New Role (Optional)</label>
            <input type="text" name="new_role" value="{{ old('new_role') }}"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500"
                placeholder="e.g., Coordinator, Event Organizer">
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('users.index') }}" class="px-4 py-2 rounded border border-gray-400 text-gray-700 hover:bg-gray-100">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 rounded bg-green-600 text-white font-semibold hover:bg-green-700 shadow">
                Create User
            </button>
        </div>

    </form>
</div>

@endsection
