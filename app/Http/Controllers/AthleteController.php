<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\School;
use App\Models\Sport;

class AthleteController extends Controller
{
    public function index()
    {
        $athletes = Athlete::with(['school', 'sport'])->get();
        return view('athletes.index', compact('athletes'));
    }

    public function create()
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('athletes.create', compact('schools','sports'));
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

        Athlete::create($request->all());
        return redirect()->route('athletes.index')->with('success', 'Athlete added successfully!');
    }

    public function edit(Athlete $athlete)
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('athletes.edit', compact('athlete','schools','sports'));
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

        $athlete->update($request->all());
        return redirect()->route('athletes.index')->with('success', 'Athlete updated successfully!');
    }

    public function destroy(Athlete $athlete)
    {
        $athlete->delete();
        return redirect()->route('athletes.index')->with('success', 'Athlete deleted successfully!');
    }
}
