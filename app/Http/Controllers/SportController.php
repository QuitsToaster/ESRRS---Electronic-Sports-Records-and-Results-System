<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SportController extends Controller
{
    /**
     * Display a listing of the sports.
     */
    public function index()
    {
        $sports = Sport::with('types')->orderBy('name')->get();
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
            'name' => 'required|string|unique:sports,name',
            'type_of_game.*' => 'nullable|string', // validate array items
        ]);

        $sport = Sport::create([
            'name' => $request->name,
        ]);

        if ($request->type_of_game) {
            foreach ($request->type_of_game as $type) {
                if ($type) {
                    $sport->types()->create(['type_name' => $type]);
                }
            }
        }

        // Audit log
        AuditLog::create([
            'model_type' => 'Sport',
            'model_id' => $sport->id,
            'user_id' => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($sport->toArray()),
            'action' => 'Created Sport: ' . $sport->name,
        ]);

        return redirect()->route('sports.index')->with('success', 'Sport added successfully!');
    }

    /**
     * Show the form for editing the specified sport.
     */
    public function edit($id)
    {
        $sport = Sport::with('types')->findOrFail($id);
        return view('sports.edit', compact('sport'));
    }

    /**
     * Update the specified sport in the database.
     */
    public function update(Request $request, $id)
    {
        $sport = Sport::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:sports,name,' . $sport->id,
            'type_of_game.*' => 'nullable|string',
        ]);

        $oldValues = $sport->toArray();

        $sport->update([
            'name' => $request->name,
        ]);

        // Delete old types and add new
        $sport->types()->delete();
        if ($request->type_of_game) {
            foreach ($request->type_of_game as $type) {
                if ($type) {
                    $sport->types()->create(['type_name' => $type]);
                }
            }
        }

        // Audit log
        AuditLog::create([
            'model_type' => 'Sport',
            'model_id' => $sport->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($sport->toArray()),
            'action' => 'Updated Sport: ' . $sport->name,
        ]);

        return redirect()->route('sports.index')->with('success', 'Sport updated successfully!');
    }

    /**
     * Remove the specified sport from the database.
     */
    public function destroy($id)
    {
        $sport = Sport::findOrFail($id);
        $oldValues = $sport->toArray();

        $sport->delete();

        // Audit log for deletion
        AuditLog::create([
            'model_type' => 'Sport',
            'model_id' => $sport->id,
            'user_id' => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action' => 'Deleted Sport: '.$sport->name,
        ]);

        return redirect()->route('sports.index')->with('success', 'Sport deleted successfully!');
    }
}
