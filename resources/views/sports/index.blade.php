@extends('layouts.app')

@section('page-title', 'Sports')

@section('content')

{{-- Tabs --}}
<div class="mb-6 border-b border-gray-200">
    <nav class="flex gap-6">
        <a href="{{ route('sports.index') }}"
           class="pb-3 border-b-2 font-semibold
                  {{ request()->routeIs('sports.index')
                      ? 'border-blue-600 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Regular Sports
        </a>

        <a href="{{ route('para-sports.index') }}"
           class="pb-3 border-b-2 font-semibold
                  border-transparent text-gray-500 hover:text-gray-700">
            Para Games
        </a>
    </nav>
</div>

<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-600">
        Elementary & Secondary sports
    </p>

    <a href="{{ route('sports.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Sport
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">
                    Sport Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">
                    Division
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">
                    Type of Game
                </th>
                <th class="px-6 py-3 text-right text-xs font-semibold uppercase">
                    Actions
                </th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse($sports as $sport)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">
                        {{ $sport->name }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs rounded-full
                            {{ $sport->division === 'Elementary' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $sport->division }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        @forelse($sport->types as $type)
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full mr-1">
                                {{ $type->type_name }}
                            </span>
                        @empty
                            <span class="text-gray-400 italic">No type</span>
                        @endforelse
                    </td>

                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('sports.edit', $sport->id) }}"
                           class="text-blue-600 mr-3">Edit</a>

                        <form action="{{ route('sports.destroy', $sport->id) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Delete this sport?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-500">
                        No sports found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
