@extends('layouts.app')

@section('page-title', 'All Users')

@section('content')

{{-- ================= INSTRUCTIONS / NOTES ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>View all registered users in the system, including their role, level access, and sport/division permissions.</li>
        <li>Use the search box to find users by <strong>name</strong>, <strong>email</strong>, or <strong>role</strong>.</li>
        <li>Apply filters to narrow down the list by <strong>Role</strong>, <strong>Level Access</strong> (Elementary, Secondary, Paragames), or <strong>Division</strong> (e.g., SDO - Santiago City).</li>
        <li>The <strong>Total Users</strong> count shows all users, while the <strong>Filtered Users</strong> count updates based on the applied filters.</li>
        <li>Click <strong>+ Add User</strong> to manually create a new user account.</li>
        <li>Click <strong>Edit</strong> to update a user’s information or <strong>Delete</strong> to remove a user.</li>
        <li>Click <strong>Reset Password</strong> to set a user’s password to the default (<strong>ESRRS@2026</strong>).</li>
        <li>Super Admins can view and manage <strong>Account Requests</strong> at the bottom of the page.</li>
    </ul>
</div>

{{-- Account Requests Table --}}
@if(auth()->user()->role->name === 'Super Admin')
<div class="mt-10">
    <h2 class="text-xl font-bold mb-4">Account Requests</h2>

    @if($accountRequests->isEmpty())
        <div class="bg-white border border-gray-300 rounded-lg p-6 text-gray-500 shadow-sm">
            No account requests at this time.
        </div>
    @else
        <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Request Type</th>
                        <th class="px-4 py-2 text-left">Message</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($accountRequests as $accountRequest)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition">
                            <td class="px-4 py-2">{{ $accountRequest->id }}</td>
                            <td class="px-4 py-2">{{ $accountRequest->email }}</td>
                            <td class="px-4 py-2 capitalize">
                                {{ str_replace('_', ' ', $accountRequest->type) }}
                            </td>
                            <td class="px-4 py-2">{{ $accountRequest->message ?? '-' }}</td>

                            {{-- ✅ STATUS FIX --}}
                            <td class="px-4 py-2 font-semibold">
                                @if($accountRequest->status === 'done')
                                    <span class="text-green-600">Processed</span>
                                @else
                                    <span class="text-yellow-600">Pending</span>
                                @endif
                            </td>

                            <td class="px-4 py-2">
                                {{ $accountRequest->created_at->format('Y-m-d H:i') }}
                            </td>

                            <td class="px-4 py-2">
                                @if($accountRequest->status !== 'done')
                                    <form action="{{ route('account-requests.done', $accountRequest->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                                            Mark as Processed
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500 italic">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    @endif
</div>
@endif



<div class="flex justify-between items-center mt-12 mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of system users, their roles, and sidebar access
        </p>
        {{-- Total users --}}
        <p class="text-sm text-gray-600 mt-1">
            Total Users: <strong>{{ $totalUsers }}</strong>
            @if(request()->hasAny(['role','level','division']))
                | Filtered Users: <strong>{{ $users->count() }}</strong>
            @endif
        </p>
    </div>
    <a href="{{ route('users.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
       + Add User
    </a>
</div>

{{-- Filters Form --}}
<form method="GET" action="{{ route('users.index') }}" class="mb-4 flex flex-wrap gap-2 items-end">
    <div>
        <label class="text-gray-700 text-sm">Role</label>
        <select name="role" class="border border-gray-300 rounded px-3 py-2">
            <option value="">All</option>
            @foreach($roles as $roleOption)
                <option value="{{ $roleOption->id }}" {{ request('role') == $roleOption->id ? 'selected' : '' }}>
                    {{ $roleOption->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-gray-700 text-sm">Level Access</label>
        <select name="level" class="border border-gray-300 rounded px-3 py-2">
            <option value="">All</option>
            @foreach(['Elementary','Secondary','Paragames'] as $levelOption)
                <option value="{{ $levelOption }}" {{ request('level') == $levelOption ? 'selected' : '' }}>
                    {{ $levelOption }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-gray-700 text-sm">Division</label>
        <select name="division" class="border border-gray-300 rounded px-3 py-2">
            <option value="">All</option>
            @foreach($divisions as $divOption)
                <option value="{{ $divOption->name }}" {{ request('division') == $divOption->name ? 'selected' : '' }}>
                    {{ $divOption->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
            Filter
        </button>
        <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700 ml-2">Clear</a>
    </div>
</form>

{{-- Users Table --}}
@if($users->isEmpty())
    <div class="bg-white border border-gray-300 rounded-lg p-8 text-center text-gray-500 shadow-sm">
        No users found.
    </div>
@else
    <div x-data="passwordReset()" class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 border-b border-gray-300">
                <tr>
                    <th class="px-4 py-2 text-left text-gray-700">Name</th>
                    <th class="px-4 py-2 text-left text-gray-700">Email</th>
                    <th class="px-4 py-2 text-left text-gray-700">Role</th>
                    <th class="px-4 py-2 text-left text-gray-700">Module Access</th>
                    <th class="px-4 py-2 text-left text-gray-700">Level Access</th>
                    <th class="px-4 py-2 text-left text-gray-700">Category Access</th>
                    <th class="px-4 py-2 text-left text-gray-700">Sport Access</th>
                    <th class="px-4 py-2 text-left text-gray-700">Sport Type Access</th>
                    <th class="px-4 py-2 text-left text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition">
                    <td class="px-4 py-2 font-medium text-gray-800">{{ $user->name }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ $user->email }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ $user->role->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ $user->access ? implode(', ', $user->access) : '-' }}</td>
                    <td class="px-4 py-2 text-sm">{{ $user->level_access ? implode(', ', $user->level_access) : '—' }}</td>
                    <td class="px-4 py-2 text-sm">{{ $user->category_access ? implode(', ', $user->category_access) : '—' }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ $user->sports_access ? implode(', ', $user->sports_access) : '-' }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ $user->sport_type_access ? implode(', ', $user->sport_type_access) : '-' }}</td>
                    <td class="px-4 py-2 flex items-center gap-3">
    {{-- EDIT --}}
    <a href="{{ route('users.edit', $user) }}"
       class="text-yellow-600 hover:text-yellow-800"
       title="Edit">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg>
    </a>

    {{-- DELETE --}}
    <form action="{{ route('users.destroy', $user) }}" method="POST"
          onsubmit="return confirm('Are you sure?');">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="text-red-600 hover:text-red-800"
                title="Delete">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
  <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
</svg>
        </button>
    </form>

    {{-- RESET PASSWORD --}}
    <button @click="openModal('{{ $user->email }}', {{ $user->id }})"
            class="text-blue-600 hover:text-blue-800"
            title="Reset Password">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-unlock-fill" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M12 0a4 4 0 0 1 4 4v2.5h-1V4a3 3 0 1 0-6 0v2h.5A2.5 2.5 0 0 1 12 8.5v5A2.5 2.5 0 0 1 9.5 16h-7A2.5 2.5 0 0 1 0 13.5v-5A2.5 2.5 0 0 1 2.5 6H8V4a4 4 0 0 1 4-4"/>
</svg>
    </button>
</td>

                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Password Reset Modal --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                <h2 class="text-xl font-bold mb-4">Password Reset</h2>
                <p>Reset password for: <span class="font-semibold" x-text="email"></span></p>
                <p class="mt-2">New default password: <span class="font-semibold">ESRRS@2026</span></p>
                <div class="mt-6 flex justify-end gap-2">
                    <button @click="confirmReset" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Confirm
                    </button>
                    <button @click="showModal=false" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- JS for password reset --}}
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
                headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' },
                body: JSON.stringify({ password:'ESRRS@2026' })
            })
            .then(res=>res.json())
            .then(data=>{ alert(data.message); this.showModal=false; })
            .catch(err=>{ alert('Something went wrong'); console.error(err); });
        }
    }
}
</script>

@endsection
