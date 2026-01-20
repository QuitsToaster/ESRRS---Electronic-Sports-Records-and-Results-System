@extends('layouts.app')

@section('page-title', 'Audit Logs')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of logged actions performed by users in the system
        </p>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

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
                    <th class="px-4 py-2 text-left text-gray-700">Date/Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition">
                        <td class="px-4 py-2 font-medium text-gray-800">{{ $log->user->name ?? 'System' }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $log->action }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
