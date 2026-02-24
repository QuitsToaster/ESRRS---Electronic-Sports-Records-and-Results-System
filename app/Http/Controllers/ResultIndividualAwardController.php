<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndividualAwardResult;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ResultIndividualAwardController extends Controller
{
    public function store(Request $request)
{
    $user = auth()->user();

    $status = $user->role->name === 'Tournament Secretary'
        ? 'pending'
        : 'approved';

    $eventId = Event::where('is_active', 1)->value('id');

    // ✅ OPTIONAL FILE UPLOAD
    $filePath = null;
    if ($user->role->name === 'Tournament Secretary' && $request->hasFile('validation_file')) {
        $filePath = $request->file('validation_file')
            ->store('award-validations', 'public');
    }

   foreach ($request->individual_results as $sportId => $types) {
    foreach ($types as $typeId => $medals) {
        foreach ($medals as $medal => $athleteId) {

            if (!$athleteId) continue;

            $award = IndividualAwardResult::updateOrCreate(
                [
                    'event_id' => $eventId,
                    'division' => $request->division,
                    'category' => $request->category,
                    'sport_id' => $sportId,
                    'type_id' => $typeId,
                    'medal' => $medal,
                ],
                [
                    'athlete_id' => $athleteId,
                    'sdo' => $user->division_name,
                    'status' => $status,
                    'awarded_by' => $user->id,
                    'validation_file' => $filePath,
                ]
            );

            // 🔹 Audit log for Tournament Secretary forwarding
            if ($user->role->name === 'Tournament Secretary') {
                AuditLog::create([
                    'model_type' => 'IndividualAwardResult',
                    'model_id' => $award->id,
                    'user_id' => $user->id,
                    'old_values' => json_encode([]),
                    'new_values' => json_encode($award->toArray()),
                    'action' => 'Forwarded Individual Award for Approval',
                ]);
            }
        }
    }
}

    return back()->with('success', 'Individual awards forwarded successfully.');
}


public function approveIndividual(Request $request)
{
    $award = IndividualAwardResult::findOrFail($request->award_id);
    $old = $award->toArray();

    $award->update([
        'status' => 'approved',
        'revision_message' => null
    ]);

    AuditLog::create([
        'model_type' => 'IndividualAwardResult',
        'model_id' => $award->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($old),
        'new_values' => json_encode($award->toArray()),
        'action' => 'Approved Individual Award',
    ]);

    return back()->with('success', 'Award approved.');
}

public function reject(Request $request)
{
    $award = IndividualAwardResult::findOrFail($request->award_id);
    $old = $award->toArray();

    $award->update([
        'status' => 'needs_revision',
        'revision_message' => $request->message,
    ]);

    AuditLog::create([
        'model_type' => 'IndividualAwardResult',
        'model_id' => $award->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($old),
        'new_values' => json_encode($award->toArray()),
        'action' => 'Rejected Individual Award',
    ]);

    return back()->with('success', 'Award marked as needs revision.');
}

public function revise($id)
{
    $award = IndividualAwardResult::findOrFail($id);

    // Log deletion
    AuditLog::create([
        'model_type' => 'IndividualAwardResult',
        'model_id' => $award->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($award->toArray()),
        'new_values' => json_encode([]),
        'action' => 'Award deleted for revision',
    ]);

    $award->delete();

    return back()->with('success', 'Award sent for revision successfully.');
}
}
