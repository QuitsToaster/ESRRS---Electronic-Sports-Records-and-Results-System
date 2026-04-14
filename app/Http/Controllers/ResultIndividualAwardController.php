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
    $roleName = optional($user->role)->name;

    // ================= STATUS =================
    $status = $roleName === 'Tournament Secretary'
        ? 'pending'
        : 'approved';

    // ================= FILE =================
    $filePath = $request->hasFile('validation_file')
        ? $request->file('validation_file')->store('award-validations', 'public')
        : null;

    $results = $request->input('individual_results', []);

    if (!is_array($results)) {
        return back()->with('error', 'Invalid results format.');
    }

    // ================= EVENT =================
    $event = Event::where('is_active', 1)->first() ?? Event::first();

    if (!$event) {
        return back()->with('error', 'No event available.');
    }

    // ================= PROCESS =================
    foreach ($results as $sportId => $types) {

        if (!is_array($types)) continue;

        foreach ($types as $typeId => $medals) {

            if (!is_array($medals)) continue;

            foreach ($medals as $medalName => $entries) {

    if (!is_array($entries)) continue;

    foreach ($entries as $data) {

        if (!is_array($data)) continue;

        $athleteId = $data['athlete'] ?? null;
        $division  = $data['division'] ?? null;
        $category  = $data['category'] ?? null;

        if (!$athleteId) continue;

        // ================= GET ATHLETE =================
        $athlete = \App\Models\Athlete::find($athleteId);
        if (!$athlete) continue;

        // ================= AUTO SDO =================
        $sdo = $athlete->school_division
            ? 'SDO - ' . $athlete->school_division
            : null;

        // ================= CHECK EXISTING =================
        $existing = IndividualAwardResult::where([
            'event_id' => $event->id,
            'sport_id' => $sportId,
            'type_id'  => $typeId ?: null,
            'medal'    => $medalName,
            'athlete_id' => $athleteId, // ✅ IMPORTANT FOR MULTIPLE
        ])->first();

        $oldValues = $existing ? $existing->toArray() : null;

        // ================= SAVE =================
        $award = IndividualAwardResult::updateOrCreate(
            [
                'event_id' => $event->id,
                'sport_id' => $sportId,
                'type_id'  => $typeId ?: null,
                'medal'    => $medalName,
                'athlete_id' => $athleteId, // ✅ UNIQUE PER ATHLETE
            ],
            [
                'division' => $division,
                'category' => $category,
                'sdo' => $sdo,
                'status' => $status,
                'awarded_by' => $user->id,
                'validation_file' => $filePath,
            ]
        );

        // ================= AUDIT =================
        AuditLog::create([
            'model_type' => 'IndividualAwardResult',
            'model_id'   => $award->id,
            'user_id'    => $user->id,
            'action'     => $existing
                ? "Updated Individual Award | Sport: $sportId | Type: $typeId | Medal: $medalName"
                : "Created Individual Award | Sport: $sportId | Type: $typeId | Medal: $medalName",
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => json_encode($award->toArray()),
            'description' => json_encode([
                'performed_by' => $user->name,
                'role' => $roleName,
                'event_id' => $event->id,
                'sport_id' => $sportId,
                'type_id' => $typeId,
                'division' => $division,
                'category' => $category,
                'athlete_id' => $athleteId,
                'sdo' => $sdo,
                'medal' => $medalName,
                'status' => $status,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

    }
}
        }
    }

    // ================= EVENT STATUS =================
    if (in_array($roleName, ['Tournament Manager', 'Super Admin'])) {
        $event->update(['status' => 'Finished']);
    } else {
        $event->update(['status' => 'Ongoing']);
    }

    return back()->with('success', 'Individual awards saved successfully.');
}

    public function approveIndividual(Request $request)
    {
        $award = IndividualAwardResult::findOrFail($request->award_id);
        $old = $award->toArray();

        $award->update([
            'status' => 'approved',
            'revision_message' => null
        ]);

        $user = Auth::user();
        $roleName = optional($user->role)->name;

        AuditLog::create([
            'model_type' => 'IndividualAwardResult',
            'model_id' => $award->id,
            'user_id' => $user->id,
            'action' => sprintf(
                'Approved Individual Award | Athlete ID: %s | Sport ID: %s | Medal: %s | SDO: %s',
                $award->athlete_id,
                $award->sport_id,
                strtoupper($award->medal),
                $award->sdo
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode($award->toArray()),
            'description' => json_encode([
                'approved_by' => $user->name,
                'role' => $roleName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
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

        $user = Auth::user();

        AuditLog::create([
            'model_type' => 'IndividualAwardResult',
            'model_id' => $award->id,
            'user_id' => $user->id,
            'action' => sprintf(
                'Rejected Individual Award | Athlete ID: %s | Sport ID: %s | Medal: %s | Reason: %s',
                $award->athlete_id,
                $award->sport_id,
                strtoupper($award->medal),
                $request->message
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode($award->toArray()),
            'description' => json_encode([
                'rejected_by' => $user->name,
                'role' => optional($user->role)->name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        return back()->with('success', 'Award marked as needs revision.');
    }

    public function revise($id)
    {
        $award = IndividualAwardResult::findOrFail($id);
        $old = $award->toArray();
        $user = Auth::user();

        AuditLog::create([
            'model_type' => 'IndividualAwardResult',
            'model_id'   => $award->id,
            'user_id'    => $user->id,
            'action'     => sprintf(
                'Deleted Individual Award for Revision | Division: %s | Category: %s | Sport ID: %s | Type ID: %s | Athlete ID: %s | Medal: %s | SDO: %s',
                $award->division,
                $award->category ?? 'N/A',
                $award->sport_id,
                $award->type_id,
                $award->athlete_id,
                strtoupper($award->medal),
                $award->sdo
            ),
            'old_values' => json_encode($old),
            'new_values' => json_encode([]),
            'description' => json_encode([
                'deleted_by' => $user->name,
                'role' => optional($user->role)->name,
                'reason' => 'needs_revision',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        $award->delete();

        return back()->with('success', 'Award sent for revision successfully.');
    }

    public function updateFromRevision(Request $request)
{
    $award = IndividualAwardResult::findOrFail($request->award_id);

    $award->update([
        'athlete_id' => $request->athlete_id, // ✅ update athlete
        'medal' => $request->medal,
        'sdo' => 'SDO - ' . $request->sdo, // ✅ add prefix
        'status' => 'pending',
        'revision_message' => null,
    ]);

    return back()->with('success', 'Award resubmitted successfully.');
}
}