<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coach;
use App\Models\School;
use App\Models\Sport;

class CoachController extends Controller
{
    public function index()
    {
        $coaches = Coach::with(['school','sport'])->get();
        return view('coaches.index', compact('coaches'));
    }

    public function create()
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('coaches.create', compact('schools','sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'school_id'=>'required|exists:schools,id',
            'sport_id'=>'required|exists:sports,id'
        ]);

        Coach::create($request->all());
        return redirect()->route('coaches.index')->with('success','Coach added successfully!');
    }

    public function edit(Coach $coach)
    {
        $schools = School::all();
        $sports = Sport::all();
        return view('coaches.edit', compact('coach','schools','sports'));
    }

    public function update(Request $request, Coach $coach)
    {
        $request->validate([
            'name'=>'required|string',
            'school_id'=>'required|exists:schools,id',
            'sport_id'=>'required|exists:sports,id'
        ]);

        $coach->update($request->all());
        return redirect()->route('coaches.index')->with('success','Coach updated successfully!');
    }

    public function destroy(Coach $coach)
    {
        $coach->delete();
        return redirect()->route('coaches.index')->with('success','Coach deleted successfully!');
    }
}
