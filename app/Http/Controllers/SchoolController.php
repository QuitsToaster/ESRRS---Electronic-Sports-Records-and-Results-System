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
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('division', 'like', "%{$search}%")
                ->orWhere('province', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $schools = $query->orderBy('name')->get();

        return view('schools.index', compact('schools'));
    }


    public function create()
    {
        return view('schools.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|unique:schools',
            'division'=>'required|string',
            'province'=>'nullable|string',
            'city'=>'nullable|string',
            'logo'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['name','division','province','city']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos','public');
        }

        $school = School::create($data);

        // Audit log for creation
        AuditLog::create([
            'model_type' => 'School',
            'model_id' => $school->id,
            'user_id' => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($school->toArray()),
            'action' => 'Created Team: '.$school->name,
        ]);

        return redirect()->route('schools.index')->with('success','Team added successfully!');
    }

    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'name'=>'required|string|unique:schools,name,'.$school->id,
            'division'=>'required|string',
            'province'=>'nullable|string',
            'city'=>'nullable|string',
            'logo'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $oldValues = $school->toArray(); // store old data

        $data = $request->only(['name','division','province','city']);

        if ($request->hasFile('logo')) {
            if ($school->logo) Storage::disk('public')->delete($school->logo);

            $data['logo'] = $request->file('logo')->store('logos','public');
        }

        $school->update($data);

        // Audit log for update
        AuditLog::create([
            'model_type' => 'School',
            'model_id' => $school->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($school->toArray()),
            'action' => 'Updated Team: '.$school->name,
        ]);

        return redirect()->route('schools.index')->with('success','Team updated successfully!');
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
