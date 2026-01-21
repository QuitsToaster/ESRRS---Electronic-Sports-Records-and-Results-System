<?php

namespace App\Http\Controllers;

use App\Models\Official;
use App\Models\Sport;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficialController extends Controller
{
    public function index()
    {
        $officials = Official::orderBy('created_at', 'desc')->get();
        return view('officials.index', compact('officials'));
    }

    public function create()
    {
        $sports = Sport::orderBy('name')->get();
        return view('officials.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'sport' => 'required|string|max:255',
        ]);

        $official = Official::create([
            'name'  => $request->name,
            'sport' => $request->sport,
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Created Official',
            'model_type' => Official::class,
            'model_id'   => $official->id,
            'details'    => "Added official: {$official->name} ({$official->sport})",
        ]);

        return redirect()->route('officials.index')
            ->with('success', 'Official added successfully.');
    }

    public function edit(Official $official)
    {
        $sports = Sport::orderBy('name')->get();
        return view('officials.edit', compact('official', 'sports'));
    }

    public function update(Request $request, Official $official)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'sport' => 'required|string|max:255',
        ]);

        $oldName  = $official->name;
        $oldSport = $official->sport;

        $official->update([
            'name'  => $request->name,
            'sport' => $request->sport,
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Updated Official',
            'model_type' => Official::class,
            'model_id'   => $official->id,
            'details'    => "Updated official: {$oldName} ({$oldSport}) → {$official->name} ({$official->sport})",
        ]);

        return redirect()->route('officials.index')
            ->with('success', 'Official updated successfully.');
    }

    public function destroy(Official $official)
    {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Deleted Official',
            'model_type' => Official::class,
            'model_id'   => $official->id,
            'details'    => "Deleted official: {$official->name} ({$official->sport})",
        ]);

        $official->delete();

        return redirect()->route('officials.index')
            ->with('success', 'Official deleted successfully.');
    }
}
