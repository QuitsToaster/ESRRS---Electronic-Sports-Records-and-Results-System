<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport; // ✅ Import the model

class SportController extends Controller
{
    /**
     * Display a listing of the sports.
     */
    public function index()
    {
        $sports = Sport::all();
        return view('sports.index', compact('sports'));
    }

    /**
     * Show the form for creating a new sport.
     */
    public function create()
    {
        return view('sports.create');
    }

    /**
     * Store a newly created sport in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sports,name'
        ]);

        Sport::create([
            'name' => $request->name
        ]);

        return redirect()->route('sports.index')->with('success', 'Sport added successfully!');
    }

    /**
     * Show the form for editing the specified sport.
     */
    public function edit($id)
    {
        $sport = Sport::findOrFail($id);
        return view('sports.edit', compact('sport'));
    }

    /**
     * Update the specified sport in the database.
     */
    public function update(Request $request, $id)
    {
        $sport = Sport::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:sports,name,' . $sport->id
        ]);

        $sport->update([
            'name' => $request->name
        ]);

        return redirect()->route('sports.index')->with('success', 'Sport updated successfully!');
    }

    /**
     * Remove the specified sport from the database.
     */
    public function destroy($id)
    {
        $sport = Sport::findOrFail($id);
        $sport->delete();

        return redirect()->route('sports.index')->with('success', 'Sport deleted successfully!');
    }
}
