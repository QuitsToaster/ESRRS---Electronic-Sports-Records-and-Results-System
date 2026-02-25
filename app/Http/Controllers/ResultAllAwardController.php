<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AllAwardResult;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ResultAllAwardController extends Controller
{
   public function store(Request $request)
{
    $user = auth()->user();

    // ✅ REQUIRED VALIDATION
    if ($user->role->name === 'Tournament Secretary') {
        $request->validate([
            'selected_sdo' => 'required|string',
            'results' => 'required|array',
        ]);
    }

    $status = $user->role->name === 'Tournament Secretary'
        ? 'pending'
        : 'approved';

    $eventId = Event::where('is_active', 1)->value('id');

    if (!$eventId) {
        return back()->with('error', 'No active event found.');
    }

    // ✅ OPTIONAL FILE UPLOAD
    $filePath = null;
    if ($user->role->name === 'Tournament Secretary' && $request->hasFile('validation_file')) {
        $filePath = $request->file('validation_file')
            ->store('award-validations', 'public');
    }

    $selectedSdo = $request->selected_sdo;

    foreach ($request->results as $sportId => $types) {

        foreach ($types as $typeId => $sdos) {

            // __SDO__ placeholder loop
            foreach ($sdos as $fakeSdo => $medals) {

                foreach ($medals as $medal => $count) {

                    // ✅ ONLY ONE ALLOWED
                    if ((int) $count !== 1) {
                        continue;
                    }

                    $award = AllAwardResult::updateOrCreate(
                        [
                            'event_id' => $eventId,
                            'division' => $request->division,
                            'category' => $request->category ?: null,
                            'sport_id' => $sportId,
                            'type_id'  => $typeId,
                            'sdo'      => $selectedSdo,
                            'medal'    => $medal,
                        ],
                        [
                            'count' => 1,
                            'status' => $status,
                            'awarded_by' => $user->id,
                            'validation_file' => $filePath,
                        ]
                    );

                    // 🔹 Audit log for forwarding
                    if ($user->role->name === 'Tournament Secretary') {
                        AuditLog::create([
                            'model_type' => 'AllAwardResult',
                            'model_id'   => $award->id,
                            'user_id'    => $user->id,
                            'action'     => 'Forwarded All Award for Approval',
                            'old_values' => null,
                            'new_values' => json_encode($award->toArray()),
                        ]);
                    }
                }
            }
        }
    }

    return back()->with('success', 'Awards forwarded successfully.');
}

public function approveAll(Request $request)
{
    $award = AllAwardResult::findOrFail($request->award_id);
    $old = $award->toArray();

    $award->update([
        'status' => 'approved',
        'revision_message' => null
    ]);

    AuditLog::create([
        'model_type' => 'AllAwardResult',
        'model_id' => $award->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($old),
        'new_values' => json_encode($award->toArray()),
        'action' => 'Approved All Award',
    ]);

    return back()->with('success', 'Award approved.');
}

public function reject(Request $request)
{
    $award = AllAwardResult::findOrFail($request->award_id);
    $old = $award->toArray();

    $award->update([
        'status' => 'needs_revision',
        'revision_message' => $request->message,
    ]);

    AuditLog::create([
        'model_type' => 'AllAwardResult',
        'model_id' => $award->id,
        'user_id' => Auth::id(),
        'old_values' => json_encode($old),
        'new_values' => json_encode($award->toArray()),
        'action' => 'Rejected All Award',
    ]);

    return back()->with('success', 'Award marked as needs revision.');
}

public function revise($id)
{
    $award = AllAwardResult::findOrFail($id);

    // Log deletion
    AuditLog::create([
        'model_type' => 'AllAwardResult',
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
