<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\School;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AthleteController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $athletes = Athlete::with(['school', 'sport'])
        ->when($search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('school', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('sport', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('division', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        });

    // Apply ordering and get results
    $athletes = $athletes->orderBy('name')->get();

    return view('athletes.index', compact('athletes'));
}


    public function create()
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('athletes.create', compact('schools', 'sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'school_id' => 'required|exists:schools,id',
            'sport_id' => 'required|exists:sports,id',
            'division' => 'required|in:Elementary,Secondary',
            'category' => 'required|in:Boys,Girls',
            'status' => 'required|in:Active,Disqualified,Injured'
        ]);

        $athlete = Athlete::create($request->all());

        // Audit log for creation
        AuditLog::create([
            'model_type' => 'Athlete',
            'model_id' => $athlete->id,
            'user_id' => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($athlete->toArray()),
            'action' => 'Created Athlete: '.$athlete->name,
        ]);

        return redirect()->route('athletes.index')->with('success', 'Athlete added successfully!');
    }

    public function edit(Athlete $athlete)
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('athletes.edit', compact('athlete', 'schools', 'sports'));
    }

    public function update(Request $request, Athlete $athlete)
    {
        $request->validate([
            'name' => 'required|string',
            'school_id' => 'required|exists:schools,id',
            'sport_id' => 'required|exists:sports,id',
            'division' => 'required|in:Elementary,Secondary',
            'category' => 'required|in:Boys,Girls',
            'status' => 'required|in:Active,Disqualified,Injured'
        ]);

        $oldValues = $athlete->toArray(); // store old data

        $athlete->update($request->all());

        // Audit log for update
        AuditLog::create([
            'model_type' => 'Athlete',
            'model_id' => $athlete->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($athlete->toArray()),
            'action' => 'Updated Athlete: '.$athlete->name,
        ]);

        return redirect()->route('athletes.index')->with('success', 'Athlete updated successfully!');
    }

    public function destroy(Athlete $athlete)
    {
        $oldValues = $athlete->toArray();

        $athlete->delete();

        // Audit log for deletion
        AuditLog::create([
            'model_type' => 'Athlete',
            'model_id' => $athlete->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action' => 'Deleted Athlete: '.$athlete->name,
        ]);

        return redirect()->route('athletes.index')->with('success', 'Athlete deleted successfully!');
    }
}
