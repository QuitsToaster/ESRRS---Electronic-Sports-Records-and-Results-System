<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\AccountRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
     public function index()
    {
        $users = User::with(['role', 'division'])
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|confirmed|min:6',
            'role_id'     => 'nullable|exists:roles,id',
            'new_role'    => 'nullable|string|max:50',
            'division_id' => 'required|exists:divisions,id',
            'access'      => 'nullable|array',
        ]);

        // 🔐 Resolve role
        if ($request->filled('new_role')) {
            $role = Role::firstOrCreate([
                'name' => trim($request->new_role)
            ]);
        } elseif ($request->filled('role_id')) {
            $role = Role::findOrFail($request->role_id);
        } else {
            return back()
                ->withErrors(['role_id' => 'Please select or create a role'])
                ->withInput();
        }

        // ✅ Create user (NOW WORKS)
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role_id'     => $role->id,
            'division_id' => $request->division_id,
            'access'      => $request->access ?? [],
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

        return back()->with('success', 'User deleted successfully.');
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
