<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SportController extends Controller
{
    /**
     * Display Regular Sports (Elementary + Secondary)
     */
    public function index(Request $request)
    {
        $sports = Sport::with('types')
            ->whereIn('division', ['Elementary', 'Secondary'])
            ->orderBy('name')
            ->get();

        return view('sports.index', compact('sports'));
    }

    /**
     * Show the form for creating a new regular sport
     */
    public function create()
    {
        return view('sports.create');
    }

    /**
     * Store a newly created regular sport
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sports,name',
            'division' => 'required|in:Elementary,Secondary',
            'type_of_game.*' => 'nullable|string',
        ]);

        $sport = Sport::create([
            'name'     => $request->name,
            'division' => $request->division,
        ]);

        if ($request->type_of_game) {
            foreach ($request->type_of_game as $type) {
                if ($type) {
                    $sport->types()->create(['type_name' => $type]);
                }
            }
        }

        AuditLog::create([
            'model_type' => 'Sport',
            'model_id'   => $sport->id,
            'user_id'    => Auth::id(),
            'old_values' => null,
            'new_values' => json_encode($sport->toArray()),
            'action'     => 'Created Sport: ' . $sport->name,
        ]);

        return redirect()->route('sports.index')
            ->with('success', 'Sport added successfully!');
    }

    /**
     * Edit regular sport
     */
    public function edit($id)
    {
        $sport = Sport::with('types')->findOrFail($id);
        return view('sports.edit', compact('sport'));
    }

    /**
     * Update regular sport
     */
    public function update(Request $request, $id)
    {
        $sport = Sport::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:sports,name,' . $sport->id,
            'division' => 'required|in:Elementary,Secondary',
            'type_of_game.*' => 'nullable|string',
        ]);

        $oldValues = $sport->toArray();

        $sport->update([
            'name'     => $request->name,
            'division' => $request->division,
        ]);

        $sport->types()->delete();
        if ($request->type_of_game) {
            foreach ($request->type_of_game as $type) {
                if ($type) {
                    $sport->types()->create(['type_name' => $type]);
                }
            }
        }

        AuditLog::create([
            'model_type' => 'Sport',
            'model_id'   => $sport->id,
            'user_id'    => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($sport->toArray()),
            'action'     => 'Updated Sport: ' . $sport->name,
        ]);

        return redirect()->route('sports.index')
            ->with('success', 'Sport updated successfully!');
    }

    /**
     * Delete regular sport
     */
    public function destroy($id)
    {
        $sport = Sport::findOrFail($id);

        if ($sport->division === 'Paragames') {
            abort(403, 'Use Para Games module to delete this sport.');
        }

        $oldValues = $sport->toArray();
        $sport->delete();

        AuditLog::create([
            'model_type' => 'Sport',
            'model_id'   => $sport->id,
            'user_id'    => Auth::id(),
            'old_values' => json_encode($oldValues),
            'new_values' => null,
            'action'     => 'Deleted Sport: ' . $sport->name,
        ]);

        return redirect()->route('sports.index')
            ->with('success', 'Sport deleted successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | Para Games (REUSED AS-IS)
    |--------------------------------------------------------------------------
    */

    public function paraIndex()
    {
        $sports = Sport::with('types')
            ->where('division', 'Paragames')
            ->orderBy('name')
            ->get();

        return view('sports.para.index', compact('sports'));
    }

    public function paraCreate()
    {
        return view('sports.para.create');
    }

    public function paraStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sports,name',
            'type_of_game.*' => 'nullable|string',
        ]);

        $sport = Sport::create([
            'name'     => $request->name,
            'division' => 'Paragames',
        ]);

        if ($request->type_of_game) {
            foreach ($request->type_of_game as $type) {
                if ($type) {
                    $sport->types()->create(['type_name' => $type]);
                }
            }
        }

        return redirect()->route('para-sports.index')
            ->with('success', 'Para Game added successfully.');
    }

    public function paraEdit(Sport $sport)
    {
        return view('sports.para.edit', compact('sport'));
    }

    public function paraUpdate(Request $request, Sport $sport)
    {
        $request->validate([
            'name' => 'required|string|unique:sports,name,' . $sport->id,
            'type_of_game.*' => 'nullable|string',
        ]);

        $sport->update(['name' => $request->name]);

        $sport->types()->delete();
        if ($request->type_of_game) {
            foreach ($request->type_of_game as $type) {
                if ($type) {
                    $sport->types()->create(['type_name' => $type]);
                }
            }
        }

        return redirect()->route('para-sports.index')
            ->with('success', 'Para Game updated successfully.');
    }

    public function paraDestroy(Sport $sport)
    {
        if ($sport->division !== 'Paragames') {
            abort(403);
        }

        $sport->types()->delete();
        $sport->delete();

        return redirect()->route('para-sports.index')
            ->with('success', 'Para Game deleted successfully.');
    }
}
