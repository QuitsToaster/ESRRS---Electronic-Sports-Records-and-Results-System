<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\AuditLog;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use Illuminate\Support\Facades\Auth;

class SportController extends Controller
{
    public function index(Request $request)
    {
        $sports = Sport::with('types')
            ->whereIn('division', ['Elementary', 'Secondary'])
            ->orderBy('name')
            ->get();

        return view('sports.index', compact('sports'));
    }

    public function create()
    {
        return view('sports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sports,name',
            'type_of_game.*' => 'nullable|string',
        ]);

        $sport = Sport::create([
            'name'     => $request->name,
            'division' => 'Elementary',
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

    public function edit($id)
    {
        $sport = Sport::with('types')->findOrFail($id);

        if ($sport->division === 'Paragames') {
            abort(403);
        }

        return view('sports.edit', compact('sport'));
    }

    public function update(Request $request, $id)
    {
        $sport = Sport::findOrFail($id);

        if ($sport->division === 'Paragames') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|unique:sports,name,' . $sport->id,
            'type_of_game.*' => 'nullable|string',
        ]);

        $oldValues = $sport->toArray();

        $sport->update([
            'name' => $request->name,
        ]);

        // Delete old types & recreate
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
     * Delete regular sport safely (types + award results, NOT athletes)
     */
    public function destroy($id)
    {
        $sport = Sport::findOrFail($id);

        if ($sport->division === 'Paragames') {
            abort(403, 'Use Para Games module.');
        }

        // ✅ Delete related award results first
        AllAwardResult::where('sport_id', $sport->id)->delete();
        IndividualAwardResult::where('sport_id', $sport->id)->delete();

        // Delete sport types
        $sport->types()->delete();

        // Delete the sport itself
        $sport->delete();

        return redirect()->route('sports.index')
            ->with('success', 'Sport deleted successfully!');
    }

    // ---------------- PARA GAMES ----------------

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
        // Delete related award results first
        AllAwardResult::where('sport_id', $sport->id)->delete();
        IndividualAwardResult::where('sport_id', $sport->id)->delete();

        $sport->types()->delete();
        $sport->delete();

        return redirect()->route('para-sports.index')
            ->with('success', 'Para Game deleted successfully.');
    }

    /**
     * Bulk Delete Sports
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'sports' => 'required|array',
            'sports.*' => 'exists:sports,id'
        ]);

        $sports = Sport::whereIn('id', $request->sports)
            ->whereIn('division', ['Elementary', 'Secondary'])
            ->get();

        foreach ($sports as $sport) {
            AllAwardResult::where('sport_id', $sport->id)->delete();
            IndividualAwardResult::where('sport_id', $sport->id)->delete();
            $sport->types()->delete();
            $sport->delete();
        }

        return redirect()->route('sports.index')
            ->with('success', 'Selected sports deleted successfully!');
    }

    /**
     * Bulk Delete Para Games
     */
    public function paraBulkDelete(Request $request)
    {
        $request->validate([
            'sports' => 'required|array',
            'sports.*' => 'exists:sports,id'
        ]);

        $sports = Sport::whereIn('id', $request->sports)
            ->where('division', 'Paragames')
            ->get();

        foreach ($sports as $sport) {
            AllAwardResult::where('sport_id', $sport->id)->delete();
            IndividualAwardResult::where('sport_id', $sport->id)->delete();
            $sport->types()->delete();
            $sport->delete();
        }

        return redirect()->route('para-sports.index')
            ->with('success', 'Selected para games deleted successfully!');
    }
}
