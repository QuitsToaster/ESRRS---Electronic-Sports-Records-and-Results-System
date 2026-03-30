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

    $status = $roleName === 'Tournament Secretary'
        ? 'pending'
        : 'approved';

    $eventId = $request->event_id ?? Event::where('is_active', 1)->value('id');

    if (!$eventId || !Event::where('id', $eventId)->exists()) {
        return back()->with('error', 'Invalid or no active event found.');
    }

    $filePath = $request->hasFile('validation_file')
        ? $request->file('validation_file')->store('award-validations', 'public')
        : null;

    foreach ($request->input('individual_results', []) as $sportId => $types) {

    if (!is_array($types)) continue;

    foreach ($types as $typeId => $medals) {

        if (!is_array($medals)) continue;

        foreach ($medals as $medal => $data) {

            if (!is_array($data)) continue;

            $athleteId = $data['athlete'] ?? null;

            if (!$athleteId) continue;

            // Get athlete and prefix SDO
            $athlete = \App\Models\Athlete::find($athleteId);
            $sdo = $athlete && $athlete->school_division
                ? 'SDO - ' . $athlete->school_division
                : null;

            $existing = IndividualAwardResult::where([
                'event_id' => $eventId,
                'division' => $request->division,
                'category' => $request->category ?? null,
                'sport_id' => $sportId,
                'type_id'  => $typeId ?: null,
                'medal'    => $medal,
                'sdo'      => $sdo,
            ])->first();

            $oldValues = $existing ? $existing->toArray() : null;

            $award = IndividualAwardResult::updateOrCreate(
                [
                    'event_id' => $eventId,
                    'division' => $request->division,
                    'category' => $request->category ?? null,
                    'sport_id' => $sportId,
                    'type_id'  => $typeId ?: null,
                    'medal'    => $medal,
                    'sdo'      => $sdo,
                ],
                [
                    'athlete_id' => $athleteId,
                    'status' => $status,
                    'awarded_by' => $user->id,
                    'validation_file' => $filePath,
                ]
            );

            AuditLog::create([
                'model_type' => 'IndividualAwardResult',
                'model_id'   => $award->id,
                'user_id'    => $user->id,
                'action'     => $existing ? "Updated Individual Award" : "Created Individual Award",
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => json_encode($award->toArray()),
            ]);
        }
    }
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