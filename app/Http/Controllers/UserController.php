<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\AccountRequest;
use App\Models\Sport;
use App\Models\SportType;
use App\Models\AuditLog;
use App\Models\Division;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');
    $roleFilter = $request->input('role');
    $levelFilter = $request->input('level');
    $divisionFilter = $request->input('division');

    $usersQuery = User::with('role');

    // Search
if ($search) {

    // Normalize search
    $search = strtolower(trim($search));

    // Split into words
    $keywords = preg_split('/\s+/', $search);

    $usersQuery->where(function ($q) use ($keywords) {

        foreach ($keywords as $word) {

            $q->where(function ($sub) use ($word) {

                $sub->whereRaw('LOWER(name) LIKE ?', ["%{$word}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$word}%"])
                    ->orWhereRaw('LOWER(contact_number) LIKE ?', ["%{$word}%"])
                    ->orWhereHas('role', function ($r) use ($word) {
                        $r->whereRaw('LOWER(name) LIKE ?', ["%{$word}%"]);
                    });

            });

        }

    });
}

    // Role filter
    if ($roleFilter) {
        $usersQuery->where('role_id', $roleFilter);
    }

    // Level filter
    if ($levelFilter) {
        $usersQuery->whereJsonContains('level_access', $levelFilter);
    }

    // Division filter
    if ($divisionFilter) {
        $usersQuery->where('name', 'like', "%{$divisionFilter}%");
    }

    $users = $usersQuery->orderBy('name')->get();

    $totalUsers = User::count();
    $roles = Role::all();
    $divisions = \App\Models\Division::orderBy('name')->get();

    // Load sports and sport types
    $sports = Sport::orderBy('division')
        ->orderBy('category')
        ->orderBy('name')
        ->get();

    $sportTypes = SportType::orderBy('type_name')->get();

    // Account requests for super admin
    $accountRequests = (auth()->user()->role->name === 'Super Admin') 
        ? \App\Models\AccountRequest::orderBy('created_at', 'desc')->get() 
        : collect();

    return view('users.index', compact(
        'users', 
        'totalUsers', 
        'roles', 
        'divisions', 
        'accountRequests',
        'sports',
        'sportTypes'   // <-- pass these!
    ));
}



    // Show form to create user
  public function create()
{
    $roles = Role::all();
    $divisions = Division::orderBy('name')->get(); // ✅ fetch all divisions

    $sports = Sport::orderBy('division')
        ->orderBy('category')
        ->orderBy('name')
        ->get();

    $sportTypes = SportType::orderBy('type_name')->get();

    return view('users.create', compact('roles', 'divisions', 'sports', 'sportTypes'));
}



    // Store new user
public function store(Request $request)
{
    // Determine the role first
    if ($request->filled('new_role')) {
        $role = Role::firstOrCreate(['name' => trim($request->new_role)]);
    } elseif ($request->filled('role_id')) {
        $role = Role::findOrFail($request->role_id);
    } else {
        return back()->withErrors(['role_id' => 'Role is required'])->withInput();
    }

    // Base validation rules
    $rules = [
        'name'             => 'required|string|max:255',
        'email'            => 'required|email|unique:users',
        'password'         => 'required|confirmed|min:6',
        'role_id'          => 'nullable|exists:roles,id',
        'contact_number'   => 'nullable|digits:10',
        'new_role'         => 'nullable|string|max:50',
        'access'           => 'nullable|array',
        'sports_access'    => 'nullable|array',
        'sports_access.*'  => 'exists:sports,id',
        'sport_type_access'=> 'nullable|array',
        'sport_type_access.*'=> 'exists:sport_types,id',
        'level_access'     => 'nullable|array',
        'category_access'  => 'nullable|array',
    ];

    // Conditional rules for Billeting Quarters
    if ($role->name === 'Billeting Quarters') {
        $rules['divisions']   = 'required|array|min:1';
        $rules['divisions.*'] = 'required|exists:divisions,id';
    }

    // Validate request
    $request->validate($rules);

    // Create user
    $user = User::create([
        'name'             => $request->name,
        'email'            => $request->email,
        'password'         => Hash::make($request->password),
        'role_id'          => $role->id,
        'access'           => $request->access ?? [],
        'sports_access'    => $request->sports_access ?? [],
        'sport_type_access'=> $request->sport_type_access ?? [],
        'level_access'     => $request->level_access ?? [],
        'category_access'  => $request->category_access ?? [],
        'divisions'        => $request->divisions ?? [], // safe to keep as empty array
        'contact_number'   => $request->contact_number ? '+63' . $request->contact_number : null,
    ]);

    // Audit log
    AuditLog::create([
        'model_type' => 'User',
        'model_id'   => $user->id,
        'user_id'    => auth()->id(),
        'action'     => 'Created User',
        'new_values' => json_encode($user->toArray()),
    ]);

    return redirect()->route('users.index')->with('success', 'User created successfully.');
}




    // Show form to edit user
public function edit(User $user)
{
    $roles = Role::all();
    $sports = Sport::all();
    $sportTypes = SportType::all();

    // ✅ Add this line to get all divisions
    $divisions = Division::all(); // or whatever your model/table name is

    return view('users.edit', compact(
        'user',
        'roles',
        'sports',
        'sportTypes',
        'divisions' // ✅ pass divisions to Blade
    ));
}

// Update existing user
public function update(Request $request, User $user)
{
    try {
        // Determine the role first
        if ($request->filled('new_role')) {
            $role = Role::firstOrCreate(['name' => trim($request->new_role)]);
        } elseif ($request->filled('role_id')) {
            $role = Role::findOrFail($request->role_id);
        } else {
            return back()->withErrors(['role_id' => 'Role is required'])->withInput();
        }

        // Base validation rules
        $rules = [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'password'         => 'nullable|string|min:6|confirmed',
            'role_id'          => 'nullable|exists:roles,id',
            'new_role'         => 'nullable|string|max:50',
            'access'           => 'nullable|array',
            'level_access'     => 'nullable|array',
            'category_access'  => 'nullable|array',
            'sports_access'    => 'nullable|array',
            'sport_type_access'=> 'nullable|array',
        ];

        // Only require divisions if role is Billeting Quarters
        if ($role->name === 'Billeting Quarters') {
            $rules['divisions']   = 'required|array|min:1';
            $rules['divisions.*'] = 'required|exists:divisions,id';
        } else {
            // For other roles, ignore empty or missing divisions
            $rules['divisions']   = 'nullable|array';
            $rules['divisions.*'] = 'nullable|exists:divisions,id';
        }

        // Validate request
        $request->validate($rules);

        $oldValues = $user->toArray();

        // Update user
        $user->update([
            'name'             => $request->name,
            'email'            => $request->email,
            'role_id'          => $role->id,
            'access'           => $request->access ?? [],
            'level_access'     => $request->level_access ?? [],
            'category_access'  => $request->category_access ?? [],
            'sports_access'    => $request->sports_access ?? [],
            'sport_type_access'=> $request->sport_type_access ?? [],
            'divisions'        => $role->name === 'Billeting Quarters' ? $request->divisions ?? [] : [],  
            'contact_number'   => $request->contact_number ? '+63' . $request->contact_number : null,
        ]);

        // Only update password if filled
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

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

    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        return back()->with('error', "An error occurred: " . $e->getMessage())->withInput();
    }
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
        'message' => "Password for {$user->email} has been reset to ESRRS@2026"
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

public function exportPdf(Request $request)
{
    $roleId = $request->input('role');

    $usersQuery = User::with('role');

    if ($roleId && $roleId !== 'all') {
        $usersQuery->where('role_id', $roleId);
    }

    $users = $usersQuery->get();

    // GROUP BY ROLE
    $groupedUsers = $users->groupBy(fn($u) => $u->role->name ?? 'No Role');

    // ✅ LOAD LOOKUP DATA (IMPORTANT)
    $sports = Sport::all();
    $sportTypes = SportType::all();

    $pdf = Pdf::loadView('users.export_pdf', [
        'groupedUsers' => $groupedUsers,
        'sports' => $sports,           // ✅ FIX
        'sportTypes' => $sportTypes    // ✅ FIX
    ])->setPaper('letter', 'landscape');

    return $pdf->download('users_report.pdf');
}
}
