@extends('layouts.app')

@section('page-title', 'Audit Logs')

@section('content')

{{-- Header + Export Button --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of logged actions performed by users in the system
        </p>
    </div>

    {{-- Export PDF Button --}}
    <a href="{{ route('audit-logs.export') }}"
       class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded shadow transition">
        Export Logs (PDF)
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Search & Date Filter --}}
<form method="GET" action="{{ route('audit-logs.index') }}" class="mb-4 flex flex-wrap items-center gap-2">
    {{-- Search --}}
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search logs by user, action, model..."
           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">

    {{-- Start Date --}}
    <input type="date" name="start_date" value="{{ request('start_date') }}"
           class="border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">

    {{-- End Date --}}
    <input type="date" name="end_date" value="{{ request('end_date') }}"
           class="border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">

    {{-- Submit --}}
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>

    {{-- Clear --}}
    @if(request()->hasAny(['search','start_date','end_date']))
        <a href="{{ route('audit-logs.index') }}" class="text-gray-500 hover:text-gray-700 ml-2">
            Clear
        </a>
    @endif
</form>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
    @if($logs->isEmpty())
        <div class="text-gray-500 text-center py-6">
            No audit logs found.
        </div>
    @else

        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 border-b border-gray-300">
                <tr>
                    <th class="px-4 py-2 text-left text-gray-700">User</th>
                    <th class="px-4 py-2 text-left text-gray-700">Action</th>
                    <th class="px-4 py-2 text-left text-gray-700">Date and Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition">
                        <td class="px-4 py-2 font-medium text-gray-800">
                            {{ $log->user->name ?? 'System' }}
                        </td>

                        <td class="px-4 py-2 text-gray-700">
                            {{ $log->action }}
                        </td>

                        <td class="px-4 py-2 text-gray-700">
                            {{ $log->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif
</div>

@endsection
