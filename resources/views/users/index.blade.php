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
    {{-- Wrap the table and modal in Alpine component --}}
    <div x-data="passwordReset()" class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">

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

                        {{-- Edit --}}
                        <a href="{{ route('users.edit', $user) }}"
                           class="text-yellow-600 hover:text-yellow-800 font-medium">
                            Edit
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                Delete
                            </button>
                        </form>

                        {{-- Reset Password --}}
                        <button
                            @click="openModal('{{ $user->email }}', {{ $user->id }})"
                            class="text-blue-600 hover:text-blue-800 font-medium">
                            Reset Password
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Password Reset Modal --}}
        <div x-show="showModal" x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                <h2 class="text-xl font-bold mb-4">Password Reset</h2>
                <p>Reset password for: <span class="font-semibold" x-text="email"></span></p>
                <p class="mt-2">New default password: <span class="font-semibold">CEMS12345</span></p>

                <div class="mt-6 flex justify-end gap-2">
                    <button @click="confirmReset" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Confirm
                    </button>
                    <button @click="showModal=false" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

    </div>
@endif

{{-- Account Requests Section for Super Admin --}}
@if(auth()->user()->role->name === 'Super Admin')
    <div class="mt-10">
        <h2 class="text-2xl font-bold mb-4">Account Requests</h2>

        @if($accountRequests->isEmpty())
            <div class="bg-white border border-gray-300 rounded-lg p-6 text-center text-gray-500 shadow-sm">
                No account requests at the moment.
            </div>
        @else
            <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead class="bg-gray-100 border-b border-gray-300">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700">Email</th>
                            <th class="px-4 py-2 text-left text-gray-700">Type</th>
                            <th class="px-4 py-2 text-left text-gray-700">Status</th>
                            <th class="px-4 py-2 text-left text-gray-700">Requested At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accountRequests as $request)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition">
                            <td class="px-4 py-2 text-gray-700">{{ $request->email }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $request->type === 'forgot_password' ? 'Password Reset' : 'New Account' }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ ucfirst($request->status) }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif

<script>
function passwordReset() {
    return {
        showModal: false,
        email: '',
        userId: null,

        openModal(email, id) {
            this.email = email;
            this.userId = id;
            this.showModal = true;
        },

        confirmReset() {
            fetch(`/users/${this.userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password: 'CEMS12345' })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                this.showModal = false;
            })
            .catch(err => {
                alert('Something went wrong.');
                console.error(err);
            });
        }
    }
}
</script>

@endsection
