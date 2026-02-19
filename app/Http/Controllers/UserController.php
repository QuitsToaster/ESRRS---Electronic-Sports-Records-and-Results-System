<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\AccountRequest;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');

    $users = User::with('role')
        ->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('role', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        })
        ->orderBy('name', 'asc')
        ->get();

    // Fetch account requests only for super admin
    $accountRequests = collect(); // default empty collection
    if (auth()->user()->role->name === 'Super Admin') {
        $accountRequests = AccountRequest::orderBy('created_at', 'desc')->get();
    }

    return view('users.index', compact('users', 'accountRequests'));
}


    // Show form to create user
    public function create()
{
    $roles  = Role::all();
    $sports = Sport::orderBy('name')->get(); // ✅ FETCH SPORTS

    return view('users.create', compact('roles', 'sports'));
}


    // Store new user
   public function store(Request $request)
{
    $request->validate([
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users',
        'password'      => 'required|confirmed|min:6',
        'role_id'       => 'nullable|exists:roles,id',
        'new_role'      => 'nullable|string|max:50',
        'access'        => 'nullable|array',
        'sports_access' => 'nullable|array',
        'sports_access.*' => 'exists:sports,id',
    ]);

    // Determine role
    if ($request->filled('new_role')) {
        $role = Role::firstOrCreate(['name' => trim($request->new_role)]);
    } elseif ($request->filled('role_id')) {
        $role = Role::find($request->role_id);
    } else {
        return back()
            ->withErrors(['role_id' => 'Please select a role or add a new one.'])
            ->withInput();
    }

    $user = User::create([
        'name'          => $request->name,
        'email'         => $request->email,
        'password'      => Hash::make($request->password),
        'role_id'       => $role->id,
        'access'        => $request->access ?? [],
        'sports_access' => $request->sports_access ?? [], // ✅ SAVE SPORTS
    ]);

    AuditLog::create([
        'model_type' => 'User',
        'model_id'   => $user->id,
        'user_id'    => auth()->id(),
        'action'     => 'Created User',
        'old_values' => null,
        'new_values' => json_encode($user->toArray()),
    ]);

    return redirect()
        ->route('users.index')
        ->with('success', 'User created successfully.');
}



    // Show form to edit user
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    // Update existing user
    public function update(Request $request, User $user)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6|confirmed',
        'role_id'  => 'nullable|exists:roles,id',
        'new_role' => 'nullable|string|max:50',
    ]);

    $oldValues = $user->toArray();

    // Determine role
    if (!empty($request->new_role)) {
        $role = Role::firstOrCreate(['name' => $request->new_role]);
    } elseif (!empty($request->role_id)) {
        $role = Role::find($request->role_id);
        if (!$role) {
            return back()->withErrors(['role_id' => 'Selected role not found'])->withInput();
        }
    } else {
        return back()->withErrors(['role_id' => 'Please select a role or enter a new role'])->withInput();
    }

    // Update user fields
    $user->name = $request->name;
    $user->email = $request->email;
    $user->role_id = $role->id;
    $user->access = $request->access ?? [];

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();


    // Audit log
    AuditLog::create([
        'model_type' => 'User',
        'model_id'   => $user->id,
        'user_id'    => auth()->id(),
        'action'     => 'Updated User',
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($user->toArray()),
    ]);

    return redirect()->route('users.index')->with('success', 'User updated successfully.');
}


    // Delete user
    public function destroy(User $user)
    {
        $oldValues = $user->toArray();
        $user->delete();

        AuditLog::create([
            'model_type' => 'User',
            'model_id'   => $user->id,
            'user_id'    => auth()->id(),
            'action'     => 'Deleted User',
            'old_values' => json_encode($oldValues),
            'new_values' => null,
        ]);

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    // Update logged-in user's profile
public function updateProfile(Request $request)
{
    $user = auth()->user();

    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6|confirmed',
    ]);

    $oldValues = $user->toArray();

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    // Audit log
    AuditLog::create([
        'model_type' => 'User',
        'model_id'   => $user->id,
        'user_id'    => auth()->id(),
        'action'     => 'Updated Own Profile',
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($user->toArray()),
    ]);

    return back()->with('success', 'Profile updated successfully!');
}

public function resetPassword(Request $request, User $user)
{
    $user->password = bcrypt($request->password); // default password
    $user->save();

    return response()->json([
        'message' => "Password for {$user->email} has been reset to CEMS12345"
    ]);
}


public function markAccountRequestDone(AccountRequest $accountRequest)
{
    // Only super admin protection (extra safety)
    if (auth()->user()->role->name !== 'Super Admin') {
        abort(403);
    }

    $oldValues = $accountRequest->toArray();

    $accountRequest->status = 'done';
    $accountRequest->save();

    AuditLog::create([
        'model_type' => 'AccountRequest',
        'model_id'   => $accountRequest->id,
        'user_id'    => auth()->id(),
        'action'     => 'Marked Account Request as Done',
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($accountRequest->toArray()),
    ]);

    return redirect()
        ->route('users.index')
        ->with('success', 'Account request marked as done.');
}


}
