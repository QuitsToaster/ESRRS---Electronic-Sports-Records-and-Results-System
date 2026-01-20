<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coach;
use App\Models\School;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CoachController extends Controller
{
    public function index()
    {
        $coaches = Coach::with(['school', 'sport'])->get();
        return view('coaches.index', compact('coaches'));
    }

    public function create()
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('coaches.create', compact('schools', 'sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'school_id' => 'required|exists:schools,id',
            'sport_id' => 'required|exists:sports,id'
        ]);

        $coach = Coach::create($request->all());

        // Audit log for creation
        AuditLog::create([
            'model_type' => 'Coach',
            'model_id' => $coach->id,
            'user_id' => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($coach->toArray()),
            'action' => 'Created Coach: '.$coach->name,
        ]);

        return redirect()->route('coaches.index')->with('success', 'Coach added successfully!');
    }

    public function edit(Coach $coach)
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('coaches.edit', compact('coach', 'schools', 'sports'));
    }

    public function update(Request $request, Coach $coach)
    {
        $request->validate([
            'name' => 'required|string',
            'school_id' => 'required|exists:schools,id',
            'sport_id' => 'required|exists:sports,id'
        ]);

        $oldValues = $coach->toArray(); // store old data

        $coach->update($request->all());

        // Audit log for update
        AuditLog::create([
            'model_type' => 'Coach',
            'model_id' => $coach->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($coach->toArray()),
            'action' => 'Updated Coach: '.$coach->name,
        ]);

        return redirect()->route('coaches.index')->with('success', 'Coach updated successfully!');
    }

    public function destroy(Coach $coach)
    {
        $oldValues = $coach->toArray();

        $coach->delete();

        // Audit log for deletion
        AuditLog::create([
            'model_type' => 'Coach',
            'model_id' => $coach->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action' => 'Deleted Coach: '.$coach->name,
        ]);

        return redirect()->route('coaches.index')->with('success', 'Coach deleted successfully!');
    }
}
