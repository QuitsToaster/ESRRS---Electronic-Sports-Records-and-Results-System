<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    public function index(Request $request)
{
    $query = School::query();

    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('division', 'like', "%{$search}%")
              ->orWhere('province', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }

    // ✅ PAGINATION (adjust 10 if needed)
    $schools = $query
        ->orderBy('name')
        ->paginate(10)
        ->withQueryString(); // keeps search when changing page

    return view('schools.index', compact('schools'));
}



    public function create()
    {
        return view('schools.create');
    }

    public function store(Request $request)
{
    $request->validate([
    'name' => 'required|string|unique:schools',
    'school_type' => 'nullable|in:Public,Private',
    'school_address' => 'nullable|string',
    'division' => 'required|in:Elementary,Secondary',
    'school_division' => 'nullable|string',
    'school_code' => 'nullable|string|unique:schools,school_code',
    'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
]);


    $data = $request->only([
        'name','school_type','school_address','division','school_division','school_code'
    ]);

    if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('logos','public');
    }

    $school = School::create($data);

    // Audit log
    AuditLog::create([
        'model_type'=>'School',
        'model_id'=>$school->id,
        'user_id'=>Auth::id(),
        'old_values'=>null,
        'new_values'=>json_encode($school->toArray()),
        'action'=>'Created School: '.$school->name,
    ]);

    return redirect()->route('schools.index')->with('success','School added successfully!');
}

    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
{
    $request->validate([
        // ❌ REMOVE UNIQUE VALIDATION
        'name' => 'required|string|max:255',

        'school_type' => 'nullable|in:Public,Private',
        'school_address' => 'nullable|string',
        'division' => 'required|in:Elementary,Secondary',
        'school_division' => 'nullable|string',

        // ❌ REMOVE UNIQUE HERE ALSO
        'school_code' => 'nullable|string|max:255',

        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $oldValues = $school->toArray();

    /* ================= DATA ================= */
    $data = [
        'name'             => strtoupper($request->name), // ✅ FORCE UPPERCASE (optional but consistent)
        'school_type'      => $request->school_type,
        'school_address'   => $request->school_address,
        'division'         => $request->division,
        'school_division'  => $request->school_division,
        'school_code'      => $request->school_code,
        'province'         => $request->province,
        'city'             => $request->city,
    ];

    /* ================= LOGO ================= */
    if ($request->hasFile('logo')) {
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        $data['logo'] = $request->file('logo')->store('logos','public');
    }

    /* ================= UPDATE ================= */
    $school->update($data);

    /* ================= AUDIT ================= */
    AuditLog::create([
        'model_type' => 'School',
        'model_id'   => $school->id,
        'user_id'    => Auth::id(),
        'old_values' => json_encode($oldValues),
        'new_values' => json_encode($school->fresh()->toArray()),
        'action'     => 'Updated School: '.$school->name,
    ]);

    return redirect()->route('schools.index')
        ->with('success','School updated successfully!');
}

    public function destroy(School $school)
    {
        $oldValues = $school->toArray();

        if ($school->logo) Storage::disk('public')->delete($school->logo);

        $school->delete();

        // Audit log for deletion
        AuditLog::create([
            'model_type' => 'School',
            'model_id' => $school->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action' => 'Deleted Team: '.$school->name,
        ]);

        return redirect()->route('schools.index')->with('success','Team deleted successfully!');
    }
}
